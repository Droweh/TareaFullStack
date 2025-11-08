<?php
include "datawall.php";
class Database {
    protected PDO $pdo;
    public array $success;
    protected Datawall $notFound;
    private Datawall $expiration;
    private Datawall $invalidToken;

    public function __construct(string $host, string $username, string $dbname, string $password, int $port) {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        date_default_timezone_set('America/Montevideo');

        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4",
            $username,
            $password,
            $options
        );

        $this->success = [
            "status" => "success",
            "result" => null
        ];

        $this->notFound = new Datawall(
            "Not Found",
            Datawall::notFound,
            Datawall::inclusive,
            [
                true => "La base de datos no devolvio ningun dato",
                "La base de datos no devolvio ningun dato" => fn($input): bool => !empty($input)
            ],
            "Data Base Output Filter",
            true
        );

        $this->invalidToken = new Datawall(
            "Invalid Token",
            Datawall::notFound,
            Datawall::inclusive,
            [
                true => "La base de datos no devolvio ningun dato",
                "La base de datos no devolvio ningun dato" => function ($input): bool {
                    setcookie("golden-token", "", time() - 3600);
                    
                    return !empty($input);
                }
            ],
            "Token Filter",
            true,
            "El usuario indexado no se encuentra en la base de datos"
        );

        $this->expiration = new Datawall(
            "Expiration Filter",
            Datawall::unauthorized,
            Datawall::exclusive,
            [
                "link_expirado" => function (array $input): bool {
                        if (new DateTime($input["expira"]) < new DateTime()) {
                            $accion = $this->pdo->prepare("delete from codigo_temporal where codigo = :codigo;");
                            $accion->execute(["codigo" => $input["codigo"]]);

                            return true;
                        } else {
                            return false;
                        }
                    }
            ],
            "Expiration Filter",
            true,
            "El link de invitacion ha alcanzado su fecha de expiracion"
        );
    }

    public function getUserCredentials(string $token): array {
        $solicitud = $this->pdo->prepare("select correo from sesion where token = :token;");
        $solicitud->execute(["token" => $token]);
        $userinfo = $this->invalidToken->filter($solicitud->fetch());

        $solicitud = $this->pdo->prepare("select * from usuario where correo = :correo;");
        $solicitud->execute(["correo" => $userinfo["correo"]]);

        $this->notFound->setOrigin("Database->getUserCredentials()");
        $usuario = $this->notFound->filter($solicitud->fetch());

        return $this->returnSuccess($usuario);
    }

    public function generarCodigoAcceso(): string {
        do {
            $codigo = bin2hex(random_bytes(32));
            $solicitud = $this->pdo->prepare("select codigo from codigo_temporal where codigo = :codigo");
            $solicitud->execute(["codigo" => $codigo]);
            $existe = $solicitud->fetch() !== false;
        } while ($existe);

        return $codigo;
    }
    
    public function registrarCodigoTemporal(string $codigo, string $correo, string $expira): array {
        $eliminar = $this->pdo->prepare("delete from codigo_temporal where correo = :correo");
        $eliminar->execute(["correo" => $correo]);

        $insertar = $this->pdo->prepare("insert into codigo_temporal(codigo, correo, expira) values (:codigo, :correo, :expira)");
        $insertar->execute(["codigo" => $codigo, "correo" => $correo, "expira" => $expira]);

        return $this->returnSuccess(null);
    }

    public function verificarCodigoAcceso(string $codigo): array {
        $solicitud = $this->pdo->prepare("select correo, expira from codigo_temporal where codigo = :codigo");
        $solicitud->execute(["codigo" => $codigo]);

        $this->notFound->setOrigin("Perfil->verificarCodigoAcceso()");
        $this->notFound->setErrMessage("Link de invitacion invalido");
        $solicitud = $this->notFound->filter($solicitud->fetch());
        $this->expiration->filter(["codigo" => $codigo, "expira" => $solicitud["expira"]]);

        return $this->returnSuccess($solicitud["correo"]);
    }

    public function returnSuccess(mixed $result): array {
        $this->success["result"] = $result;
        return $this->success;
    }

}