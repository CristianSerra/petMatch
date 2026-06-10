<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Usuário não autenticado."
    ]);

    exit;
}

header("Content-Type: application/json");

require_once "database.php";

try {

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    if(!$dados){

        echo json_encode([
            "success" => false,
            "message" => "Dados inválidos."
        ]);
        exit;
    }

    $usuario_id = $_SESSION["usuario_id"];

    $housing = trim($dados["housing"] ?? "");
    $children = trim($dados["children"] ?? "");
    $time = trim($dados["time"] ?? "");
    $lifestyle = trim($dados["lifestyle"] ?? "");

    if(
        empty($housing) ||
        empty($children) ||
        empty($time) ||
        empty($lifestyle)
    ){

        echo json_encode([
            "success" => false,
            "message" => "Todos os campos são obrigatórios."
        ]);
        exit;
    }

// Verifica se já existe perfil para o usuário
$sqlCheck = "
    SELECT id
    FROM perfil_usuario
    WHERE usuario_id = :usuario_id
    LIMIT 1
";

$stmtCheck = $pdo->prepare($sqlCheck);

$stmtCheck->execute([
    ":usuario_id" => $usuario_id
]);

$perfilExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($perfilExistente) {

    // UPDATE
    $sql = "
        UPDATE perfil_usuario
        SET
            tipo_moradia = :tipo_moradia,
            possui_criancas = :possui_criancas,
            tempo_disponivel = :tempo_disponivel,
            estilo_vida = :estilo_vida
        WHERE usuario_id = :usuario_id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":usuario_id" => $usuario_id,
        ":tipo_moradia" => $housing,
        ":possui_criancas" => $children,
        ":tempo_disponivel" => $time,
        ":estilo_vida" => $lifestyle
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Perfil atualizado com sucesso."
    ]);

} else {

    // INSERT
    $sql = "
        INSERT INTO perfil_usuario
        (
            usuario_id,
            tipo_moradia,
            possui_criancas,
            tempo_disponivel,
            estilo_vida
        )
        VALUES
        (
            :usuario_id,
            :tipo_moradia,
            :possui_criancas,
            :tempo_disponivel,
            :estilo_vida
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":usuario_id" => $usuario_id,
        ":tipo_moradia" => $housing,
        ":possui_criancas" => $children,
        ":tempo_disponivel" => $time,
        ":estilo_vida" => $lifestyle
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Perfil criado com sucesso."
    ]);
}

} catch(Exception $e){

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}