<?php
include "database.php";

class Tablero extends Database {

    public function __construct() {
        parent::__construct("database", "gbloomer", "gbloom_db", "goldenblosser", 3306);
    }

    public function crearTablero(string $token, string $titulo, string $tableroId): array {
        $credentials = $this->getUserCredentials($token)["result"];

        $this->notFound->setOrigin("Lista->crearLista");

        return $this->returnSuccess(null);
    }

    public function eliminarTablero(string $token): array {
        return $this->returnSuccess(null);
    }

    public function editarTablero(string $token): array {
        return $this->returnSuccess(null);
    }

    public function agregarColaborador(string $token): array {
        return $this->returnSuccess(null);
    }

    public function quitarColaborador(string $token): array {
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
    
    public function getColaboradores(string $token): array {
        return $this->returnSuccess(null);
    }

    public function getTablero(string $token): array {
        return $this->returnSuccess(null);
    }

    public function getCreador(string $token): array {
        return $this->returnSuccess(null);
    }

    public function generarLinkInvitacion(string $token): array {
        return $this->returnSuccess(null);
    }

    public function comprobarLink(string $token): array {
        return $this->returnSuccess(null);
    }

}