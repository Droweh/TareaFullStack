<?php
include "../../../php/complementos/includeAll.php";
include "../../../php/complementos/filters.php";

$perfil = new Perfil();

$filtroParametrosOpcionales = new Datawall(
    "Parámetros Opcionales",
    Datawall::notFound,
    Datawall::inclusive,
    [
        "al_menos_un_parametro" => function($data) {
            return isset($data["nombre"]) || isset($data["apellido"]) || (isset($data["contraseña"]) && isset($data["newcontraseña"]));
        }
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
    
    $filtroParametrosOpcionales->filter($body);
    
    $token = $_COOKIE["token"];
    $credentials = $perfil->getUserCredentials($token);
    
    $cambios = [];
    if (isset($body["nombre"])) $cambios["nombre"] = $body["nombre"];
    if (isset($body["apellido"])) $cambios["apellido"] = $body["apellido"];
    if (isset($body["contraseña"])) $cambios["contraseña"]["antigua"] = $body["contraseña"];
    if (isset($body["newcontraseña"])) $cambios["contraseña"]["nueva"] = $body["newcontraseña"];
    
    $perfil->editarUsuario($token, $cambios);
    
    echo json_encode($perfil->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>