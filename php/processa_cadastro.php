<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (!$nome || !$email || !$telefone || !$senha) {
        header("Location: ../pages/login.php?status=erro&msg=Preencha todos os campos.");
        exit;
    }

    if ($senha !== $confirmar_senha) {
        header("Location: ../pages/login.php?status=erro&msg=As senhas não coincidem.");
        exit;
    }

    try {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);

        if ($stmtCheck->fetch()) {
            header("Location: ../pages/login.php?status=erro&msg=Este e-mail já está cadastrado.");
            exit;
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
        header("Location: ../pages/login.php?status=erro&msg=Erro ao criar conta.");
        exit;
    }
}