<?php
session_start();
require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/../php/funcoes.php';

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_email'])) {
    header('Location: login.php');
    exit;
}

$usuario_id    = $_SESSION['usuario_id'] ?? null;
$usuario_email = $_SESSION['usuario_email'] ?? '';
$usuario_nome  = $_SESSION['usuario_nome'] ?? 'Cliente';

$sucesso = null;
$erro    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrfValido($_POST['csrf_token'] ?? null)) {
    $erro = 'Sessão expirada. Atualize a página e tente novamente.';
  } else {
  $acao = $_POST['acao'] ?? '';

    if ($acao === 'atualizar_perfil') {
        $nome     = postTexto('nome');
        $email    = postEmail('email');
        $telefone = postTexto('telefone');

        if (!empty($nome) && !empty($email)) {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id");
                $stmt->execute([
                    ':nome'     => $nome,
                    ':email'    => $email,
                    ':telefone' => $telefone,
                    ':id'       => $usuario_id
                ]);

                $_SESSION['usuario_nome']     = $nome;
                $_SESSION['usuario_email']    = $email;
                $_SESSION['usuario_telefone'] = $telefone;

                $sucesso = "Dados atualizados com sucesso!";
            } catch (PDOException $e) {
              error_log($e->getMessage());
              $erro = "Erro ao atualizar dados. Tente novamente.";
            }
        } else {
            $erro = "Nome e E-mail são obrigatórios.";
        }
    }

    if ($acao === 'alterar_senha') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha  = $_POST['nova_senha'] ?? '';
        $confirmar   = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar)) {
            $erro = "Preencha todos os campos de senha.";
        } elseif ($nova_senha !== $confirmar) {
            $erro = "A nova senha e a confirmação não coincidem.";
        } else {
            $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $usuario_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha_atual, $user['senha'])) {
                $nova_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
                $stmtUpdate->execute([':senha' => $nova_hash, ':id' => $usuario_id]);
                $sucesso = "Senha alterada com sucesso!";
            } else {
                $erro = "Senha atual incorreta.";
            }
        }
    }

    if ($acao === 'cancelar_agendamento') {
        $agendamento_id = $_POST['agendamento_id'] ?? '';
        if (!empty($agendamento_id)) {
            try {
                $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = :id AND (usuario_id = :uid OR email = :email)");
                $stmt->execute([
                    ':id'    => $agendamento_id,
                    ':uid'   => $usuario_id,
                    ':email' => $usuario_email
                ]);
                header("Location: perfil.php?tab=agendamentos");
                exit;
            } catch (PDOException $e) {
              error_log($e->getMessage());
              $erro = "Erro ao cancelar agendamento. Tente novamente.";
            }
        }
    }
    }
}

$stmtUser = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id OR email = :email LIMIT 1");
$stmtUser->execute([':id' => $usuario_id, ':email' => $usuario_email]);
$dadosUsuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

$nomeVal     = $dadosUsuario['nome'] ?? $_SESSION['usuario_nome'] ?? '';
$emailVal    = $dadosUsuario['email'] ?? $_SESSION['usuario_email'] ?? '';
$telefoneVal = $dadosUsuario['telefone'] ?? $_SESSION['usuario_telefone'] ?? '';
$telefoneMascarado = mascararTelefone($telefoneVal);

