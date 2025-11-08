<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$tablero = new Tablero();
$request = $_SERVER["REQUEST_METHOD"];

$filtroParametros = new Datawall(
    "Parámetros Opcionales",
    Datawall::notFound,
    Datawall::all_match,
    [
        "sin_id" => fn($data) => isset($data["id"])
    ],
    "Validación Parámetros Edición",
    true,
    "No se han encontrado los parametros necesarios para la accion solicitada"
);

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    $token = $_COOKIE["token"];

    $bodyInput = file_get_contents('php://input');
    $filtroBodyJSON->filter($bodyInput);
    $body = json_decode($bodyInput, true);
    
    $filtroParametros->filter($body);
    $tableroId = $body["id"];

    $tableros = $tablero->eliminarTablero($token, $tableroId);

    echo json_encode($tablero->returnSuccess($tableros));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>