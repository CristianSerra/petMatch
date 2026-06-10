<?php

$host = "localhost";
$dbname = "petmatch";
$user = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die(json_encode([
        "success" => false,
        "message" => "Erro na conexão com banco",
        "error" => $e->getMessage()
    ]));
}