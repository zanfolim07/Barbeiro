<?php
session_start();
require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/../php/funcoes.php';

$erro = '';
$sucesso = $_GET['msg'] ?? '';

if (isset($_GET['action']) && $_GET['action'] === 'sair') {
    session_destroy();
    header('Location: login-admin.php?msg=Você+saiu+do+sistema!');
    exit;
}

if (isset($_SESSION['admin_logado'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrfValido($_POST['csrf_token'] ?? null)) {
    $erro = 'Sessão expirada. Atualize a página e tente novamente.';
  } else {
    $usuario = postTexto('usuario');
    $senha   = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM administradores WHERE usuario = ? LIMIT 1");
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($senha, $admin['senha'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_nome']   = $admin['nome'] ?? $admin['usuario'];

            if (!empty($_POST['lembrar'])) {
                setcookie('admin_lembrar', $admin['usuario'], time() + (30 * 24 * 60 * 60), '/');
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
      }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BarberPro | Login Administrativo</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/auth.css">
  <style>
    :root {
      --primary-color: #7b212d;
      --bg-light: #f1f1f1;
    }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body>

  <div class="auth-container">

    <?php if ($sucesso): ?>
      <div class="auth-alert sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if ($erro): ?>
      <div class="auth-alert erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="auth-card">
      <div class="auth-header">
        <h1><i class="fa-solid fa-scissors"></i> BarberPro</h1>
        <p>Acesse o painel administrativo</p>
      </div>

      <form class="auth-form" method="POST" action="login-admin.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
        <div class="form-group">
          <label for="usuario">Usuário</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="usuario"
              name="usuario"
              class="form-control"
              placeholder="Digite seu usuário"
              value="<?= isset($_COOKIE['admin_lembrar']) ? htmlspecialchars($_COOKIE['admin_lembrar']) : '' ?>"
              required
              autofocus
            >
          </div>
        </div>

        <div class="form-group">
          <label for="senha">Senha</label>
          <div class="input-wrapper">
            <input
              type="password"
              id="senha"
              name="senha"
              class="form-control"
              placeholder="Digite sua senha"
              required
            >
            <button type="button" class="toggle-password" aria-label="Mostrar senha">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="lembrar" name="lembrar" <?= isset($_COOKIE['admin_lembrar']) ? 'checked' : '' ?>>
          <label for="lembrar">Lembrar meu usuário neste dispositivo</label>
        </div>

        <button type="submit" class="btn-auth">Entrar</button>
      </form>

      <div class="auth-footer">
        Esqueceu a senha?
        <button type="button" onclick="alert('Entre em contato com o administrador do sistema.')">Recuperar acesso</button>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const toggleBtn = document.querySelector('.toggle-password');
      const senhaInput = document.getElementById('senha');

      toggleBtn.addEventListener('click', function () {
        const isPassword = senhaInput.type === 'password';
        senhaInput.type = isPassword ? 'text' : 'password';

        const icon = toggleBtn.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    });
  </script>
</body>
</html>