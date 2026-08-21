<?php
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login-admin.php');
    exit;
}

require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/../php/funcoes.php';

$pagina = $_GET['pagina'] ?? 'inicio';
$csrfToken = tokenCsrf();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if (!csrfValido($_GET['csrf_token'] ?? null)) {
        header('Location: ?pagina=' . urlencode($pagina) . '&msg=Ação inválida.');
        exit;
    }
    $id = intval($_GET['id'] ?? 0);
    
    if ($action === 'deletar_usuario' && $id > 0) {
        $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina . '&msg=Usuário+removido!'); exit;
    } elseif ($action === 'deletar_newsletter' && $id > 0) {
        $pdo->prepare("DELETE FROM newsletter WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina . '&msg=Inscrito+removido!'); exit;
    } elseif ($action === 'deletar_agendamento' && $id > 0) {
        $pdo->prepare("DELETE FROM agendamentos WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina . '&msg=Agendamento+removido!'); exit;
    } elseif ($action === 'deletar_admin' && $id > 0) {
        if ($id == $_SESSION['admin_id']) {
            header('Location: ?pagina=' . $pagina . '&msg=Você+não+pode+se+apagar!'); exit;
        }
        $pdo->prepare("DELETE FROM administradores WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina . '&msg=Administrador+removido!'); exit;
    } elseif ($action === 'iniciar_corte' && $id > 0) {
        $pdo->prepare("UPDATE agendamentos SET status = 'em_andamento' WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina); exit;
    } elseif ($action === 'encerrar_corte' && $id > 0) {
        $pdo->prepare("UPDATE agendamentos SET status = 'concluido' WHERE id = ?")->execute([$id]);
        header('Location: ?pagina=' . $pagina); exit;
    } elseif ($action === 'enviar_aviso' && $id > 0) {
        $tipoAviso = $_GET['tipo'] ?? 'confirmar';
        
        $stmtAg = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
        $stmtAg->execute([$id]);
        $agendamento = $stmtAg->fetch();

        if ($agendamento && !empty($agendamento['telefone'])) {
            $telefone = preg_replace('/[^0-9]/', '', $agendamento['telefone']);
            $horario = $agendamento['horario'] ?? 'horário agendado';
            
            if ($tipoAviso === 'confirmar') {
                $mensagem = "Olá, {$agendamento['nome']}! Passando para confirmar seu horário às {$horario} na Barbearia. Tudo certo?";
            } else {
                $mensagem = "Olá, {$agendamento['nome']}! Informamos que tivemos um pequeno atraso na barbearia e entraremos em atendimento em instantes. Agradecemos a compreensão!";
            }
            
            $urlWhatsapp = "https://api.whatsapp.com/send?phone=55{$telefone}&text=" . urlencode($mensagem);
            header("Location: " . $urlWhatsapp);
            exit;
        } else {
            header('Location: ?pagina=' . $pagina . '&msg=Telefone+não+encontrado!');
            exit;
        }
    } elseif ($action === 'remarcar_cliente') {
        $telefoneRaw = $_GET['tel'] ?? '';
        $nomeCliente = $_GET['nome'] ?? 'Cliente';
        
        if (!empty($telefoneRaw)) {
            $telefone = preg_replace('/[^0-9]/', '', $telefoneRaw);
            $mensagem = "Olá, {$nomeCliente}! Notamos que já faz um tempinho desde seu último corte aqui na Barbearia. Que tal remarcar seu próximo atendimento?";
            
            $urlWhatsapp = "https://api.whatsapp.com/send?phone=55{$telefone}&text=" . urlencode($mensagem);
            header("Location: " . $urlWhatsapp);
            exit;
        } else {
            header('Location: ?pagina=' . $pagina . '&msg=Telefone+inválido!');
            exit;
        }
    }
}

$totalAgendamentos = $pdo->query("SELECT COUNT(*) FROM agendamentos")->fetchColumn();
$concluidos        = $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE status = 'concluido'")->fetchColumn();
$totalUsuarios     = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

$usuarios   = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC LIMIT 5")->fetchAll();
$newsletter = $pdo->query("SELECT * FROM newsletter ORDER BY data_inscricao DESC LIMIT 5")->fetchAll();
$fidelidade = $pdo->query("SELECT nome, COUNT(*) as total_cortes FROM agendamentos WHERE status = 'concluido' GROUP BY nome ORDER BY total_cortes DESC LIMIT 3")->fetchAll();
$admins     = $pdo->query("SELECT * FROM administradores ORDER BY id DESC")->fetchAll();

$barbeirosMap = [
    'joao_silva' => [
        'nome' => 'João Silva',
        'cargo' => 'Barbeiro profissional'
    ],
    'lucas_oliveira' => [
        'nome' => 'Lucas Oliveira',
        'cargo' => 'Barbeiro profissional'
    ],
    'rafael_costa' => [
        'nome' => 'Rafael Costa',
        'cargo' => 'Barbeiro profissional'
    ]
];

if (array_key_exists($pagina, $barbeirosMap)) {
    $barbeiroAtual = $barbeirosMap[$pagina];
    
    $stmtAgenda = $pdo->prepare("SELECT * FROM agendamentos WHERE barbeiro = ? AND (status IS NULL OR status != 'concluido') ORDER BY horario ASC");
    $stmtAgenda->execute([$barbeiroAtual['nome']]);
    $agendamentosBarbeiro = $stmtAgenda->fetchAll();

    $minhaAgendaHoje = count($agendamentosBarbeiro);
    $proximoClienteNome = !empty($agendamentosBarbeiro) ? $agendamentosBarbeiro[0]['nome'] : 'Nenhum';
    
    $stmtFin = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE barbeiro = ? AND status = 'concluido'");
    $stmtFin->execute([$barbeiroAtual['nome']]);
    $finalizadosHoje = $stmtFin->fetchColumn();

    $stmtFidelidadeBarbeiro = $pdo->prepare("SELECT nome, telefone, email, COUNT(*) as total_cortes FROM agendamentos WHERE barbeiro = ? AND status = 'concluido' GROUP BY nome, telefone, email ORDER BY total_cortes DESC LIMIT 5");
    $stmtFidelidadeBarbeiro->execute([$barbeiroAtual['nome']]);
    $clientesFrequentes = $stmtFidelidadeBarbeiro->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
    <title>Barbearia | Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/geral.css">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

  <aside class="sidebar">
    <div>
        <div class="brand"><i class="fa-solid fa-scissors"></i> Barbearia</div>
        <ul class="nav-menu">
            <li class="<?= $pagina === 'inicio' ? 'active' : '' ?>">
                <a href="?pagina=inicio"><i class="fa-solid fa-house"></i> Inicio</a>
            </li>
            <li class="<?= $pagina === 'joao_silva' ? 'active' : '' ?>">
                <a href="?pagina=joao_silva"><i class="fa-solid fa-user"></i> João Silva</a>
            </li>
            <li class="<?= $pagina === 'lucas_oliveira' ? 'active' : '' ?>">
                <a href="?pagina=lucas_oliveira"><i class="fa-solid fa-user"></i> Lucas Oliveira</a>
            </li>
            <li class="<?= $pagina === 'rafael_costa' ? 'active' : '' ?>">
                <a href="?pagina=rafael_costa"><i class="fa-solid fa-user"></i> Rafael Costa</a>
            </li>
            <li class="<?= $pagina === 'profissionais' ? 'active' : '' ?>">
                <a href="?pagina=profissionais"><i class="fa-solid fa-user-gear"></i> Cadastrar Profissional</a>
            </li>
        </ul>
    </div>
    <div class="footer-nav">
        <p style="margin: 8px 0;"><a href="../index.php" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-globe"></i> Site</a></p>
        <p style="margin: 8px 0;"><a href="login-admin.php?action=sair" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></p>
    </div>
  </aside>

  <main class="main">
    <?php if ($pagina === 'inicio'): ?>
        <div class="header">
            <h1>Inicio</h1> 
            <strong>Painel Administrativo Geral</strong>
        </div>
        
        <section class="grid-metrics">
            <div class="card">
                <h3><i class="fa-solid fa-calendar-check"></i> Total de agendamentos</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0 0 0;"><?=$totalAgendamentos?></p>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-circle-check"></i> Cortes concluídos</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0 0 0;"><?=$concluidos?></p>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-users"></i> Usuários cadastrados</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0 0 0;"><?=$totalUsuarios?></p>
            </div>
        </section>

        <div class="card">
            <h2>Clientes cadastrados</h2>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>Telefone</th><th>Email</th><th>Data</th><th>Ação</th></tr></thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><?=$u['id']?></td>
                        <td><?=$u['nome']?></td>
                        <td><?=$u['telefone']?></td>
                        <td><?=$u['email']?></td>
                        <td><?=$u['data_cadastro'] ?? '-'?></td>
                        <td><a href="?pagina=inicio&action=deletar_usuario&id=<?=$u['id']?>&csrf_token=<?=$csrfToken?>" class="action-link"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-bottom-grid">
            <div class="card">
                <h2>Lista newsletter</h2>
                <table>
                    <thead><tr><th>ID</th><th>Email</th><th>Data</th><th>Ação</th></tr></thead>
                    <tbody>
                        <?php foreach($newsletter as $n): ?>
                        <tr>
                            <td><?=$n['id']?></td>
                            <td><?=$n['email']?></td>
                            <td><?=$n['data_inscricao'] ?? '-'?></td>
                            <td><a href="?pagina=inicio&action=deletar_newsletter&id=<?=$n['id']?>&csrf_token=<?=$csrfToken?>" class="action-link"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h2>Fidelidade</h2>
                <?php foreach($fidelidade as $f): ?>
                <div style="margin-bottom:15px;">
                    <p style="margin: 0 0 5px 0;"><strong><?=$f['nome']?></strong> (<?=$f['total_cortes']?>/12)</p>
                    <div class="progress-bar"><div class="progress-fill" style="width: <?=min(($f['total_cortes']/12)*100, 100)?>%;"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($pagina === 'profissionais'): ?>
        <div class="header">
            <h1>Profissionais / Administradores</h1>
            <a href="cadastro-admin.php" class="btn-action" style="padding: 10px 20px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-user-plus"></i> Cadastrar Novo</a>
        </div>

        <div class="card">
            <h2><i class="fa-solid fa-user-shield"></i> Administradores Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Dia Cadastrado</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admins as $adm): ?>
                    <tr>
                        <td><?=$adm['id']?></td>
                        <td><?=$adm['nome']?></td>
                        <td><?=$adm['usuario']?></td>
                        <td><?=$adm['data_cadastro'] ?? '-'?></td>
                        <td>
                            <?php if ($adm['id'] != $_SESSION['admin_id']): ?>
                                <a href="?pagina=profissionais&action=deletar_admin&id=<?=$adm['id']?>&csrf_token=<?=$csrfToken?>" class="action-link" onclick="return confirm('Deseja realmente apagar este administrador?');"><i class="fa-solid fa-trash"></i></a>
                            <?php else: ?>
                                <span style="color: #999; font-size: 0.85rem;" title="Você está logado nesta conta">(Atual)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif (array_key_exists($pagina, $barbeirosMap)): ?>
        <?php 
        $b = $barbeirosMap[$pagina]; 
        
        $diasSemana = ['Sunday' => 'Domingo', 'Monday' => 'Segunda-feira', 'Tuesday' => 'Terça-feira', 'Wednesday' => 'Quarta-feira', 'Thursday' => 'Quinta-feira', 'Friday' => 'Sexta-feira', 'Saturday' => 'Sábado'];
        $mesesAno = ['January' => 'janeiro', 'February' => 'fevereiro', 'March' => 'março', 'April' => 'abril', 'May' => 'maio', 'June' => 'junho', 'July' => 'julho', 'August' => 'agosto', 'September' => 'setembro', 'October' => 'outubro', 'November' => 'novembro', 'December' => 'dezembro'];
        
        $diaIngles = date('l');
        $mesIngles = date('F');
        $dataFormatada = $diasSemana[$diaIngles] . ', ' . date('d') . ' de ' . $mesesAno[$mesIngles];
        ?>
        <div class="header">
            <h1>Bem vindo <?=$b['nome']?></h1>
            <div class="date-time">
                <?= ucfirst($dataFormatada) ?><br>
                <span><?= date('H:i') ?></span>
            </div>
        </div>

        <div style="margin-bottom: 10px; font-weight: 600; color: #444;">Resumo da Agenda do dia</div>
        <section class="grid-metrics">
            <div class="card">
                <h3 style="font-size: 0.95rem; color: #555; margin: 0;">Minha Agenda Hoje</h3>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 8px 0; color: #111;"><?=$minhaAgendaHoje?></p>
                <span style="font-size: 0.85rem; color: #666;">Agendamentos de hoje</span>
            </div>
            <div class="card">
                <h3 style="font-size: 0.95rem; color: #555; margin: 0;">Proximo cliente</h3>
                <p style="font-size: 2.2rem; font-weight: bold; margin: 8px 0; color: #111;"><?=$proximoClienteNome?></p>
                <span style="font-size: 0.85rem; color: #666;">Proximo cliente para atendimento</span>
            </div>
            <div class="card">
                <h3 style="font-size: 0.95rem; color: #555; margin: 0;">Finalizados Hoje</h3>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 8px 0; color: #111;"><?=$finalizadosHoje?></p>
                <span style="font-size: 0.85rem; color: #666;">Total de serviços finalizados</span>
            </div>
        </section>

        <div class="card">
            <h2 style="font-size: 1.1rem; margin-top: 0;">Agendamentos Diarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>Horario</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Serviço</th>
                        <th>Ação</th>
                        <th>Avisos</th>
                        <th>Função</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($agendamentosBarbeiro)): ?>
                        <?php foreach($agendamentosBarbeiro as $ag): ?>
                        <tr>
                            <td><?=$ag['horario'] ?? '--:--'?></td>
                            <td><?=$ag['nome']?></td>
                            <td><?=$ag['telefone'] ?? '-'?></td>
                            <td><?=$ag['servico'] ?? 'Corte'?></td>
                            <td>
                                <?php if (($ag['status'] ?? '') === 'em_andamento'): ?>
                                    <a href="?pagina=<?=$pagina?>&action=encerrar_corte&id=<?=$ag['id']?>&csrf_token=<?=$csrfToken?>" class="btn-action encerrar">Encerrar</a>
                                <?php else: ?>
                                    <a href="?pagina=<?=$pagina?>&action=iniciar_corte&id=<?=$ag['id']?>&csrf_token=<?=$csrfToken?>" class="btn-action">Iniciar</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown-container">
                                    <button type="button" class="btn-enviar toggle-dropdown">Enviar</button>
                                    <div class="dropdown-menu">
                                        <a href="?pagina=<?=$pagina?>&action=enviar_aviso&tipo=confirmar&id=<?=$ag['id']?>&csrf_token=<?=$csrfToken?>" target="_blank">Confirmar horário</a>
                                        <a href="?pagina=<?=$pagina?>&action=enviar_aviso&tipo=atraso&id=<?=$ag['id']?>&csrf_token=<?=$csrfToken?>" target="_blank">Avisar atraso</a>
                                    </div>
                                </div>
                            </td>
                            <td><a href="?pagina=<?=$pagina?>&action=deletar_agendamento&id=<?=$ag['id']?>&csrf_token=<?=$csrfToken?>" class="action-link"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: #777;">Nenhum agendamento pendente para este profissional.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 style="font-size: 1.1rem; margin-top: 0;">Clientes frequentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Avisos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientesFrequentes)): ?>
                        <?php $ranking = 1; foreach($clientesFrequentes as $cf): ?>
                        <tr>
                            <td><div class="ranking-circle"><?=$ranking++?></div></td>
                            <td><?=$cf['nome']?></td>
                            <td><?=$cf['telefone'] ?? '-'?></td>
                            <td><?=$cf['email'] ?? '-'?></td>
                            <td>
                                <div class="dropdown-container">
                                    <button type="button" class="btn-enviar toggle-dropdown">Enviar</button>
                                    <div class="dropdown-menu">
                                        <a href="?pagina=<?=$pagina?>&action=remarcar_cliente&tel=<?=urlencode($cf['telefone'])?>&nome=<?=urlencode($cf['nome'])?>&csrf_token=<?=$csrfToken?>" target="_blank">Remarcar</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: #777;">Nenhum cliente frequente cadastrado ainda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
  </main>

  <script>
    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('.toggle-dropdown');
        
        if (toggle) {
            const container = toggle.closest('.dropdown-container');
            const menu = container.querySelector('.dropdown-menu');
            
            document.querySelectorAll('.dropdown-container').forEach(d => {
                if (d !== container) {
                    d.classList.remove('active');
                    const m = d.querySelector('.dropdown-menu');
                    if (m) m.style.display = 'none';
                }
            });
            
            container.classList.toggle('active');
            
            if (container.classList.contains('active')) {
                const rect = toggle.getBoundingClientRect();
                menu.style.display = 'block';
                menu.style.position = 'fixed';
                menu.style.zIndex = '999999';
                menu.style.left = rect.left + 'px';
                menu.style.top = (rect.bottom + 6) + 'px';
                menu.style.bottom = 'auto';
            } else {
                menu.style.display = 'none';
            }
            e.stopPropagation();
        } else {
            document.querySelectorAll('.dropdown-container').forEach(d => {
                d.classList.remove('active');
                const m = d.querySelector('.dropdown-menu');
                if (m) m.style.display = 'none';
            });
        }
    });
  </script>
</body>
</html>