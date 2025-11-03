<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$perfil = new Perfil();
$request = $_SERVER["REQUEST_METHOD"];

$filtroParametros = new Datawall(
    "Parámetros Requeridos",
    Datawall::notFound,
    Datawall::all_match,
    [
        "El parametro 'nombre' es requerido" => fn($data) => isset($data['nombre']),
        "El parametro 'apellido' es requerido" => fn($data) => isset($data['apellido']),
        "El parametro 'correo' es requerido" => fn($data) => isset($data['correo']),
        "El parametro 'contraseña' es requerido" => fn($data) => isset($data['contraseña'])
    ],
    "Validación Parámetros",
    true
);

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
    
    $bodyInput = file_get_contents('php://input');
    $filtroBodyJSON->filter($bodyInput);
    $body = json_decode($bodyInput, true);
    
    $filtroParametros->filter($body);
    
    $nombre = $body["nombre"];
    $apellido = $body["apellido"];
    $contraseña = $body["contraseña"];
    $correo = $body["correo"];

    $register = $perfil->registrarUsuario($nombre, $apellido, $contraseña, $correo);

    echo json_encode($perfil->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>