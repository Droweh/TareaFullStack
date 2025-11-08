<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$tablero = new Tablero();
$request = $_SERVER["REQUEST_METHOD"];

$filtroParametros = new Datawall(
    "Parámetros Requeridos",
    Datawall::notFound,
    Datawall::all_match,
    [
        "El parametro 'nombre' es requerido" => fn($data) => isset($data['nombre'])
    ],
    "Validación Parámetros",
    true
);

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    

    $bodyInput = file_get_contents('php://input');
    $filtroBodyJSON->filter($bodyInput);
    $body = json_decode($bodyInput, true);
    
    $filtroParametros->filter($body);
    
    $nombre = $body["nombre"];
    $token = $_COOKIE["token"];

    $register = $tablero->crearTablero($token, $nombre, "descripcion por defecto");

    echo json_encode($tablero->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>