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
        "titulo" => fn($data) => isset($data["titulo"]),
        "nuevo_titulo" => fn($data) => isset($data["newTitulo"]),
        "sin_id" => fn($data) => isset($data["tableroId"])
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

    $tableroId = $body["tableroId"];
    $titulo = $body["titulo"];
    $newTitulo = $body["newTitulo"];

    $lista->editarLista($token, $titulo, $newTitulo, $tableroId);

    echo json_encode($lista->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>