<?php
include "../../php/complementos/includeAll.php";

$perfil = new Perfil();
if (isset($_COOKIE["token"])) {
    $token = $_COOKIE["token"];
    $perfil->cerrarSesion($token);

    echo json_encode($perfil->returnSuccess(null));
} else {
    echo json_encode(["status" => "notFound", "ErrMessage" => "El token de sesion requerido no se encuentra registrado"]);
}
?>