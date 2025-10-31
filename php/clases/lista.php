<?php
include "database.php";

class Lista extends Database {

    public function __construct() {
        parent::__construct("database", "gbloomer", "gbloom_db", "goldenblosser", 3306);
    }

    public function crearLista(string $token): array {
        return $this->returnSuccess(null);
    }

    public function eliminarLista(string $token): array {
        return $this->returnSuccess(null);
    }

    public function editarLista(string $token): array {
        return $this->returnSuccess(null);
    }

    public function agregarTarea(string $token): array {
        return $this->returnSuccess(null);
    }

    public function quitarTarea(string $token): array {
        return $this->returnSuccess(null);
    }
    
    public function getLista(string $token): array {
        return $this->returnSuccess(null);
    }

}