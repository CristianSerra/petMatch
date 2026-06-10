<?php

header("Content-Type: application/json");

$data =
json_decode(
file_get_contents("php://input"),
true
);

$email =
$data["email"] ?? "";

$login =
$data["login"] ?? "";

$senha =
$data["senha"] ?? "";

$arquivo =
__DIR__ . "/data/usuarios.json";

if (!file_exists($arquivo)) {

file_put_contents(
$arquivo,
json_encode([])
);

}

$usuarios =
json_decode(
file_get_contents($arquivo),
true
);

if (!$usuarios){
    $usuarios = [];
}

$usuarios[] = [

"email" => $email,
"login" => $login,
"senha" => $senha

];

file_put_contents(

$arquivo,

json_encode(
$usuarios,
JSON_PRETTY_PRINT
)

);

echo json_encode([

"success" => true

]);