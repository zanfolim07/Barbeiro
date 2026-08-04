<?php
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'msg' => 'Método não permitido.']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['status' => 'erro', 'msg' => 'E-mail inválido!']);
    exit;
}

try {
    $sql = "INSERT IGNORE INTO newsletter (email) VALUES (:email)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'sucesso', 'msg' => 'Inscrição realizada com sucesso!']);
    } else {
        echo json_encode(['status' => 'aviso', 'msg' => 'Este e-mail já está cadastrado!']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'msg' => 'Erro interno no servidor.']);
}