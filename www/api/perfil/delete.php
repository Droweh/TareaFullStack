<?php
include "../../php/complementos/includeAll.php";
include "../../php/complementos/filters.php";

$perfil = new Perfil();

try {
    $filtroMetodoPost->filter($_SERVER["REQUEST_METHOD"]);
        
    $token = isset($_COOKIE["token"]) ? $_COOKIE["token"] : "";
    $perfil->eliminarUsuario($token);
    
    echo json_encode($perfil->returnSuccess(null));
    
} catch (Exception $e) {
    echo $e->getMessage();
}
?>