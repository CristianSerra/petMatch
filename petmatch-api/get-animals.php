<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once("database.php");

try {

    // =========================================
    // CONSULTA
    // =========================================

    $sql = "SELECT * FROM animals";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // =========================================
    // TRATAMENTO DOS DADOS
    // =========================================

    foreach($animals as &$animal){

        // inteiro
        $animal["matchScore"] =
            (int)$animal["matchScore"];

        // boolean
        $animal["isFavorite"] =
            (bool)$animal["isFavorite"];

        // badges
        $animal["badges"] =
            json_decode($animal["badges"], true);

        if(!$animal["badges"]){
            $animal["badges"] = new stdClass();
        }

        // arrays
        $animal["environment"] =
            json_decode($animal["environment"], true) ?? [];

        $animal["behaviors"] =
            json_decode($animal["behaviors"], true) ?? [];

        $animal["compatibilities"] =
            json_decode($animal["compatibilities"], true) ?? [];

        $animal["health"] =
            json_decode($animal["health"], true) ?? [];

        $animal["images"] =
            json_decode($animal["images"], true) ?? [];

        // contact
        $animal["contact"] = [
            "caretakerName" => $animal["caretakerName"],
            "phone" => $animal["phone"],
            "email" => $animal["email"]
        ];

        // remove colunas antigas
        unset($animal["caretakerName"]);
        unset($animal["phone"]);
        unset($animal["email"]);
    }

    // =========================================
    // RETORNO
    // =========================================

    echo json_encode([
        "success" => true,
        "total" => count($animals),
        "animals" => $animals
    ],
    JSON_UNESCAPED_UNICODE |
    JSON_PRETTY_PRINT);

} catch(PDOException $e){

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erro ao carregar animais",
        "error" => $e->getMessage()
    ]);
}