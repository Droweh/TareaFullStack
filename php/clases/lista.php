<?php
class Lista extends Tablero {

    private Datawall $registrarTarea;

    public function __construct() {
        parent::__construct();

        $this->registrarTarea = new Datawall (
            "Filtro de registro de Tarea",
            Datawall::forbidden,
            Datawall::all_match,
            [
                "Titulo demasiado largo" => fn($input) => strlen($input["titulo"]) <= 32,
                "Fecha de inicio invalida" => fn($input) => ($d = DateTime::createFromFormat("Y-m-d", $input["fechaInicio"])) && $d->format("Y-m-d") === $input["fechaInicio"],
                "Fecha de fin invalida" => fn($input) => ($d = DateTime::createFromFormat("Y-m-d", $input["fechaFin"])) && $d->format("Y-m-d") === $input["fechaFin"]
            ],
            "Lista Gestion de Tareas",
            true,
            "Los datos ingresados son invalidos"
        );
    }

    public function crearLista(string $token, string $titulo, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        if (strlen($titulo) > 32) {
            throw new Exception(json_encode([
                "status" => Datawall::forbidden,
                "ErrMessage" => "El titulo es demasiado largo",
                "ErrDetails" => [],
                "origin" => "Lista->crearLista()"
            ]));
        }

        $insertar = $this->pdo->prepare("insert into lista(titulo, tableroId) values (:titulo, :tableroId);");
        $insertar->execute(["titulo" => $titulo, "tableroId" => $tableroId]);

        return $this->returnSuccess(null);
    }

    public function eliminarLista(string $token, string $titulo, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        $eliminar = $this->pdo->prepare("delete from tarea where lista = :lista and tableroId = :tableroId;");
        $eliminar->execute(["lista" => $titulo, "tableroId" => $tableroId]);

        $eliminar = $this->pdo->prepare("delete from lista where titulo = :titulo and tableroId = :tableroId;");
        $eliminar->execute(["titulo" => $titulo, "tableroId" => $tableroId]);

        return $this->returnSuccess(null);
    }

    public function editarLista(string $token, string $titulo, string $newtitulo, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        if (strlen($titulo) > 32) {
            throw new Exception(json_encode([
                "status" => Datawall::forbidden,
                "ErrMessage" => "El titulo es demasiado largo",
                "ErrDetails" => [],
                "origin" => "Lista->crearLista()"
            ]));
        }

        $actualizar = $this->pdo->prepare("update lista set titulo = :newtitulo where titulo = :titulo and tableroId = :tableroId;");
        $actualizar->execute(["titulo" => $titulo, "newtitulo" => $newtitulo, "tableroId" => $tableroId]);

        return $this->returnSuccess(null);
    }

    public function agregarTarea(string $token, string $titulo, string $fechaInicio, string $fechaFin, int $duracion, string $lista, int $tableroId): array {
        $this->isColaborador($token, $tableroId);
        $this->registrarTarea->filter(["titulo" => $titulo, "fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin]);

        $insertar = $this->pdo->prepare("insert into tarea(titulo, lista, tableroId, fechaInicio, fechaFin, duracion) values (:titulo, :lista, :tableroId, :fechaInicio, :fechaFin, :duracion);");
        $insertar->execute(["titulo" => $titulo, "lista" => $lista, "tableroId" => $tableroId, "fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin, "duracion" => $duracion]);

        return $this->returnSuccess(null);
    }

    public function quitarTarea(string $token, string $titulo, string $lista, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        $eliminar = $this->pdo->prepare("delete from tarea where titulo = :titulo and tableroId = :tableroId and lista = :lista;");
        $eliminar->execute(["titulo" => $titulo, "lista" => $lista, "tableroId" => $tableroId]);

        return $this->returnSuccess(null);
    }
    
    public function getLista(string $token, string $lista, int $tableroId): array {
        $this->isColaborador($token, $tableroId);

        $this->notFound->setOrigin("Lista->getLista()");
        $this->notFound->setErrMessage("No hay tareas en la lista solicitada");
        $solicitud = $this->pdo->prepare("select * from tarea where lista = :lista and tableroId = :tableroId;");
        $solicitud->execute(["lista" => $lista, "tableroId" => $tableroId]);
        $lista = $this->notFound->filter($solicitud->fetchAll());

        return $this->returnSuccess($lista);
    }

}