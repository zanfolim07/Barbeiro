<?php
session_start();

// O usuário precisa estar logado/cadastrado para agendar
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$sucesso = null;
$erro = null;

// Processamento do formulário de agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../php/conexao.php';

    $usuario_id = $_SESSION['usuario_id'];
    $nome = trim($_POST['nome'] ?? '');
    $telefone_bruto = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $servico = trim($_POST['servico'] ?? '');
    $barbeiro = trim($_POST['barbeiro'] ?? '');
    $observacao = trim($_POST['observacao'] ?? '');
    $data_agendamento = trim($_POST['data_agendamento'] ?? '');
    $horario = trim($_POST['horario'] ?? '');

    // Remove TUDO que não for número do telefone
    $telefone = preg_replace('/\D/', '', $telefone_bruto);
    $tamanho_tel = strlen($telefone);

    // Validações rigorosas
    if (empty($nome) || empty($telefone) || empty($email) || empty($servico) || empty($data_agendamento) || empty($horario)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $erro = "Por favor, informe um endereço de e-mail completo e válido (ex: seuemail@gmail.com).";
    } elseif ($tamanho_tel < 10 || $tamanho_tel > 11) {
        $erro = "O número de telefone precisa ter a quantidade correta de dígitos (com DDD: 10 ou 11 números).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, nome, telefone, email, servico, barbeiro, observacao, data_agendamento, horario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $nome, $telefone, $email, $servico, $barbeiro, $observacao, $data_agendamento, $horario]);
            
            $sucesso = "Agendamento realizado com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro ao salvar agendamento: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendamento - Barbearia</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/agendamento.css?v=1.5">
</head>
<body>

  <!-- CABEÇALHO -->
  <header class="main-header">
    <nav class="navbar">
      <button class="hamburger-btn" aria-label="Abrir Menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <div class="nav-links">
        <a href="../index.php#inicio">Início</a>
        <a href="../index.php#sobre">Sobre</a>
        <a href="../index.php#servicos">Serviços</a>
        <a href="../index.php#equipe">Barbeiros</a>
        <a href="agendamentos.php" class="active">Agendar</a>
        <a href="../index.php#contato">Contato</a>
      </div>

      <div class="user-area">
        <?php if (isset($_SESSION['usuario_nome'])): ?>
          <a href="perfil.php" class="user-icon" title="Meu Perfil">
            <i class="fa-solid fa-user"></i>
          </a>
        <?php else: ?>
          <a href="login.php" class="user-icon" title="Entrar / Cadastrar">
            <i class="fa-regular fa-user"></i>
          </a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <main class="agendamento-container">
    <h1 class="page-title">Agende seu dia e seu horário</h1>

    <?php if (isset($sucesso)): ?>
      <div class="alert-box success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= htmlspecialchars($sucesso) ?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($erro)): ?>
      <div class="alert-box error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($erro) ?></span>
      </div>
    <?php endif; ?>

    <form class="agendamento-grid" id="form-agendamento" method="POST" action="agendamentos.php">
      
      <input type="hidden" name="data_agendamento" id="input-data" value="">
      <input type="hidden" name="horario" id="input-horario" value="">

      <!-- COLUNA DA ESQUERDA -->
      <div class="form-column">
        
        <div class="form-group">
          <label for="nome">Nome *</label>
          <input type="text" id="nome" name="nome" class="form-control" placeholder="EX: Guilherme Farias" value="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="telefone">Telefone (Com DDD) *</label>
          <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" value="<?= htmlspecialchars($_SESSION['usuario_telefone'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="email">E-mail *</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="EX: guilherme@gmail.com" value="<?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="servico">Serviço *</label>
          <select id="servico" name="servico" class="form-control" required>
            <option value="" disabled selected hidden>Selecione o serviço</option>
            <option value="Corte Masculino">Corte Masculino - R$ 45,00</option>
            <option value="Barba Completa">Barba Completa - R$ 30,00</option>
            <option value="Corte + Barba">Corte + Barba - R$ 70,00</option>
            <option value="Sobrancelha">Sobrancelha - R$ 20,00</option>
            <option value="Hidratação Capilar">Hidratação Capilar - R$ 45,00</option>
            <option value="Barboterapia">Barboterapia - R$ 50,00</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 1.5rem;">
          <div class="barbers-selection">
            <label class="barber-option">
              <input type="radio" name="barbeiro" value="João Silva" checked>
              <div class="barber-avatar">
                <img src="../img/joao-silva.jpg" alt="João Silva">
              </div>
              <span>João Silva</span>
            </label>

            <label class="barber-option">
              <input type="radio" name="barbeiro" value="Lucas Oliveira">
              <div class="barber-avatar">
                <img src="../img/Lucas-Oliveira.jpg" alt="Lucas Oliveira">
              </div>
              <span>Lucas Oliveira</span>
            </label>

            <label class="barber-option">
              <input type="radio" name="barbeiro" value="Rafael Costa">
              <div class="barber-avatar">
                <img src="../img/Rafael-costa.jpg" alt="Rafael Costa">
              </div>
              <span>Rafael Costa</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="observacao">Observação (Opcional)</label>
          <textarea id="observacao" name="observacao" class="form-control" placeholder="Alguma observação?"></textarea>
        </div>

      </div>

      <!-- COLUNA DA DIREITA -->
      <div class="schedule-column">
        
        <div class="calendar-wrapper">
          <div class="calendar-header">
            <button type="button" class="nav-btn" id="btn-prev-month"><i class="fa-solid fa-chevron-left"></i></button>
            <h3 id="calendar-month-year"></h3>
            <button type="button" class="nav-btn" id="btn-next-month"><i class="fa-solid fa-chevron-right"></i></button>
          </div>

          <div class="calendar-days-header">
            <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sab</span>
          </div>

          <div class="calendar-days-grid" id="calendar-days"></div>
        </div>

        <div class="time-selection">
          <h4>Escolha seu horário *</h4>
          
          <div class="time-grid">
            <button type="button" class="time-pill">09:00</button>
            <button type="button" class="time-pill">09:30</button>
            <button type="button" class="time-pill">10:00</button>
            <button type="button" class="time-pill">10:30</button>
            <button type="button" class="time-pill">11:00</button>
            <button type="button" class="time-pill">11:30</button>
            <button type="button" class="time-pill">12:00</button>
            <button type="button" class="time-pill">12:30</button>
            <button type="button" class="time-pill">13:00</button>
            <button type="button" class="time-pill">13:30</button>
            <button type="button" class="time-pill">14:00</button>
            <button type="button" class="time-pill">14:30</button>
            <button type="button" class="time-pill">15:00</button>
            <button type="button" class="time-pill">15:30</button>
            <button type="button" class="time-pill">16:00</button>
            <button type="button" class="time-pill">16:30</button>
            <button type="button" class="time-pill">17:00</button>
            <button type="button" class="time-pill">17:30</button>
            <button type="button" class="time-pill">18:00</button>
          </div>
        </div>

        <button type="submit" class="btn-confirm">Confirmar agendamento</button>

      </div>

    </form>
  </main>

  <footer class="footer-banner">
    <img src="../img/banner-footer.jpg" alt="Fundo Rodapé" class="banner-bg-img">
    <div class="overlay"></div>
    <div class="footer-content">
      <div class="footer-top">
        <div class="hours-block">
          <p><strong>Segunda a Sexta</strong> 09:00 - 20:00</p>
          <p><strong>Sábado</strong> 09:00 - 18:00</p>
        </div>
        <div class="newsletter-block">
          <label for="email-footer">Inscreva-se</label>
          <div class="input-group">
            <input type="email" id="email-footer" placeholder="Email:">
            <button class="btn-submit">Enviar</button>
          </div>
        </div>
      </div>
      <nav class="footer-nav">
        <a href="../index.php#inicio">Início</a>
        <a href="../index.php#sobre">Sobre</a>
        <a href="../index.php#servicos">Serviços</a>
        <a href="../index.php#equipe">Barbeiros</a>
        <a href="agendamentos.php">Agendar</a>
        <a href="../index.php#contato">Contato</a>
      </nav>
    </div>
  </footer>

  <script src="../js/agendamento.js"></script>
</body>
</html>