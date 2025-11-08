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
        "La solicitud debe contener el id del tablero" => fn($data) => isset($data["tableroId"]),
        "No se han ingresado los cambios" => fn($data) => 
        isset($data["newTitulo"]) || isset($data["fechaFin"]) || isset($data["fechaInicio"]) || isset($data["duracion"]) || isset($data["estado"])
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
    
    $titulo = $body["titulo"];
    $tableroId = $body["tableroId"];
    $listaId = $body["lista"];
    $token = $_COOKIE["token"];
    $cambios = [];
    if (isset($body["fechaFin"])) $cambios["fechaFin"] = $body["fechaFin"];
    if (isset($body["fechaInicio"])) $cambios["fechaInicio"] = $body["fechaInicio"];
    if (isset($body["duracion"])) $cambios["duracion"] = $body["duracion"];
    if (isset($body["estado"])) $cambios["estado"] = $body["estado"];
    if (isset($body["newTitulo"])) $cambios["titulo"] = $body["newTitulo"];

    $lista->editarTarea($token, $titulo, $listaId, $tableroId, $cambios);

    echo json_encode($lista->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>