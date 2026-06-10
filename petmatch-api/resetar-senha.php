<?php

header("Content-Type: application/json");

require_once "conexao.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$email = trim($data["email"] ?? "");
$dataNascimento = trim($data["dataNascimento"] ?? "");
$novaSenha = trim($data["novaSenha"] ?? "");

try {

    $sql = "
        SELECT id
        FROM users
        WHERE email = :email
        AND birth_date = :birth_date
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email,
        ":birth_date" => $dataNascimento
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {

        echo json_encode([
            "success" => false,
            "message" => "Data de nascimento inválida"
        ]);

        exit;
    }

    $senhaHash = password_hash(
        $novaSenha,
        PASSWORD_DEFAULT
    );

    $update = $pdo->prepare("
        UPDATE users
        SET senha = :senha
        WHERE id = :id
    ");

    $update->execute([
        ":senha" => $senhaHash,
        ":id" => $usuario["id"]
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Senha alterada com sucesso"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}