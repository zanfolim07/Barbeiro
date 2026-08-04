<?php
/* ==========================================================================
   PAINEL DE ADMINISTRAÇÃO - BARBEARIA (ADMIN/DASHBOARD.PHP)
   ========================================================================== */

// Importa a conexão com o banco de dados (que está na pasta php/)
require_once __DIR__ . '/../php/conexao.php';

// --- AÇÕES DO PAINEL (ATUALIZAR STATUS OU DELETAR) ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        if ($action === 'concluir') {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'concluido' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: dashboard.php?msg=Status+atualizado!');
            exit;
        } elseif ($action === 'deletar_agendamento') {
            $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: dashboard.php?msg=Agendamento+removido!');
            exit;
        } elseif ($action === 'deletar_newsletter') {
            $stmt = $pdo->prepare("DELETE FROM newsletter WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: dashboard.php?msg=Inscrito+removido!');
            exit;
        } elseif ($action === 'deletar_usuario') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: dashboard.php?msg=Usuário+removido!');
            exit;
        }
    }
}

// --- CONSULTAS PARA O DASHBOARD ---

// 1. Métricas Totais
$totalAgendamentos = $pdo->query("SELECT COUNT(*) FROM agendamentos")->fetchColumn();
$totalNewsletter   = $pdo->query("SELECT COUNT(*) FROM newsletter")->fetchColumn();
$totalUsuarios     = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$pendentes         = $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE status = 'pendente' OR status IS NULL OR status = ''")->fetchColumn();
$concluidos        = $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE status = 'concluido'")->fetchColumn();

// 2. Lista de Agendamentos (Mais recentes primeiro)
$sqlAgendamentos = "SELECT * FROM agendamentos ORDER BY id DESC LIMIT 15";
$agendamentos = $pdo->query($sqlAgendamentos)->fetchAll();

// 3. Lista de Inscritos na Newsletter
$sqlNewsletter = "SELECT * FROM newsletter ORDER BY data_inscricao DESC LIMIT 20";
$newsletter = $pdo->query($sqlNewsletter)->fetchAll();

