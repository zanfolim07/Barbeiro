<?php
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nome     = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $servico  = trim($_POST['servico'] ?? '');
    $barbeiro = trim($_POST['barbeiro'] ?? '');
    $dataHora = trim($_POST['data_hora'] ?? '');

    if (empty($nome) || empty($telefone) || empty($servico) || empty($barbeiro) || empty($dataHora)) {
        header('Location: ../index.php?status=erro&msg=Preencha todos os campos!');
        exit;
    }

    try {
        $sql = "INSERT INTO agendamentos (nome_cliente, telefone_cliente, servico, barbeiro, data_agendamento) 
                VALUES (:nome, :telefone, :servico, :barbeiro, :data_hora)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':servico', $servico);
        $stmt->bindValue(':barbeiro', $barbeiro);
        $stmt->bindValue(':data_hora', $dataHora);

        if ($stmt->execute()) {
            header('Location: ../index.php?status=sucesso');
            exit;
        } else {
            header('Location: ../index.php?status=erro&msg=Não foi possível realizar o agendamento.');
            exit;
        }

    } catch (PDOException $e) {
        header('Location: ../index.php?status=erro&msg=Erro interno no servidor.');
        exit;
    }

} else {
    header('Location: ../index.php');
    exit;
}
?>