$stmtAgendamentos = $pdo->prepare("
    SELECT * FROM agendamentos 
    WHERE (usuario_id = :uid OR email = :email) 
      AND (status != 'cancelado' OR status IS NULL)
    ORDER BY data_agendamento DESC, horario DESC
");
$stmtAgendamentos->execute([
    ':uid'   => $usuario_id,
    ':email' => $emailVal
]);
$agendamentos = $stmtAgendamentos->fetchAll(PDO::FETCH_ASSOC);

$preciosServicos = [
    'Corte Masculino'    => '45,00',
    'Corte de Cabelo'   => '45,00',
    'Barba Completa'    => '30,00',
    'Corte + Barba'     => '70,00',
    'Combo (Corte + Barba)' => '70,00',
    'Sobrancelha'       => '20,00',
    'Hidratação Capilar'=> '45,00',
    'Barboterapia'      => '50,00'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil - Barbearia</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/auth.css">
  <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>

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
        <a href="agendamentos.php">Agendar</a>
        <a href="../index.php#contato">Contato</a>
      </div>

      <a href="perfil.php" class="user-icon" aria-label="Perfil do Usuário">
        <i class="fa-solid fa-user"></i>
      </a>
    </nav>
  </header>

  <main class="profile-container">
    
    <h1 class="profile-title" id="user-greeting">Olá, <?= htmlspecialchars($nomeVal) ?></h1>

    <?php if ($sucesso): ?>
      <div class="alert-box success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= htmlspecialchars($sucesso) ?></span>
      </div>
    <?php endif; ?>

    <?php if ($erro): ?>
      <div class="alert-box error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($erro) ?></span>
      </div>
    <?php endif; ?>

    <div class="view-switch-wrapper">
      <div class="view-switch">
        <button type="button" class="switch-btn active" id="tab-perfil">Perfil</button>
        <button type="button" class="switch-btn" id="tab-agendamentos">Agendamentos</button>
      </div>
    </div>

  <section class="view-section" id="section-perfil">
      
    <div class="profile-card" id="card-dados-usuario">
        <form class="profile-form" id="form-perfil" method="POST" action="perfil.php">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
          <input type="hidden" name="acao" value="atualizar_perfil">

          <div class="form-group">
            <label for="prof-nome">Nome</label>
            <input type="text" id="prof-nome" name="nome" class="form-control" value="<?= htmlspecialchars($nomeVal) ?>" readonly required>
          </div>

          <div class="form-group">
            <label for="prof-email">Email</label>
            <input type="email" id="prof-email" name="email" class="form-control" value="<?= htmlspecialchars($emailVal) ?>" readonly required>
          </div>

          <div class="form-group">
            <label for="prof-telefone">Telefone</label>
            <div class="phone-field">
              <input type="hidden" id="prof-telefone-real" name="telefone" value="<?= htmlspecialchars($telefoneVal) ?>">
              <input type="tel" id="prof-telefone" name="telefone_visual" class="form-control phone-mask-input" value="<?= htmlspecialchars($telefoneMascarado) ?>" data-full-phone="<?= htmlspecialchars($telefoneVal) ?>" data-masked-phone="<?= htmlspecialchars($telefoneMascarado) ?>" readonly>
              <button type="button" class="toggle-phone" aria-label="Mostrar telefone">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="button" class="btn-auth" id="btn-editar-perfil" style="flex: 1;">
              <i class="fa-solid fa-pencil" aria-hidden="true"></i>
              <span>Editar</span>
            </button>
            <button type="button" class="btn-auth" id="btn-open-alterar-senha" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: #333;">Alterar senha</button>
          </div>
        </form>
      </div>

      <div class="profile-card hidden" id="card-alterar-senha">
        <h2 class="card-title-center">Alterar senha</h2>
        
        <form class="profile-form" id="form-alterar-senha" method="POST" action="perfil.php">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
          <input type="hidden" name="acao" value="alterar_senha">

          <div class="form-group">
            <label for="senha-atual">Senha Atual</label>
            <div class="input-wrapper">
              <input type="password" id="senha-atual" name="senha_atual" class="form-control" placeholder="Digite sua senha" required>
              <button type="button" class="toggle-password" aria-label="Alternar senha">
                <i class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="nova-senha">Nova senha</label>
            <div class="input-wrapper">
              <input type="password" id="nova-senha" name="nova_senha" class="form-control" placeholder="Digite sua senha" required>
              <button type="button" class="toggle-password" aria-label="Alternar senha">
                <i class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="confirmar-nova-senha">Confirme nova senha</label>
            <div class="input-wrapper">
              <input type="password" id="confirmar-nova-senha" name="confirmar_nova_senha" class="form-control" placeholder="Digite sua senha" required>
              <button type="button" class="toggle-password" aria-label="Alternar senha">
                <i class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-auth" style="flex: 1;">Confirmar</button>
            <button type="button" class="btn-auth" id="btn-cancel-alterar-senha" style="flex: 1; background: #666;">Voltar</button>
          </div>
        </form>
      </div>

    </section>

    <section class="view-section hidden" id="section-agendamentos">
      <div class="appointments-wrapper">
        <table class="appointments-table">
          <thead>
            <tr>
              <th>Serviço</th>
              <th>Dia</th>
              <th>Horário</th>
              <th>Barbeiro</th>
              <th>Valor</th>
              <th>Função</th>
            </tr>
          </thead>
          <tbody id="lista-agendamentos">
            <?php if (count($agendamentos) > 0): ?>
              <?php foreach ($agendamentos as $ag): ?>
                <?php 
                  $dataFmt = date('d/m/Y', strtotime($ag['data_agendamento']));
                  $horarioFmt = date('H:i', strtotime($ag['horario']));
                  $valor = $preciosServicos[$ag['servico']] ?? '45,00';
                ?>
                <tr>
                  <td><?= htmlspecialchars($ag['servico']) ?></td>
                  <td><?= $dataFmt ?></td>
                  <td><?= $horarioFmt ?></td>
                  <td><?= htmlspecialchars($ag['barbeiro'] ?? 'Não especificado') ?></td>
                  <td><?= $valor ?></td>
                  <td>
                    <form method="POST" action="perfil.php" onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
                      <input type="hidden" name="acao" value="cancelar_agendamento">
                      <input type="hidden" name="agendamento_id" value="<?= $ag['id'] ?>">
                      <button type="submit" class="btn-cancel">Cancelar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 2rem;">
                  Nenhum agendamento encontrado.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

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
          <label for="email-footer">Inscreva se</label>
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

  <script src="../js/main.js"></script>
<script src="../js/auth.js"></script>
</body>
</html>