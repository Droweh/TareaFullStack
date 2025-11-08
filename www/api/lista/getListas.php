<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$tableroObj = new Tablero();
$request = $_SERVER["REQUEST_METHOD"];

try {
    $filtroMetodoGet->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    $token = $_COOKIE["token"];
    $tableroId = $_GET["tablero"];

    $tablero = $tableroObj->getTablero($token, $tableroId);

    echo json_encode($tableroObj->returnSuccess($tablero["result"]));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>