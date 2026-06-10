<?php

session_start();
header("Content-Type: application/json");

require_once "database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$login = trim($data["login"] ?? "");
$senha = trim($data["senha"] ?? "");

if (!$login || !$senha) {

echo json_encode([
    "success" => false,
    "message" => "Preencha todos os campos"
]);

exit;

}

try {

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE login = :login
    OR email = :email
    LIMIT 1
");

$stmt->execute([
    ":login" => $login,
    ":email" => $login
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    echo json_encode([
        "success" => false,
        "message" => "Usuário não encontrado"
    ]);

    exit;
}

if (!password_verify($senha, $user["senha"])) {

    echo json_encode([
        "success" => false,
        "message" => "Senha inválida"
    ]);

    exit;
}

// SESSÃO
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_login"] = $user["login"];

echo json_encode([
    "success" => true,
    "message" => "Login realizado com sucesso",
    "user" => [
        "id" => $user["id"],
        "login" => $user["login"],
        "email" => $user["email"]
    ]
]);

} catch (Exception $e) {

echo json_encode([
    "success" => false,
    "message" => "Erro no servidor",
    "error" => $e->getMessage()
]);

}