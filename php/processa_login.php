<?php
session_start();
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

$email = postEmail('email');
$senha = $_POST['senha'] ?? '';

if (!$email || $senha === '') {
    redirecionarComStatus('../pages/login.php', 'Preencha todos os campos.');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        redirecionarComStatus('../pages/login.php', 'E-mail ou senha incorretos.');
    }

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    header('Location: ../index.php');
    exit;
} catch (PDOException $e) {
    redirecionarComStatus('../pages/login.php', 'Erro de autenticação.');
}

if (!csrfValido($_POST['csrf_token'] ?? null)) {
    redirecionarComStatus('../pages/login.php', 'Sessão expirada. Tente novamente.');
}