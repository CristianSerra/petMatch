<?php

session_start();

header("Content-Type: application/json");

require_once "database.php";

try {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $login = trim($data["login"] ?? "");
    $senha = trim($data["senha"] ?? "");

    if (empty($login) || empty($senha)) {

        echo json_encode([
            "success" => false,
            "message" => "Login e senha são obrigatórios."
        ]);

        exit;
    }

    $sql = "
        SELECT
            id,
            login,
            email,
            senha
        FROM users
        WHERE login = :login
           OR email = :login
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":login" => $login
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {

        echo json_encode([
            "success" => false,
            "message" => "Usuário não encontrado."
        ]);

        exit;
    }

    /*
     * Se as senhas estiverem salvas em texto puro:
     */
    if ($usuario["senha"] !== $senha) {

        echo json_encode([
            "success" => false,
            "message" => "Senha inválida."
        ]);

        exit;
    }

    /*
     * Se estiver usando password_hash(),
     * substitua o bloco acima por:
     *
     * if (!password_verify($senha, $usuario["senha"])) {
     *     ...
     * }
     */

    $_SESSION["usuario_id"] = $usuario["id"];
    $_SESSION["usuario"] = $usuario["login"];
    $_SESSION["email"] = $usuario["email"];

    echo json_encode([
        "success" => true,
        "usuario_id" => $usuario["id"],
        "login" => $usuario["login"]
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}