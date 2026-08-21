<?php
// Ativa a exibição de erros para identificar o problema na hora
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../php/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome           = trim($_POST['nome'] ?? '');
    $usuario        = trim($_POST['usuario'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $usuario === '' || $senha === '' || $confirmarSenha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        // Verifica se o usuário já existe no banco
        $stmtCheck = $pdo->prepare("SELECT id FROM administradores WHERE usuario = ? LIMIT 1");
        $stmtCheck->execute([$usuario]);
        
        if ($stmtCheck->fetch()) {
            $erro = 'Este nome de usuário já está em uso.';
        } else {
            // Criptografa a senha com segurança
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmtInsert = $pdo->prepare("INSERT INTO administradores (nome, usuario, senha) VALUES (?, ?, ?)");
            
            if ($stmtInsert->execute([$nome, $usuario, $senhaHash])) {
                // Pega o ID do administrador recém-cadastrado
                $novoId = $pdo->lastInsertId();

                // Cria a sessão automaticamente para logar o usuário
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id']     = $novoId;
                $_SESSION['admin_nome']   = $nome;

                // Redireciona direto para a dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                $erro = 'Erro ao cadastrar administrador. Tente novamente.';
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
  <title>BarberPro | Cadastrar Administrador</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/login-admin.css">
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
        <h1><i class="fa-solid fa-scissors"></i> BarberPro</h1>
        <p>Cadastrar novo administrador</p>
      </div>

      <form class="auth-form" method="POST" action="cadastro-admin.php">
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

        <button type="submit" class="btn-auth">Cadastrar e Entrar</button>
      </form>

      <div class="auth-footer">
        Já possui uma conta?
        <button type="button" onclick="window.location.href='login-admin.php'">Fazer login</button>
      </div>
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