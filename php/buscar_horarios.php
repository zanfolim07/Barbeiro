<?php
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

$data = $_GET['data'] ?? '';
$barbeiro = $_GET['barbeiro'] ?? '';

try {
    if (!$data) {
        echo json_encode(['ocupados' => []]);
        exit;
    }

    $sql = "SELECT horario FROM agendamentos WHERE data_agendamento = :data AND (status != 'cancelado' OR status IS NULL)";
    $params = [':data' => $data];

    if ($barbeiro) {
        $sql .= " AND barbeiro = :barbeiro";
        $params[':barbeiro'] = $barbeiro;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['ocupados' => $stmt->fetchAll(PDO::FETCH_COLUMN)]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['ocupados' => [], 'status' => 'erro', 'msg' => 'Não foi possível consultar os horários.']);
}