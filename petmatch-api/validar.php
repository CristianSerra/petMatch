<?php

session_start();

header("Content-Type: application/json");

if (
    isset($_SESSION["user_id"])
){

    echo json_encode([
        "logado" => true,
        "usuario" => [
            "id" => $_SESSION["user_id"],
            "login" => $_SESSION["user_login"]
        ]
    ]);

} else {

    echo json_encode([
        "logado" => false
    ]);

}