<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        header("Location: ../pages/login.php?status=erro&msg=Preencha todos os campos.");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o usuário existir e a senha for válida
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];

            // Redireciona diretamente para a página principal do site
            header("Location: ../index.php");
            exit;
        } else {
            header("Location: ../pages/login.php?status=erro&msg=E-mail ou senha incorretos.");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../pages/login.php?status=erro&msg=Erro de autenticação.");
        exit;
    }
}