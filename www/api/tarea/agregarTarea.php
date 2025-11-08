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
        "El parametro 'titulo' es requerido" => fn($data) => isset($data['titulo']),
        "La solicitud debe contener el titulo de la lista" => fn($data) => isset($data["lista"]),
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
    
    $nombre = $body["titulo"];
    $tableroId = $body["tableroId"];
    $listaId = $body["lista"];
    $token = $_COOKIE["token"];

    $fechaInicio = new DateTime();
    $fechaFinal = new DateTime();
    $fechaFinal->modify("+1 day");

    $register = $lista->agregarTarea($token, $nombre, $fechaInicio->format("Y-m-d"), $fechaFinal->format("Y-m-d"), 1, $listaId, $tableroId);

    echo json_encode($lista->returnSuccess($register["result"]));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>