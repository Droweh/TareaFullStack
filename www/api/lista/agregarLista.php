<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$lista = new Lista();
$request = $_SERVER["REQUEST_METHOD"];

$filtroParametros = new Datawall(
    "Parámetros Requeridos",
    Datawall::notFound,
    Datawall::all_match,
    [
        "El parametro 'nombre' es requerido" => fn($data) => isset($data['nombre']),
        "La solicitud debe contener el id del tablero" => fn($data) => isset($data["tableroId"])
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
    $tableroId = $body["tableroId"];
    $token = $_COOKIE["token"];

    $register = $lista->crearLista($token, $nombre, $tableroId);

    echo json_encode($lista->returnSuccess($register["result"]));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>