// 4. Lista de Usuários Cadastrados
$sqlUsuarios = "SELECT * FROM usuarios ORDER BY id DESC LIMIT 20";
$usuarios = $pdo->query($sqlUsuarios)->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Administrativo | Barbearia</title>
  
  <!-- Fontes & Ícones (FontAwesome) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- CSS da Dashboard -->
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

  <div class="app-container">
    
    <!-- 1. SIDEBAR / MENU LATERAL -->
    <aside class="sidebar">
      <div>
        <div class="brand">
          <i class="fa-solid fa-scissors"></i>
          <span>Barbearia</span>
        </div>
        
        <ul class="nav-menu">
          <li class="nav-item active">
            <a href="dashboard.php">
              <i class="fa-solid fa-chart-line"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="../index.php" target="_blank">
              <i class="fa-solid fa-globe"></i>
              <span>Ver Site</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer">
        <a href="../index.php" class="btn-logout">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span>Sair</span>
        </a>
      </div>
    </aside>

    <!-- 2. CONTEÚDO PRINCIPAL -->
    <main class="main-wrapper">
      
      <!-- Topo da Página -->
      <header class="header-banner">
        <div class="header-title">
          <h1>Painel de Controle</h1>
          <p>Acompanhe agendamentos, clientes cadastrados e inscritos em tempo real.</p>
        </div>

        <div class="admin-profile">
          <div class="admin-avatar">A</div>
          <span class="admin-name">Administrador</span>
        </div>
      </header>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert-box success">
          <span><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($_GET['msg']) ?></span>
        </div>
      <?php endif; ?>

      <!-- Cards de Estatísticas -->
      <section class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon gold">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <div class="metric-data">
            <h3><?= $totalAgendamentos ?></h3>
            <p>Total Agendamentos</p>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon amber">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="metric-data">
            <h3><?= $pendentes ?></h3>
            <p>Pendentes</p>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon green">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div class="metric-data">
            <h3><?= $concluidos ?></h3>
            <p>Concluídos</p>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon gold">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="metric-data">
            <h3><?= $totalUsuarios ?></h3>
            <p>Usuários Cadastrados</p>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon blue">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div class="metric-data">
            <h3><?= $totalNewsletter ?></h3>
            <p>Inscritos Newsletter</p>
          </div>
        </div>
      </section>

      <!-- Tabela de Usuários Cadastrados -->
      <div class="content-card" style="margin-bottom: 2rem;">
        <div class="card-header">
          <h2><i class="fa-solid fa-users-gear"></i> Usuários Cadastrados</h2>
          <span class="badge-count"><?= count($usuarios) ?> cadastros</span>
        </div>

        <div class="table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome / Telefone</th>
                <th>E-mail</th>
                <th>Senha (Hash)</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($usuarios) > 0): ?>
                <?php foreach ($usuarios as $user): ?>
                  <tr>
                    <td><strong>#<?= $user['id'] ?></strong></td>
                    <td>
                      <div class="client-info">
                        <strong><?= htmlspecialchars($user['nome']) ?></strong>
                        <span><i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($user['telefone'] ?? 'Não informado') ?></span>
                      </div>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                      <code style="font-size:0.75rem; color:var(--text-muted); word-break: break-all;">
                        <?= htmlspecialchars($user['senha']) ?>
                      </code>
                    </td>
                    <td>
                      <a href="dashboard.php?action=deletar_usuario&id=<?= $user['id'] ?>" class="action-btn delete" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                        <i class="fa-solid fa-trash-can"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="empty-state">Nenhum usuário cadastrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grade Principal (Agendamentos + Newsletter) -->
      <div class="dashboard-grid">
        
        <!-- Tabela de Agendamentos -->
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fa-solid fa-list-check"></i> Próximos Agendamentos</h2>
            <span class="badge-count"><?= count($agendamentos) ?> registros</span>
          </div>

          <div class="table-responsive">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Cliente</th>
                  <th>Serviço / Barbeiro</th>
                  <th>Data & Hora</th>
                  <th>Obs.</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($agendamentos) > 0): ?>
                  <?php foreach ($agendamentos as $item): ?>
                    <tr>
                      <td>
                        <div class="client-info">
                          <strong><?= htmlspecialchars($item['nome']) ?></strong>
                          <span><i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($item['telefone']) ?></span>
                          <span style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($item['email']) ?></span>
                        </div>
                      </td>
                      <td>
                        <div class="client-info">
                          <strong><?= htmlspecialchars($item['servico']) ?></strong>
                          <span>Com: <?= htmlspecialchars($item['barbeiro']) ?></span>
                        </div>
                      </td>
                      <td>
                        <strong><?= date('d/m/Y', strtotime($item['data_agendamento'])) ?></strong><br>
                        <span style="color:var(--text-muted); font-size:0.85rem; font-weight: 600;"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($item['horario']) ?></span>
                      </td>
                      <td>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                          <?= !empty($item['observacao']) ? htmlspecialchars($item['observacao']) : '-' ?>
                        </span>
                      </td>
                      <td>
                        <?php 
                          $st = !empty($item['status']) ? strtolower($item['status']) : 'pendente';
                          $stClass = ($st === 'concluido') ? 'concluido' : (($st === 'cancelado') ? 'cancelado' : 'pendente');
                        ?>
                        <span class="status-badge <?= $stClass ?>">
                          <?= ucfirst($st) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (($item['status'] ?? 'pendente') !== 'concluido'): ?>
                          <a href="dashboard.php?action=concluir&id=<?= $item['id'] ?>" class="action-btn check" title="Marcar como Concluído">
                            <i class="fa-solid fa-check"></i>
                          </a>
                        <?php endif; ?>
                        <a href="dashboard.php?action=deletar_agendamento&id=<?= $item['id'] ?>" class="action-btn delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?');">
                          <i class="fa-solid fa-trash-can"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="empty-state">Nenhum agendamento encontrado.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Lista de Inscritos na Newsletter -->
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fa-solid fa-paper-plane"></i> Newsletter</h2>
            <span class="badge-count"><?= count($newsletter) ?> e-mails</span>
          </div>

          <div class="newsletter-list">
            <?php if (count($newsletter) > 0): ?>
              <?php foreach ($newsletter as $news): ?>
                <div class="newsletter-item">
                  <div>
                    <div class="newsletter-email"><?= htmlspecialchars($news['email']) ?></div>
                    <div class="newsletter-date">Inscrito em: <?= date('d/m/Y H:i', strtotime($news['data_inscricao'])) ?></div>
                  </div>
                  <a href="dashboard.php?action=deletar_newsletter&id=<?= $news['id'] ?>" class="action-btn delete" title="Excluir" onclick="return confirm('Excluir e-mail?');">
                    <i class="fa-solid fa-trash-can"></i>
                  </a>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Nenhum e-mail cadastrado ainda.</div>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </main>

  </div>

</body>
</html>