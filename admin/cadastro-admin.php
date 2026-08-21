<?php
session_start();
require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/../php/funcoes.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrfValido($_POST['csrf_token'] ?? null)) {
    $erro = 'Sessão expirada. Atualize a página e tente novamente.';
  } else {
    $nome           = postTexto('nome');
    $usuario        = postTexto('usuario');
    $senha          = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $usuario === '' || $senha === '' || $confirmarSenha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM administradores WHERE usuario = ? LIMIT 1");
        $stmtCheck->execute([$usuario]);
        
        if ($stmtCheck->fetch()) {
            $erro = 'Este nome de usuário já está em uso.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmtInsert = $pdo->prepare("INSERT INTO administradores (nome, usuario, senha) VALUES (?, ?, ?)");
            
            if ($stmtInsert->execute([$nome, $usuario, $senhaHash])) {
                $novoId = $pdo->lastInsertId();

                session_regenerate_id(true);
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id']     = $novoId;
                $_SESSION['admin_nome']   = $nome;

                header('Location: dashboard.php');
                exit;
            } else {
                $erro = 'Erro ao cadastrar administrador. Tente novamente.';
            }
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
  <title>Barbearia | Cadastrar Administrador</title>
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

    <?php if ($erro): ?>
      <div class="auth-alert erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="auth-card">
      <div class="auth-header">
        <h1><i class="fa-solid fa-scissors"></i> Barbearia</h1>
        <p>Cadastrar novo administrador</p>
      </div>

      <form class="auth-form" method="POST" action="cadastro-admin.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
        <div class="form-group">
          <label for="nome">Nome completo</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="nome"
              name="nome"
              class="form-control"
              placeholder="Digite seu nome"
              value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
              required
              autofocus
            >
          </div>
        </div>

        <div class="form-group">
          <label for="usuario">Usuário</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="usuario"
              name="usuario"
              class="form-control"
              placeholder="Escolha um usuário"
              value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
              required
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
            <button type="button" class="toggle-password" aria-label="Mostrar senha" data-target="senha">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="confirmar_senha">Confirmar Senha</label>
          <div class="input-wrapper">
            <input
              type="password"
              id="confirmar_senha"
              name="confirmar_senha"
              class="form-control"
              placeholder="Confirme sua senha"
              required
            >
            <button type="button" class="toggle-password" aria-label="Mostrar senha" data-target="confirmar_senha">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-auth">Cadastrar</button>
      </form>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const toggleButtons = document.querySelectorAll('.toggle-password');

      toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
          const targetId = this.getAttribute('data-target');
          const input = document.getElementById(targetId);
          
          if (input) {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
          }
        });
      });
    });
  </script>
</body>
</html>