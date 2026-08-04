<?php
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

$data = $_GET['data'] ?? '';
$barbeiro = $_GET['barbeiro'] ?? '';

if (!empty($data)) {
    // Busca os horários agendados para aquele dia e barbeiro (desconsiderando cancelados)
    $sql = "SELECT horario FROM agendamentos WHERE data_agendamento = :data AND (status != 'cancelado' OR status IS NULL)";
    $params = [':data' => $data];

    if (!empty($barbeiro)) {
        $sql .= " AND barbeiro = :barbeiro";
        $params[':barbeiro'] = $barbeiro;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['ocupados' => $horariosOcupados]);
} else {
    echo json_encode(['ocupados' => []]);
}