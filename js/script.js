/* ==========================================================================
   INTERATIVIDADE GERAL E NAVEGAÇÃO DA BARBEARIA (JS/SCRIPT.JS)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

  /* --------------------------------------------------------------------------
     1. MENU HAMBÚRGUER (MOBILE)
     -------------------------------------------------------------------------- */
  const hamburgerBtn = document.getElementById('hamburger-btn') || document.querySelector('.hamburger-btn');
  const navLinks = document.getElementById('nav-links') || document.querySelector('.nav-links');

  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', () => {
      navLinks.classList.toggle('active');
    });

    // Fecha o menu mobile ao clicar em qualquer link de navegação
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('active');
      });
    });
  }

  /* --------------------------------------------------------------------------
     2. CARROSSEL DE SERVIÇOS (SETAS DE NAVEGAÇÃO)
     -------------------------------------------------------------------------- */
  const servicesGrid = document.getElementById('services-grid');
  const btnPrevServices = document.getElementById('btn-prev-services');
  const btnNextServices = document.getElementById('btn-next-services');

  if (servicesGrid && btnPrevServices && btnNextServices) {
    // Seta para a Direita (Avança os serviços)
    btnNextServices.addEventListener('click', () => {
      servicesGrid.scrollBy({
        left: servicesGrid.clientWidth,
        behavior: 'smooth'
      });
    });

    // Seta para a Esquerda (Volta os serviços)
    btnPrevServices.addEventListener('click', () => {
      servicesGrid.scrollBy({
        left: -servicesGrid.clientWidth,
        behavior: 'smooth'
      });
    });
  }

  /* --------------------------------------------------------------------------
     3. ENVIO DA NEWSLETTER VIA AJAX (SEM RECARREGAR A PÁGINA)
     -------------------------------------------------------------------------- */
  const formNewsletter = document.getElementById('form-newsletter');
  const newsletterMsg = document.getElementById('newsletter-msg');

  if (formNewsletter && newsletterMsg) {
    formNewsletter.addEventListener('submit', function (e) {
      e.preventDefault(); // Evita a recarga da página e o salto para o topo

      const formData = new FormData(formNewsletter);

      fetch('php/processa_newsletter.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        exibirMensagemNewsletter(data.msg, data.status);

        if (data.status === 'sucesso') {
          formNewsletter.reset();
        }
      })
      .catch(error => {
        exibirMensagemNewsletter('Erro ao enviar. Tente novamente.', 'erro');
      });
    });
  }

  function exibirMensagemNewsletter(texto, tipo) {
    newsletterMsg.textContent = texto;
    newsletterMsg.className = `newsletter-msg ${tipo}`;
    newsletterMsg.style.display = 'block';
    newsletterMsg.style.opacity = '1';
    newsletterMsg.style.transform = 'translateY(0)';

    setTimeout(() => {
      newsletterMsg.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      newsletterMsg.style.opacity = '0';
      newsletterMsg.style.transform = 'translateY(-4px)';

      setTimeout(() => {
        newsletterMsg.style.display = 'none';
      }, 500);
    }, 5000);
  }

  /* --------------------------------------------------------------------------
     4. TELA DE LOGIN / CADASTRO (TROCA DE CARDS)
     -------------------------------------------------------------------------- */
  const cardLogin = document.getElementById('card-login');
  const cardCadastro = document.getElementById('card-cadastro');
  const btnToCadastro = document.getElementById('btn-to-cadastro');
  const btnToLogin = document.getElementById('btn-to-login');

  if (btnToCadastro && cardLogin && cardCadastro) {
    btnToCadastro.addEventListener('click', () => {
      cardLogin.classList.add('hidden');
      cardCadastro.classList.remove('hidden');
    });
  }

  if (btnToLogin && cardLogin && cardCadastro) {
    btnToLogin.addEventListener('click', () => {
      cardCadastro.classList.add('hidden');
      cardLogin.classList.remove('hidden');
    });
  }

  /* --------------------------------------------------------------------------
     5. TELA DE PERFIL (ALTERAÇÃO DE ABAS E CARDS)
     -------------------------------------------------------------------------- */
  const tabPerfil = document.getElementById('tab-perfil');
  const tabAgendamentos = document.getElementById('tab-agendamentos');
  const sectionPerfil = document.getElementById('section-perfil');
  const sectionAgendamentos = document.getElementById('section-agendamentos');

  const cardDados = document.getElementById('card-dados-usuario');
  const cardSenha = document.getElementById('card-alterar-senha');
  const btnOpenSenha = document.getElementById('btn-open-alterar-senha');
  const btnCancelSenha = document.getElementById('btn-cancel-alterar-senha');

  // Verifica se a URL possui o parâmetro para manter na aba de agendamentos
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('tab') === 'agendamentos' && tabAgendamentos && sectionAgendamentos) {
    tabAgendamentos.classList.add('active');
    tabPerfil.classList.remove('active');
    sectionAgendamentos.classList.remove('hidden');
    sectionPerfil.classList.add('hidden');
  }

  // Alternância entre a Aba Perfil e a Aba Agendamentos
  if (tabPerfil && tabAgendamentos && sectionPerfil && sectionAgendamentos) {
    tabPerfil.addEventListener('click', () => {
      tabPerfil.classList.add('active');
      tabAgendamentos.classList.remove('active');
      sectionPerfil.classList.remove('hidden');
      sectionAgendamentos.classList.add('hidden');
    });

    tabAgendamentos.addEventListener('click', () => {
      tabAgendamentos.classList.add('active');
      tabPerfil.classList.remove('active');
      sectionAgendamentos.classList.remove('hidden');
      sectionPerfil.classList.add('hidden');
    });
  }

  // Alternar entre Formulário de Dados e Formulário de Troca de Senha
  if (btnOpenSenha && cardDados && cardSenha) {
    btnOpenSenha.addEventListener('click', () => {
      cardDados.classList.add('hidden');
      cardSenha.classList.remove('hidden');
    });
  }

  if (btnCancelSenha && cardDados && cardSenha) {
    btnCancelSenha.addEventListener('click', () => {
      cardSenha.classList.add('hidden');
      cardDados.classList.remove('hidden');
    });
  }

  /* --------------------------------------------------------------------------
     6. BOTÕES DE MOSTRAR / OCULTAR SENHA (OLHO - GLOBAL)
     -------------------------------------------------------------------------- */
  const togglePasswordBtns = document.querySelectorAll('.toggle-password');

  togglePasswordBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      const icon = btn.querySelector('i');

      if (input && input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      } else if (input) {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    });
  });

});

/* --------------------------------------------------------------------------
     7. MENU DROPDOWN DO PERFIL
     -------------------------------------------------------------------------- */
const userMenuBtn = document.getElementById('user-menu-btn');
const userDropdown = document.getElementById('user-dropdown');

if (userMenuBtn && userDropdown) {
  userMenuBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('show');
  });

  document.addEventListener('click', (e) => {
    if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
      userDropdown.classList.remove('show');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      userDropdown.classList.remove('show');
    }
  });
}