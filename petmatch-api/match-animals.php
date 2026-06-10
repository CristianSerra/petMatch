<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once("database.php");

// =====================================================
// RECEBE JSON DO FRONT
// =====================================================

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){

    echo json_encode([
        "success" => false,
        "message" => "Dados inválidos"
    ]);

    exit;
}

// =====================================================
// PERFIL USUÁRIO
// =====================================================

$housing   = $data["housing"] ?? "";
$children  = $data["children"] ?? "";
$time      = $data["time"] ?? "";
$lifestyle = $data["lifestyle"] ?? "";



// =====================================================
// BUSCA ANIMAIS
// =====================================================

$sql = "SELECT * FROM animals";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];

// =====================================================
// FUNÇÃO DE MATCH
// =====================================================

function calculateMatch($animal, $housing, $children, $time, $lifestyle){

    $score = 0;

    // =========================================
    // DECODIFICA JSONS
    // =========================================

    $environment = json_decode($animal["environment"], true) ?? [];
    $behaviors = json_decode($animal["behaviors"], true) ?? [];
    $compatibilities = json_decode($animal["compatibilities"], true) ?? [];

    // =========================================
    // MORADIA
    // =========================================

    if($housing === "Apartamento"){

        if(
            in_array("Casa Pequena", $environment) ||
            in_array("Apartamento", $environment)
        ){
            $score += 30;
        }
    }

    if($housing === "Casa"){

        if(
            in_array("Casa com Quintal", $environment)
        ){
            $score += 30;
        }
    }

    if($housing === "Chácara"){

        if(
            in_array("Área Rural", $environment) ||
            in_array("Sítio", $environment)
        ){
            $score += 30;
        }
    }

        // =========================================
    if($children === "Sim"){

        if(
            in_array("Sociável com pessoas", $compatibilities)
        ){
            $score += 25;
        }

        if(
            in_array("Sem Crianças", $compatibilities)
        ){
            $score -= 20;
        }
    }

    if($children === "Não"){
        $score += 10;
    }

    // =========================================
    // TEMPO DISPONÍVEL
    // =========================================

    if($time === "Pouco"){

        if(
            in_array("Independente", $behaviors) ||
            in_array("Pode ficar sozinho", $compatibilities)
        ){
            $score += 25;
        }
    }

    if($time === "Médio"){
        $score += 15;
    }

    if($time === "Muito"){

        if(
            in_array("Brincalhão", $behaviors) ||
            in_array("Carinhoso", $behaviors)
        ){
            $score += 25;
        }
    }

    // =========================================
    // ESTILO DE VIDA
    // =========================================

    if($lifestyle === "Tranquilo"){

        if(
            in_array("Quieto", $behaviors)
        ){
            $score += 20;
        }
    }

    if($lifestyle === "Moderado"){

        if(
            in_array("Curioso", $behaviors)
        ){
            $score += 20;
        }
    }

    if($lifestyle === "Ativo"){

        if(
            in_array("Brincalhão", $behaviors)
        ){
            $score += 25;
        }
    }

    if($lifestyle === "Aventureiro"){

        if(
            in_array("Alegre", $behaviors) ||
            in_array("Curioso", $behaviors)
        ){
            $score += 25;
        }
    }

    // =========================================
    // LIMITE SCORE
    // =========================================

    if($score < 0){
        $score = 0;
    }

    if($score > 100){
        $score = 100;
    }

    return $score;
}

// =====================================================
// PROCESSA MATCH
// =====================================================

foreach($animals as $animal){

    $match = calculateMatch(
        $animal,
        $housing,
        $children,
        $time,
        $lifestyle
    );

    $animal["match"] = $match;

    $animal["environment"] = json_decode($animal["environment"], true);
    $animal["behaviors"] = json_decode($animal["behaviors"], true);
    $animal["compatibilities"] = json_decode($animal["compatibilities"], true);
    $animal["health"] = json_decode($animal["health"], true);
    $animal["images"] = json_decode($animal["images"], true);

    $result[] = $animal;
}

// =====================================================
// ORDENA POR MATCH
// =====================================================

usort($result, function($a, $b){
    return $b["match"] <=> $a["match"];
});

// =====================================================
// RETORNO JSON
// =====================================================

echo json_encode([
    "success" => true,
    "userProfile" => [
        "housing" => $housing,
        "children" => $children,
        "time" => $time,
        "lifestyle" => $lifestyle
    ],
    "totalAnimals" => count($result),
    "animals" => $result
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);