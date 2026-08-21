<?php
session_start();
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

$nome = postTexto('nome');
$email = postEmail('email');
$telefone = postTexto('telefone');
$senha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

if (!$nome || !$email || !$telefone || $senha === '') {
    redirecionarComStatus('../pages/login.php', 'Preencha todos os campos.');
}

if ($senha !== $confirmarSenha) {
    redirecionarComStatus('../pages/login.php', 'As senhas não coincidem.');
}

try {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);

        if ($stmtCheck->fetch()) {
            redirecionarComStatus('../pages/login.php', 'Este e-mail já está cadastrado.');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (:nome, :email, :telefone, :senha)");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':senha' => $senhaHash
        ]);

        $novoId = $pdo->lastInsertId();

        $_SESSION['usuario_id'] = $novoId;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;

        header("Location: ../index.php");
        exit;

} catch (PDOException $e) {
    redirecionarComStatus('../pages/login.php', 'Erro ao criar conta.');
}

if (!csrfValido($_POST['csrf_token'] ?? null)) {
    redirecionarComStatus('../pages/login.php', 'Sessão expirada. Tente novamente.');
}