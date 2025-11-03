<?php
$filtroMetodoPost = new Datawall(
    "Método HTTP",
    Datawall::forbidden,
    Datawall::inclusive,
    [
        "Metodo invalido" => fn($input) => $input === "POST"
    ],
    "Validación Método HTTP",
    true,
    "El metodo utilizado para la solicitud es invalido. Por favor use POST"
);

$filtroMetodoGet = new Datawall(
    "Método HTTP",
    Datawall::forbidden,
    Datawall::inclusive,
    [
        "metodo_invalido" => fn($input) => $input === "GET"
    ],
    "Validación Método HTTP",
    true,
    "El metodo utilizado para la solicitud es invalido. Por favor use GET"
);

$filtroBodyJSON = new Datawall(
    "Body JSON", 
    Datawall::forbidden,
    Datawall::inclusive,
    [
        "json_invalido" => function($input) {
            try {
                json_decode($input, true);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    ],
    "Validación JSON",
    true, 
    "El formato de entrada no es valido"
);

$filtroSesionActiva = new Datawall(
    "Sesión Activa",
    Datawall::forbidden,
    Datawall::exclusive,
    [
        "Ya tienes acceso actual a esta cuenta" => function($input) {
            if (!isset($_COOKIE["token"])) {
                return false;
            }
            
            $token = $_COOKIE["token"];
            try {
                $credentials = $input["perfil"]->getUserCredentials($token);
                return $credentials["result"]["correo"] === $input['correo'];
            } catch (Exception $e) {
                return false;
            }
        }
    ],
    "Validación Sesión Activa",
    true,
    "Ya existe una sesion activa para el nombre usuario brindado"
);

$filtroTokenSesion = new Datawall(
    "Token Sesión",
    Datawall::notFound,
    Datawall::inclusive,
    [
        fn() => isset($_COOKIE["token"]) && !empty(trim($_COOKIE["token"]))
    ],
    "Validación Token Sesión",
    true,
    "El token de sesion requerido no se encuentra registrado"
);
?>