document.addEventListener('DOMContentLoaded', () => {
  
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

  const tabPerfil = document.getElementById('tab-perfil');
  const tabAgendamentos = document.getElementById('tab-agendamentos');
  const sectionPerfil = document.getElementById('section-perfil');
  const sectionAgendamentos = document.getElementById('section-agendamentos');

  const cardDados = document.getElementById('card-dados-usuario');
  const cardSenha = document.getElementById('card-alterar-senha');
  const btnOpenSenha = document.getElementById('btn-open-alterar-senha');
  const btnCancelSenha = document.getElementById('btn-cancel-alterar-senha');

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('tab') === 'agendamentos' && tabAgendamentos && sectionAgendamentos) {
    tabAgendamentos.classList.add('active');
    tabPerfil.classList.remove('active');
    sectionAgendamentos.classList.remove('hidden');
    sectionPerfil.classList.add('hidden');
  }

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
});