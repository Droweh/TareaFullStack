<?php

class Tablero extends Database {

    private Datawall $registroTablero;

    public function __construct() {
        parent::__construct("database", "tasker", "task_db", "taskertasking", 3306);
    
        $this->registroTablero = new Datawall(
            "Filtro de registro de tableros",
            Datawall::forbidden,
            Datawall::all_match,
            [
            "El nombre brindado es demasiado largo" => fn($input) => strlen($input['nombre'] ?? '') <= 32,
            "La descripcion brindada es demasiado larga" => fn($input) => strlen($input['descripcion'] ?? '') <= 32
            ],
            "Gestion de Tableros",
            true
        );
    }

    public function crearTablero(string $token, string $nombre, string $descripcion): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];
        $this->registroTablero->filter(["nombre" => $nombre, "descripcion" => $descripcion]);

        $this->notFound->setOrigin("Tablero->crearTablero()");
        $this->notFound->setErrMessage("El tablero ya existe");
        $this->notFound->setFilterType(Datawall::exclusive);
        $solicitud = $this->pdo->prepare("select nombre from tablero where nombre = :nombre and creador = :correo;");
        $solicitud->execute(["nombre" => $nombre, "correo" => $correo]);
        $this->notFound->filter($solicitud->fetch());
        $this->notFound->setFilterType(Datawall::inclusive);

        $insertar = $this->pdo->prepare("insert into tablero(nombre, creador, fechaCreacion, descripcion) values (:nombre, :creador, CURDATE(), :descripcion);");
        $insertar->execute(["nombre" => $nombre, "creador" => $correo, "descripcion" => $descripcion]);

        $solicitud = $this->pdo->prepare("select id from tablero where nombre = :nombre and creador = :correo;");
        $solicitud->execute(["nombre" => $nombre, "correo" => $correo]);
        $tableroId = $solicitud->fetch()["id"];

        $insertar = $this->pdo->prepare("insert into colaboraciones(tableroId, usuario) values (:tableroId, :colaborador);");
        $insertar->execute(["tableroId" => $tableroId, "colaborador" => $correo]);
        
        return $this->returnSuccess( null);
    }

    public function eliminarTablero(string $token, int $tableroId): array {
        $this->isCreador($token, $tableroId);

        $eliminar = $this->pdo->prepare("delete from tarea where tableroId = :tableroId;");
        $eliminar->execute(["tableroId" => $tableroId]);

        $eliminar = $this->pdo->prepare("delete from lista where tableroId = :tableroId;");
        $eliminar->execute(["tableroId" => $tableroId]);

        $eliminar = $this->pdo->prepare("delete from tablero where id = :tableroId;");
        $eliminar->execute(["tableroId" => $tableroId]);

        return $this->returnSuccess(null);
    }

    public function editarTablero(string $token, int $tableroId, array $cambios): array {
        $this->isCreador($token, $tableroId);
        $this->registroTablero->filter($cambios);

        if (isset($cambios["nombre"])) {
            $actualizar = $this->pdo->prepare("update tablero set nombre = :nombre where id = :tableroId;");
            $actualizar->execute(["nombre" => $cambios["nombre"], "tableroId" => $tableroId]);
        }

        if (isset($cambios["descripcion"])) {
            $actualizar = $this->pdo->prepare("update tablero set descripcion = :descripcion where id = :tableroId;");
            $actualizar->execute(["descripcion" => $cambios["descripcion"], "tableroId" => $tableroId]);
        }

        return $this->returnSuccess(null);
    }

    public function agregarColaborador(string $token, int $tableroId, string $colaborador): array {
        $this->isCreador($token, $tableroId);

        $this->notFound->setOrigin("Tablero->agregarColaborador()");
        $this->notFound->setErrMessage("El usuario brindado no existe");
        $solicitud = $this->pdo->prepare("select correo from usuario where correo = :correo;");
        $solicitud->execute(["correo" => $colaborador]);
        $this->notFound->filter($solicitud->fetch());

        $insertar = $this->pdo->prepare("insert into colaboraciones(tableroId, usuario) values (:tableroId, :colaborador);");
        $insertar->execute(["tableroId" => $tableroId, "colaborador" => $colaborador]);

        return $this->returnSuccess(null);
    }

    public function quitarColaborador(string $token, int $tableroId, string $colaborador): array {
        $this->isCreador($token, $tableroId);

        $eliminar = $this->pdo->prepare("delete from colaboraciones where tableroId = :tableroId and usuario = :colaborador;");
        $eliminar->execute(["tableroId" => $tableroId, "colaborador" => $colaborador]);

        return $this->returnSuccess(null);
    }

    public function isColaborador(string $token, int $tableroId): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];

        $this->notFound->setOrigin("Tablero->isColaborador()");
        $this->notFound->setErrMessage("El usuario brindado no es colaborador del tablero indicado");
        $solicitud = $this->pdo->prepare("select usuario from colaboraciones where usuario = :correo and tableroId = :tableroId");
        $solicitud->execute(["correo" => $correo, "tableroId" => $tableroId]);

        $this->notFound->filter($solicitud->fetch());

        return $this->returnSuccess(true);
    }

    public function isCreador(string $token, int $tableroId): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];

        $this->notFound->setOrigin("Tablero->isCreador()");
        $this->notFound->setErrMessage("El usuario brindado no es el creador del tablero indicado");
        $solicitud = $this->pdo->prepare(query: "select creador from tablero where creador = :correo and id = :tableroId");
        $solicitud->execute(["correo" => $correo, "tableroId" => $tableroId]);

        $this->notFound->filter($solicitud->fetch());

        return $this->returnSuccess(true);
    }
    
    public function getColaboradores(string $token, int $tableroId): array {
        $this->isColaborador($token, $tableroId);
        
        $solicitud = $this->pdo->prepare("select * from colaboraciones where tableroId = :tableroId;");
        $solicitud->execute(["tableroId" => $tableroId]);
        $colaboradores = $solicitud->fetchAll();

        return $this->returnSuccess($colaboradores);
    }

    public function getTablero(string $token, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        $solicitud = $this->pdo->prepare("select titulo from lista where tableroId = :tableroId;");
        $solicitud->execute(["tableroId" => $tableroId]);
        $listas = $solicitud->fetchAll();

        $output = [];

        if (!empty($listas)) {
            foreach ($listas as $lista) {
                $solicitud = $this->pdo->prepare("select * from tarea where lista = :lista and tableroId = :tableroId;");
                $solicitud->execute(["lista" => $lista["titulo"], "tableroId" => $tableroId]);
                $tareas = $solicitud->fetchAll();
                
                $output[] = ["lista" => $lista["titulo"], "tareas" => $tareas];
            }
        }
        
        return $this->returnSuccess($output);
    }

    public function getTableros(string $token): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];

        $solicitud = $this->pdo->prepare("select * from colaboraciones c join tablero t on c.tableroId = t.id where c.usuario = :correo;");
        $solicitud->execute(["correo" => $correo]);
        $listas = $solicitud->fetchAll();

        return $this->returnSuccess($listas);
    }

    public function getCreador(string $token, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        $solicitud = $this->pdo->prepare("select creador from tablero where id = :tableroId;");
        $solicitud->execute(["tableroId" => $tableroId]);
        $creador = $solicitud->fetch();

        return $this->returnSuccess($creador);
    }

    public function generarLinkInvitacion(string $token, $tableroId,  Perfil $perfil): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];
        $this->isCreador($token, $tableroId);

        $expira = new DateTime();
        $expira->modify("+15 minutes");
        $expira = $expira->format("Y-m-d H:m:s");
        $codigo = $perfil->generarCodigoAcceso();

        $this->registrarCodigoTemporal($codigo, $correo, $expira);

        $link = "https://" . $_SERVER['SERVER_NAME'] .  "api/tablero/joinInvite.php?codigo=" . $codigo . "&tablero=" . $tableroId;

        return $this->returnSuccess($link);
    }

    public function comprobarLink(string $token, string $codigo, int $tableroId): array {
        $correo = $this->getUserCredentials($token)["result"]["correo"];
        $creador = $this->verificarCodigoAcceso($codigo);

        $this->notFound->setOrigin("Tablero->comprobarLink()");
        $this->notFound->setErrMessage("El link de invitacion es invalido");
        $solicitud = $this->pdo->prepare("select creador from tablero where tableroId = :tableroId and creador = :creador;");
        $solicitud->execute(["tableroId" => $tableroId, "creador" => $creador]);
        $this->notFound->filter($solicitud->fetch());

        $insertar = $this->pdo->prepare("insert into colaboraciones(tableroId, usuario) values (:tableroId, :usuario);");
        $insertar->execute(["tableroId" => $tableroId, "usuario" => $correo]);

        return $this->returnSuccess(null);
    }

}