<?php

class Perfil extends Database {

    private Datawall $filtroRegistro;
    private Datawall $filtroAcceso;

    public function __construct() {
        parent::__construct("database", "tasker", "task_db", "taskertasking", 3306);
    
        $this->filtroRegistro = new Datawall(
            "Filtro de Registro",
            Datawall::forbidden,
            Datawall::all_match,
            [
            "El formato del correo electronico no es valido" => fn($input) => 
                filter_var($input['correo'] ?? 'test@test.test', FILTER_VALIDATE_EMAIL),
            
            "El nombre brindado es demasiado largo" => fn($input) => 
                strlen($input['nombre'] ?? '') <= 32,
            
            "El nombre brindado no puede contener espacios" => fn($input) => 
                strpos($input['nombre'] ?? '', ' ') === false,

            "El apellido brindado es demasiado largo" => fn($input) => 
                strlen($input['apellido'] ?? '') <= 32,
            
            "El apellido brindado no puede contener espacios" => fn($input) => 
                strpos($input['apellido'] ?? '', ' ') === false,
            
            "La contraseña brindada debe contener como minimo 8 caracteres" => fn($input) => 
                strlen($input['contraseña'] ?? '12345678') >= 8,
            
            "La contraseña brindada es demasiado larga" => fn($input) => 
                strlen($input['contraseña'] ?? '') <= 100,
            ],
            "Perfil->registrarUsuario()",
            true
        );

        $this->filtroAcceso = new Datawall(
            "Acceso a Cuenta",
            Datawall::forbidden,
            Datawall::all_match,
            [
            "La contraseña o el correo ingresados no son validos" => fn($input) => 
            !empty($input['usuario']) && password_verify($input['contraseña'], $input['usuario']['contraseña'])
            ],
            "Perfil->accederUsuario()",
            true,
            "Acceso Denegado"
        );
    }

    public function registrarUsuario(string $nombre, string $apellido, string $contraseña, string $correo): array {
        $solicitud = $this->pdo->prepare("select correo from usuario where correo = :correo;");
        $solicitud->execute(["correo" => $correo]);

        $this->notFound->setFilterType(Datawall::exclusive);
        $this->notFound->setOrigin("Perfil->registrarUsuario()");
        $this->notFound->setErrMessage("El correo brindado ya esta asociada a una cuenta existente");
        $this->notFound->filter($solicitud->fetch());
        
        $this->filtroRegistro->filter(["nombre" => $nombre, "apellido" => $apellido, "correo" => $correo, "contraseña" => $contraseña]);
        $this->notFound->setFilterType(Datawall::inclusive);

        $contraseña = password_hash($contraseña, PASSWORD_DEFAULT);

        $insertar = $this->pdo->prepare("insert into usuario(nombre, apellido, correo, contraseña) values (:nombre, :apellido, :correo, :contrasena);");
        $insertar->execute(["nombre" => $nombre, "apellido" => $apellido, "correo" => $correo, "contrasena" => $contraseña]);

        return $this->returnSuccess(null);
    }

    public function generarToken(): string {
        do {
            $token = bin2hex(random_bytes(32));
            $solicitud = $this->pdo->prepare("select token from sesion where token = :token");
            $solicitud->execute(["token" => $token]);
            $existe = $solicitud->fetch() !== false;
        } while ($existe);

        return $token;
    }

    public function accederUsuario(string $correo, string $contraseña): array {
        $this->filtroRegistro->setOrigin("Perfil->accederUsuario()");
        $this->filtroRegistro->filter(["correo" => $correo]);

        $solicitud = $this->pdo->prepare("select correo, contraseña from usuario where correo = :correo;");
        $solicitud->execute(["correo" => $correo]);
        $usuario = $solicitud->fetch();

        $this->notFound->setErrMessage("La contraseña o el usuario ingresados no son validos");
        $this->notFound->filter($usuario);
        $this->filtroAcceso->filter(["contraseña" => $contraseña, "usuario" => $usuario]);

        $token = $this->generarToken();
        $ahora = new DateTime();
        $ahora = $ahora->format("Y-m-d H:m:s");

        $insertar = $this->pdo->prepare("insert into sesion(token, correo, fecha) values (:token, :correo, :fecha);");
        $insertar->execute(["correo" => $correo, "token" => $token, "fecha" => $ahora]);

        return $this->returnSuccess($token);
    }

    public function eliminarUsuario(string $token): array {
        $credentials = $this->getUserCredentials($token);
        $correo = $credentials["result"]["correo"];

        $eliminar = $this->pdo->prepare("delete from sesion where correo = :correo");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from codigo_temporal where correo = :correo");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from tarea where tableroId in (select id from tablero where creador = :correo)");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from lista where tableroId in (select id from tablero where creador = :correo)");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from colaboraciones where usuario = :correo");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from tablero where creador = :correo");
        $eliminar->execute(["correo" => $correo]);

        $eliminar = $this->pdo->prepare("delete from usuario where correo = :correo");
        $eliminar->execute(["correo" => $correo]);

        return $this->returnSuccess(null);
    }

    public function cerrarSesion(string $token): array {
        $eliminar = $this->pdo->prepare("delete from sesion where token = :token");
        $eliminar->execute(["token" => $token]);

        setcookie("token", "", time() - 3600);

        return $this->returnSuccess(null);
    }

    public function editarUsuario(string $token, array $cambios): array {
        $credentials = $this->getUserCredentials($token)["result"];
        $correo = $credentials["correo"];
        $newname = $cambios["nombre"];
        $newapellido = $cambios["apellido"];
        $contraseña = $cambios["contraseña"]["antigua"];
        $newcontraseña = $cambios["contraseña"]["nueva"];

        $contraseña = password_hash($contraseña, PASSWORD_DEFAULT);

        $this->filtroRegistro->filter(["nombre" => $newname, "apellido" => $newapellido]);

        if (isset($cambios["contraseña"])) {
            $this->filtroAcceso->filter(["contraseña" => $contraseña, "usuario" => $credentials]);
        }

        // Actualiza la tabla usuario
        $actualizar = $this->pdo->prepare("update usuario set nombre = :newname, apellido = :newapellido where correo = :correo");
        $actualizar->execute(["newname" => $newname, "newapellido" => $newapellido, "correo" => $correo]);

        // Actualiza la contraseña
        if (isset($cambios["contraseña"])) {
            $actualizar = $this->pdo->prepare("update usuario set contraseña = :contrasena where correo = :correo");
            $actualizar->execute(["contrasena" => $newcontraseña, "correo" => $correo]);
        }

        return $this->returnSuccess(null);
    }
}