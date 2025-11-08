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
    $codigo = $_GET["codigo"];

    $tableros = $tablero->comprobarLink($token, $codigo, $tableroId);

    header("Location: https://" . $_SERVER['SERVER_NAME'] . "/Front/workspace/workspace.html?tablero=" . $tableroId);
} catch (Exception $e) {
    $errMessage = json_decode($e->getMessage(), true);
    echo $errMessage["ErrMessage"];
}

?>