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
        "al_menos_un_parametro" => function($data) {
            return isset($data["nombre"]) || isset($data["descripcion"]);
        },
        "sin_id" => fn($data) => isset($data["id"])
    ],
    "Validación Parámetros Edición",
    true,
    "No se han encontrado los parametros necesarios para la accion solicitada"
);

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    

    $bodyInput = file_get_contents('php://input');
    $filtroBodyJSON->filter($bodyInput);
    $body = json_decode($bodyInput, true);
    
    $filtroParametros->filter($body);
    
    $token = $_COOKIE["token"];

    $tableroId = $body["id"];
    $cambios = [];
    if (isset($body["nombre"])) $cambios["nombre"] = $body["nombre"];
    if (isset($body["descripcion"])) $cambios["descripcion"] = $body["descripcion"];

    $tablero->editarTablero($token, $tableroId, $cambios);

    echo json_encode($tablero->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>