<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexao.php";

$data = json_decode(file_get_contents("php://input"), true);

// Captura e limpa dados
$email = trim($data["email"] ?? "");
$login = trim($data["login"] ?? "");
$senha = trim($data["senha"] ?? "");
$birthDate = trim($data["birth_date"] ?? "");

// Validação básica
if (empty($email) || empty($login) || empty($senha)) {
    echo json_encode([
        "success" => false,
        "message" => "Preencha todos os campos obrigatórios."
    ]);
    exit;
}

try {

    // Verifica se email já existe (opcional, mas recomendado)
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([":email" => $email]);

    if ($check->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Este e-mail já está cadastrado."
        ]);
        exit;
    }

    // Inserção
    $stmt = $pdo->prepare("
        INSERT INTO users (login, email, senha, birth_date)
        VALUES (:login, :email, :senha, :birth_date)
    ");

    $stmt->execute([
        ":login" => $login,
        ":email" => $email,
        ":senha" => password_hash($senha, PASSWORD_DEFAULT),
        ":birth_date" => $birthDate
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Usuário cadastrado com sucesso."
    ]);

} catch (PDOException $e) {

    // Log interno do erro (não expõe para o usuário)
    error_log("Erro no cadastro: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Erro ao cadastrar usuário. Tente novamente mais tarde."
    ]);
}