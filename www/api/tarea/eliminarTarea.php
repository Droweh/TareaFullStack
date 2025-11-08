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
        "El parametro 'titulo' es requerido" => fn($data) => isset($data['titulo']),
        "La solicitud debe contener el titulo de la lista" => fn($data) => isset($data["lista"]),
        "La solicitud debe contener el id del tablero" => fn($data) => isset($data["tableroId"])
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
    $titulo = $body["titulo"];
    $tableroId = $body["tableroId"];
    $listaId = $body["lista"];
    $token = $_COOKIE["token"];

    $lista->quitarTarea($token, $titulo, $listaId, $tableroId);

    echo json_encode($lista->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>