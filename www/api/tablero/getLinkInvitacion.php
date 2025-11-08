<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$tablero = new Tablero();
$request = $_SERVER["REQUEST_METHOD"];

try {
    $filtroMetodoGet->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    $token = $_COOKIE["token"];
    $tableroId = $_GET["tablero"];

    $tableros = $tablero->generarLinkInvitacion($token, $tableroId, new Perfil());

    echo json_encode($tablero->returnSuccess($tableros["result"]));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>