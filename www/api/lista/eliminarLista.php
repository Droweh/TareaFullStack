<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$lista = new Lista();
$request = $_SERVER["REQUEST_METHOD"];

$filtroParametros = new Datawall(
    "Parámetros Opcionales",
    Datawall::notFound,
    Datawall::all_match,
    [
        "sin_id" => fn($data) => isset($data["tableroId"]),
        "sin_titulo" => fn($data) => isset($data["titulo"])
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
    $tableroId = $body["tableroId"];
    $titulo = $body["titulo"];

    $lista->eliminarLista($token, $titulo, $tableroId);

    echo json_encode($lista->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>