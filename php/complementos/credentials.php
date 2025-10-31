<?php
include "includeAll.php";

$perfil = new Perfil();
try {
    $token = isset($_COOKIE["token"]) ? $_COOKIE["token"] : "";
    $credentials = $perfil->getUserCredentials($token);

    echo json_encode($perfil->returnSuccess($credentials["result"]));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>