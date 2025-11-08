<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$tablero = new Tablero();
$request = $_SERVER["REQUEST_METHOD"];

try {
    $filtroMetodoGet->filter($_SERVER["REQUEST_METHOD"]);
    $filtroTokenSesion->filter(true);
    $token = $_COOKIE["token"];

    $tableros = $tablero->getTableros($token);

    echo json_encode($tablero->returnSuccess($tableros));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>