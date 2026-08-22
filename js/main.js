document.addEventListener('DOMContentLoaded', () => {

  const hamburgerBtn = document.getElementById('hamburger-btn') || document.querySelector('.hamburger-btn');
  const navLinks = document.getElementById('nav-links') || document.querySelector('.nav-links');

  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', () => {
      navLinks.classList.toggle('active');
    });

    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('active');
      });
    });
  }

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

  document.querySelectorAll('.toggle-phone').forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.closest('.phone-field') || btn.parentElement;
      const phone = container?.querySelector('[data-full-phone]');
      const icon = btn.querySelector('i');
      const revealed = btn.dataset.revealed === 'true';

      if (!phone) {
        return;
      }

      if (phone.tagName === 'INPUT') {
        phone.value = revealed ? phone.dataset.maskedPhone : phone.dataset.fullPhone;
      } else {
        phone.textContent = revealed ? phone.dataset.maskedPhone : phone.dataset.fullPhone;
      }

      btn.dataset.revealed = revealed ? 'false' : 'true';
      btn.setAttribute('aria-label', revealed ? 'Mostrar telefone' : 'Ocultar telefone');
      icon.classList.toggle('fa-eye', revealed);
      icon.classList.toggle('fa-eye-slash', !revealed);
    });
  });

  document.querySelectorAll('.phone-mask-input').forEach(input => {
    const form = input.form;
    const storage = form?.querySelector('[data-phone-storage]') || document.getElementById(`${input.id}-real`);
    let digits = storage?.value.replace(/\D/g, '').slice(0, 11) || '';

    const formatPhone = (value) => {
      return value.length > 4 ? `${'*'.repeat(value.length - 4)}${value.slice(-4)}` : value;
    };

    input.value = formatPhone(digits);
    let substituirSelecao = false;

    const atualizarTelefone = (novosDigitos) => {
      digits = novosDigitos.replace(/\D/g, '').slice(0, 11);
      if (storage) storage.value = digits;
      input.value = formatPhone(digits);
      input.setSelectionRange(input.value.length, input.value.length);
    };

    input.addEventListener('paste', (event) => {
      event.preventDefault();
      const colado = event.clipboardData?.getData('text') || '';
      const substituir = input.selectionStart === 0 && input.selectionEnd === input.value.length;
      atualizarTelefone(substituir ? colado : digits + colado);
    });

    input.addEventListener('keydown', (event) => {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'a') {
        substituirSelecao = true;
      }

      if ((event.key === 'Backspace' || event.key === 'Delete')
        && input.selectionStart === 0
        && input.selectionEnd === input.value.length) {
        event.preventDefault();
        substituirSelecao = false;
        atualizarTelefone('');
      }
    });

    input.addEventListener('input', (event) => {
      if (event.inputType?.startsWith('delete')) {
        atualizarTelefone(digits.slice(0, -1));
      } else if (event.data) {
        atualizarTelefone(substituirSelecao ? event.data : digits + event.data);
      } else {
        atualizarTelefone(input.value);
      }

      substituirSelecao = false;
    });

    form?.addEventListener('submit', () => {
      if (storage) storage.value = digits;
    });
  });

  const formNewsletter = document.getElementById('form-newsletter');
  const newsletterMsg = document.getElementById('newsletter-msg');

  if (formNewsletter && newsletterMsg) {
    formNewsletter.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(formNewsletter);

      const isInPagesFolder = window.location.pathname.includes('/pages/');
      const urlPhp = isInPagesFolder ? '../php/processa_newsletter.php' : 'php/processa_newsletter.php';

      fetch(urlPhp, {
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

});