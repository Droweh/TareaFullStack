<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$perfil = new Perfil();

$filtroParametros = new Datawall(
    "Parámetros Requeridos",
    Datawall::notFound,
    Datawall::all_match,
    [
        "El parametro 'correo' es requerido" => fn($data) => isset($data['correo']),
        "El parametro 'contraseña' es requerido" => fn($data) => isset($data['contraseña'])
    ],
    "Validación Parámetros Login",
    true
);

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
    
    $bodyInput = file_get_contents('php://input');
    $filtroBodyJSON->filter($bodyInput);
    $body = json_decode($bodyInput, true);
    
    $filtroParametros->filter($body);
    
    $correo = trim($body["correo"]);
    $contraseña = $body["contraseña"];
    
    $filtroSesionActiva->filter(["correo" => $correo, "perfil" => $perfil]);
    
    if (isset($_COOKIE["token"])) {
        $perfil->cerrarSesion($_COOKIE["token"]);
    }
    
    $login = $perfil->accederUsuario($correo, $contraseña);
    $token = $login["result"];
    $expira = time() + 365 * 24 * 60 * 60;
    
    setcookie('token', $token, [
        'expires' => $expira,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    echo json_encode($perfil->returnSuccess(null));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>