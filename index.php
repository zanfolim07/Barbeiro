<?php
session_start();
require_once __DIR__ . '/php/funcoes.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barbearia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/geral.css">
  <link rel="stylesheet" href="css/home.css">
</head>
<body>
  <header class="hero">
    <img src="img/banner-principal.jpg" alt="Banner Barbearia" class="hero-bg-img">
    <div class="overlay"></div>
    
    <nav class="navbar">

      <button class="hamburger-btn" id="hamburger-btn" aria-label="Abrir Menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <div class="nav-links" id="nav-links">
        <a href="#inicio" class="active">Início</a>
        <a href="#sobre">Sobre</a>
        <a href="#servicos">Serviços</a>
        <a href="#equipe">Equipe</a>
        <a href="pages/agendamentos.php">Agendar</a>
        <a href="#contato">Contato</a>
      </div>

      <div class="user-area">
        <?php if (isset($_SESSION['usuario_nome'])): ?>
          <div class="user-menu-wrapper">
            <button type="button" class="user-icon-btn" id="user-menu-btn" aria-label="Menu do Usuário" style="display: flex; align-items: center; gap: 0.6rem; background: transparent; border: none; cursor: pointer;">
              <span class="user-greeting" style="color: #fff; font-weight: 500; font-size: 0.95rem;">
                Olá, <?= htmlspecialchars(explode(' ', $_SESSION['usuario_nome'])[0]) ?>
              </span>
              <span class="user-icon"><i class="fa-solid fa-user"></i></span>
            </button>

            <div class="user-dropdown" id="user-dropdown">
              <a href="pages/perfil.php" class="dropdown-item">
                <i class="fa-regular fa-id-card"></i> Meu Perfil
              </a>
              <a href="php/logout.php" class="dropdown-item logout">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="pages/login.php" class="user-icon" title="Entrar / Cadastrar" style="color: #fff; font-size: 1.3rem;"><i class="fa-regular fa-user"></i></a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="hero-content" id="inicio">
      <h1>Seu estilo começa aqui.</h1>
      <p>Agende seu horário em poucos minutos com profissionais especializados.
      Corte, barba e muito mais.</p>
      <div class="hero-buttons">
        <a href="pages/agendamentos.php" class="btn btn-outline">Agendar Horário</a>
        <a href="#servicos" class="btn btn-outline">Serviços</a>
      </div>
    </div>
  </header>

  <main>
    <section id="sobre" class="about-section">
      <div class="about-container">
        <div class="about-text">
          <h2>Mais que uma barbearia, uma <br><span class="highlight">experiência.</span></h2>
          <p>Na nossa barbearia, cada atendimento é pensado para oferecer conforto, qualidade e estilo. Trabalhamos com profissionais experientes, ambiente moderno e produtos de alta qualidade para garantir o melhor resultado em cada serviço.</p>
        </div>
        <div class="about-image">
          <img src="img/img-sobre.jpg" alt="Ambiente da Barbearia">
        </div>
      </div>
    </section>

    <section id="servicos" class="services-section">
      <h2>Nossos <span class="highlight">Serviços</span></h2>
      <div class="services-wrapper">
        <button class="nav-arrow left" id="btn-prev-services" aria-label="Anterior">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        
        <div class="services-grid" id="services-grid">
          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-corte-masculino.jpg" alt="Corte Tesoura e Máquina">
            </div>
            <h3>Corte Masculino</h3>
            <p class="service-desc">Cortes modernos e clássicos realizados com técnicas profissionais para valorizar seu estilo.</p>
            <div class="service-info">
              <span class="price">R$ 45,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 40 min</span>
            </div>
          </article>

          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-barba.jpg" alt="Barba Completa">
            </div>
            <h3>Barba</h3>
            <p class="service-desc">Acabamento perfeito, alinhamento e hidratação para deixar sua barba impecável.</p>
            <div class="service-info">
              <span class="price">R$ 30,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 30 min</span>
            </div>
          </article>

          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-corte-barba.jpg" alt="Combo Barba e Cabelo">
            </div>
            <h3>Corte + Barba</h3>
            <p class="service-desc">O combo ideal para quem deseja um visual completo.</p>
            <div class="service-info">
              <span class="price">R$ 70,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 1 hora</span>
            </div>
          </article>

          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-sobrancelha.jpg" alt="Pigmentação de Barba">
            </div>
            <h3>Sobrancelha</h3>
            <p class="service-desc">Design discreto para destacar sua expressão.</p>
            <div class="service-info">
              <span class="price">R$ 20,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 15 min</span>
            </div>
          </article>

          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-hidratação.jpg" alt="Sobrancelha">
            </div>
            <h3>Hidratação Capilar</h3>
            <p class="service-desc">Hidratação profunda para fios mais saudáveis, macios e brilhantes.</p>
            <div class="service-info">
              <span class="price">R$ 45,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 25 min</span>
            </div>
          </article>

          <article class="service-card">
            <div class="service-img-container">
              <img src="img/img-barboterapia.jpg" alt="Tratamento Capilar">
            </div>
            <h3>Barboterapia</h3>
            <p class="service-desc">Tratamento completo para barba com hidratação, modelagem e acabamento profissional.</p>
            <div class="service-info">
              <span class="price">R$ 50,00</span>
              <span class="time"><i class="fa-regular fa-clock"></i> 45 min</span>
            </div>
          </article>
        </div>

        <button class="nav-arrow right" id="btn-next-services" aria-label="Próximo">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </section>

    <section id="equipe" class="barbers-section">
      <h2>Nossa <span class="highlight">Equipe</span></h2>
      <div class="barbers-grid">
        <article class="barber-card">
          <div class="barber-avatar">
            <img src="img/joao-silva.jpg" alt="Barbeiro Joao Silva">
          </div>
          <h3>João Silva</h3>
          <div class="tags">
            <span class="tag">Degradê</span>
            <span class="tag">Barba</span>
            <span class="tag">Corte Social</span>
          </div>
          <div class="rating">
            <div class="stars">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <span class="reviews">(120 avaliações)</span>
          </div>
        </article>

        <article class="barber-card">
          <div class="barber-avatar">
            <img src="img/Lucas-Oliveira.jpg" alt="Barbeiro Lucas Oliveira">
          </div>
          <h3>Lucas Oliveira</h3>
          <div class="tags">
            <span class="tag">Cortes Modernos</span>
            <span class="tag">Barba</span>
            <span class="tag">Pigmentação</span>
          </div>
          <div class="rating">
            <div class="stars">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <span class="reviews">(98 avaliações)</span>
          </div>
        </article>

        <article class="barber-card">
          <div class="barber-avatar">
            <img src="img/Rafael-costa.jpg" alt="Barbeiro Rafael Costa">
          </div>
          <h3>Rafael Costa</h3>
          <div class="tags">
            <span class="tag">Navalhado</span>
            <span class="tag">Sobrancelha</span>
            <span class="tag">Corte Clássico</span>
          </div>
          <div class="rating">
            <div class="stars">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <span class="reviews">(145 avaliações)</span>
          </div>
        </article>
      </div>
    </section>

    <section id="contato" class="contact-banner">
      <img src="img/banner-contato.jpg" alt="Fundo Contato" class="banner-bg-img">
      <div class="overlay"></div>
      <div class="contact-container">
        <div class="contact-text">
          <h2>Entre em Contato</h2>
          <p>Será um prazer atender você. Escolha um dos canais ao lado e fale com nossa equipe.</p>
        </div>
        <div class="contact-info">
          <div class="contact-item">
            <i class="fa-solid fa-location-dot"></i>
            <span>Rua Exemplo, 123 - Centro</span>
          </div>
          <div class="contact-item">
            <i class="fa-solid fa-phone"></i>
            <span>(11) 99999-9999</span>
          </div>
          <div class="contact-item">
            <i class="fa-brands fa-whatsapp"></i>
            <span>(11) 98888-8888</span>
          </div>
        </div>
      </div>
    </section>

    <section class="feedbacks-section">
      <h2>Feedbacks</h2>
      <div class="feedbacks-grid">
        <article class="feedback-card">
          <div class="client-avatar">
            <img src="img/Carlos-Henrique.jpg" alt="Foto do Cliente">
          </div>
          <div class="feedback-body">
            <div class="feedback-header">
              <span class="client-name">Carlos Henrique</span>
              <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
              </div>
            </div>
            <p>"Atendimento impecável! O ambiente é ótimo e os barbeiros são extremamente atenciosos."</p>
          </div>
        </article>

        <article class="feedback-card">
          <div class="client-avatar">
            <img src="img/Joao-nunes.jpg" alt="Foto do Cliente">
          </div>
          <div class="feedback-body">
            <div class="feedback-header">
              <span class="client-name">João Nunes</span>
              <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
              </div>
            </div>
            <p>"Faço a barba toda semana aqui. A toalha quente no final faz toda a diferença!"</p>
          </div>
        </article>

        <article class="feedback-card centered-card">
          <div class="client-avatar">
            <img src="img/Felipe-martins.jpg" alt="Foto do Cliente">
          </div>
          <div class="feedback-body">
            <div class="feedback-header">
              <span class="client-name">Felipe Martins</span>
              <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
              </div>
            </div>
            <p>"Excelente lugar. Sempre saio satisfeito com o resultado do meu degradê."</p>
          </div>
        </article>
      </div>
    </section>
  </main>

  <footer class="footer-banner">
    <img src="img/banner-footer.jpg" alt="Fundo Rodapé" class="banner-bg-img">
    <div class="overlay"></div>
    <div class="footer-content">
      <div class="footer-top">
        <div class="hours-block">
          <p><strong>Segunda a Sexta:</strong> 09:00 às 20:00</p>
          <p><strong>Sábado:</strong> 08:00 às 18:00</p>
        </div>
        
        <form id="form-newsletter" class="newsletter-block">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(tokenCsrf()) ?>">
          <label for="email">Receba nossas novidades:</label>

          <div class="input-group">
            <input type="email" id="email" name="email" placeholder="Seu e-mail" required>
            <button type="submit" class="btn-submit">Enviar</button>
          </div>

          <span id="newsletter-msg" class="newsletter-msg" style="display: none;"></span>
        </form>
      </div>
      
      <nav class="footer-nav">
        <a href="#inicio">Início</a>
        <a href="#sobre">Sobre</a>
        <a href="#servicos">Serviços</a>
        <a href="#equipe">Equipe</a>
        <a href="pages/agendamentos.html">Agendar</a>
        <a href="#contato">Contato</a>
      </nav>
    </div>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/carrossel.js"></script>
</body>
</html>