<?php

// Configuração do banco
$host = "localhost";
$db   = "petmatch";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Lê o JSON
$json = file_get_contents("animals.json");
$animals = json_decode($json, true);

if (!$animals) {
    die("Erro ao ler JSON");
}

// SQL de inserção
$sql = "
INSERT INTO animals (
    id, name, category, species, breed, age, sex, size,
    matchScore, isFavorite, badges,
    environment, behaviors, compatibilities, health,
    description, story, images,
    caretakerName, phone, email
) VALUES (
    :id, :name, :category, :species, :breed, :age, :sex, :size,
    :matchScore, :isFavorite, :badges,
    :environment, :behaviors, :compatibilities, :health,
    :description, :story, :images,
    :caretakerName, :phone, :email
)
";

$stmt = $pdo->prepare($sql);

foreach ($animals as $animal) {

    $stmt->execute([
        ":id" => $animal["id"],
        ":name" => $animal["name"],
        ":category" => $animal["category"],
        ":species" => $animal["species"],
        ":breed" => $animal["breed"],
        ":age" => $animal["age"],
        ":sex" => $animal["sex"],
        ":size" => $animal["size"],
        ":matchScore" => $animal["matchScore"],
        ":isFavorite" => $animal["isFavorite"] ? 1 : 0,

        ":badges" => json_encode($animal["badges"]),
        ":environment" => json_encode($animal["environment"]),
        ":behaviors" => json_encode($animal["behaviors"]),
        ":compatibilities" => json_encode($animal["compatibilities"]),
        ":health" => json_encode($animal["health"]),
        ":images" => json_encode($animal["images"]),

        ":description" => $animal["description"],
        ":story" => $animal["story"],

        ":caretakerName" => $animal["contact"]["caretakerName"],
        ":phone" => $animal["contact"]["phone"],
        ":email" => $animal["contact"]["email"],
    ]);
}

echo "Importação concluída com sucesso!";