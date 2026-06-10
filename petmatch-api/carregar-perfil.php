<?php
session_start();
if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Usuário não autenticado."
    ]);

    exit;
}

header('Content-Type: application/json');

require_once 'database.php';

try {
    $usuario_id = $_SESSION["user_id"];

    $sql = "
        SELECT
            tipo_moradia,
            possui_criancas,
            tempo_disponivel,
            estilo_vida,
            foto
        FROM perfil_usuario
        WHERE usuario_id = :usuario_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':usuario_id' => $usuario_id
    ]);

    $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$perfil) {

        echo json_encode([
            'success' => false,
            'message' => 'Perfil não encontrado'
        ]);

        exit;
    }

    echo json_encode([
        'success' => true,
        'profile' => [
            'housing'   => $perfil['tipo_moradia'],
            'children'  => $perfil['possui_criancas'],
            'time'      => $perfil['tempo_disponivel'],
            'lifestyle' => $perfil['estilo_vida'],
            'photo'     => $perfil['foto']
        ]
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}