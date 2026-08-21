<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login e Cadastro - BarberPro</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <main class="auth-container">
    
    <div id="auth-alert" class="auth-alert" style="display: none;"></div>

    <div class="auth-card" id="card-login">
      <div class="auth-header">
        <h1>Login</h1>
        <p>Bem vindo, preencha os dados corretamente.</p>
      </div>

      <form id="form-login" class="auth-form" action="../php/processa_login.php" method="POST">
        <div class="form-group">
          <label for="login-email">Email</label>
          <input type="email" id="login-email" name="email" class="form-control" placeholder="Digite seu email" required>
        </div>

        <div class="form-group">
          <label for="login-senha">Senha</label>
          <div class="input-wrapper">
            <input type="password" id="login-senha" name="senha" class="form-control" placeholder="Digite sua senha" required>
            <button type="button" class="toggle-password" aria-label="Alternar visualização da senha">
              <i class="fa-regular fa-eye-slash"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-auth">Entrar</button>
      </form>

      <div class="auth-footer">
        Ainda não possui uma conta? <button type="button" id="btn-to-cadastro">Cadastre-se</button>
      </div>
    </div>

    <div class="auth-card hidden" id="card-cadastro">
      <div class="auth-header">
        <h1>Criar conta</h1>
        <p>Bem vindo, preencha os dados corretamente.</p>
      </div>

      <form id="form-cadastro" class="auth-form" action="../php/processa_cadastro.php" method="POST">
        <div class="form-group">
          <label for="cad-nome">Nome</label>
          <input type="text" id="cad-nome" name="nome" class="form-control" placeholder="Digite seu nome" required>
        </div>

        <div class="form-group">
          <label for="cad-email">Email</label>
          <input type="email" id="cad-email" name="email" class="form-control" placeholder="Digite seu email" required>
        </div>

        <div class="form-group">
          <label for="cad-telefone">Telefone</label>
          <input type="tel" id="cad-telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" required>
        </div>

        <div class="form-group">
          <label for="cad-senha">Senha</label>
          <div class="input-wrapper">
            <input type="password" id="cad-senha" name="senha" class="form-control" placeholder="Digite sua senha" required>
            <button type="button" class="toggle-password" aria-label="Alternar visualização da senha">
              <i class="fa-regular fa-eye-slash"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="cad-confirmar-senha">Confirmar senha</label>
          <div class="input-wrapper">
            <input type="password" id="cad-confirmar-senha" name="confirmar_senha" class="form-control" placeholder="Confirme sua senha" required>
            <button type="button" class="toggle-password" aria-label="Alternar visualização da senha">
              <i class="fa-regular fa-eye-slash"></i>
            </button>
          </div>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="termos" name="termos" required>
          <label for="termos">Li e aceito os Termos de Uso e a Política de Privacidade.</label>
        </div>

        <button type="submit" class="btn-auth">Cadastrar</button>
      </form>

      <div class="auth-footer">
        Já possui uma conta? <button type="button" id="btn-to-login">Entrar</button>
      </div>
    </div>

  </main>

  <script src="../js/main.js"></script>
  <script src="../js/auth.js"></script> 
</body>
</html>