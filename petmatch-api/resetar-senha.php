<?php

header("Content-Type: application/json");

$data =
json_decode(
file_get_contents("php://input"),
true
);

$email =
strtolower(
trim($data["email"] ?? "")
);

$novaSenha =
trim($data["novaSenha"] ?? "");

$arquivo =
__DIR__ . "/data/usuarios.json";

if (!file_exists($arquivo)) {

echo json_encode([

"success" => false,
"message" => "Arquivo não encontrado"

]);

exit;

}

$usuarios =
json_decode(
file_get_contents($arquivo),
true
);

if (!is_array($usuarios)) {

echo json_encode([

"success" => false,
"message" => "JSON inválido"

]);

exit;

}

$usuarioEncontrado = false;

for ($i = 0; $i < count($usuarios); $i++) {

$emailSalvo =
strtolower(
trim($usuarios[$i]["email"])
);

if ($emailSalvo === $email) {

$usuarios[$i]["senha"] = $novaSenha;

$usuarioEncontrado = true;

break;

}

}

if (!$usuarioEncontrado) {

echo json_encode([

"success" => false,
"message" => "Usuário não encontrado",
"emailRecebido" => $email

]);

exit;

}

file_put_contents(

$arquivo,

json_encode(
$usuarios,
JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
)

);

echo json_encode([

"success" => true,
"message" => "Senha alterada"

]);