<?php
// Inclui o arquivo de conexão que está na mesma pasta
require_once __DIR__ . '/conexao.php';

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitização e captura dos campos vindos do formulário
    $nome     = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $servico  = trim($_POST['servico'] ?? '');
    $barbeiro = trim($_POST['barbeiro'] ?? '');
    $dataHora = trim($_POST['data_hora'] ?? '');

    // Validação simples: garante que nenhum campo veio vazio
    if (empty($nome) || empty($telefone) || empty($servico) || empty($barbeiro) || empty($dataHora)) {
        header('Location: ../index.php?status=erro&msg=Preencha todos os campos!');
        exit;
    }

    try {
        // SQL com Prepared Statements para segurança contra SQL Injection
        $sql = "INSERT INTO agendamentos (nome_cliente, telefone_cliente, servico, barbeiro, data_agendamento) 
                VALUES (:nome, :telefone, :servico, :barbeiro, :data_hora)";
        
        $stmt = $pdo->prepare($sql);
        
        // Associa os valores aos parâmetros da Query
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':servico', $servico);
        $stmt->bindValue(':barbeiro', $barbeiro);
        $stmt->bindValue(':data_hora', $dataHora);

        // Executa a gravação
        if ($stmt->execute()) {
            // Redireciona de volta com parâmetro de sucesso
            header('Location: ../index.php?status=sucesso');
            exit;
        } else {
            header('Location: ../index.php?status=erro&msg=Não foi possível realizar o agendamento.');
            exit;
        }

    } catch (PDOException $e) {
        // Em ambiente de produção, evite exibir $e->getMessage() diretamente
        header('Location: ../index.php?status=erro&msg=Erro interno no servidor.');
        exit;
    }

} else {
    // Se a página for acessada diretamente sem POST, redireciona para a Home
    header('Location: ../index.php');
    exit;
}
?>