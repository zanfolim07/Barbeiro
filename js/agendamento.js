document.addEventListener('DOMContentLoaded', () => {
  const monthYearText = document.getElementById('calendar-month-year');
  const daysGrid = document.getElementById('calendar-days');
  const btnPrev = document.getElementById('btn-prev-month');
  const btnNext = document.getElementById('btn-next-month');

  const inputData = document.getElementById('input-data');
  const inputHorario = document.getElementById('input-horario');

  let currentDate = new Date();
  let selectedDateStr = '';

  const monthNames = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ];

  // Máscara e formatação automática para o campo de Telefone
  const telefoneInput = document.getElementById('telefone');
  if (telefoneInput) {
    telefoneInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, ''); 
      if (value.length > 11) value = value.slice(0, 11);

      if (value.length > 10) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
      } else if (value.length > 5) {
        value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
      } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
      } else if (value.length > 0) {
        value = value.replace(/^(\d*)/, '($1');
      }
      e.target.value = value;
    });
  }

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    if (monthYearText) {
      monthYearText.textContent = `${monthNames[month]} ${year}`;
    }

    if (!daysGrid) return;
    daysGrid.innerHTML = '';

    const firstDayIndex = new Date(year, month, 1).getDay();
    const lastDay = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < firstDayIndex; i++) {
      const emptySpan = document.createElement('span');
      emptySpan.classList.add('empty');
      daysGrid.appendChild(emptySpan);
    }

    for (let day = 1; day <= lastDay; day++) {
      const daySpan = document.createElement('span');
      daySpan.textContent = day;

      const thisDate = new Date(year, month, day);
      const formattedMonth = String(month + 1).padStart(2, '0');
      const formattedDay = String(day).padStart(2, '0');
      const dateString = `${year}-${formattedMonth}-${formattedDay}`;

      if (thisDate < today) {
        daySpan.classList.add('past');
      } else {
        if (selectedDateStr === dateString) {
          daySpan.classList.add('selected');
        }

        daySpan.addEventListener('click', () => {
          document.querySelectorAll('#calendar-days span').forEach(s => s.classList.remove('selected'));
          daySpan.classList.add('selected');

          selectedDateStr = dateString;
          if (inputData) inputData.value = dateString;

          atualizarHorariosOcupados();
        });
      }

      daysGrid.appendChild(daySpan);
    }
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
    });
  }

  if (btnNext) {
    btnNext.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
    });
  }

  const timePills = document.querySelectorAll('.time-pill');

  timePills.forEach(pill => {
    pill.addEventListener('click', () => {
      if (pill.disabled || pill.classList.contains('disabled')) return;

      timePills.forEach(p => p.classList.remove('selected'));
      pill.classList.add('selected');

      if (inputHorario) {
        inputHorario.value = pill.textContent.trim();
      }
    });
  });

  function atualizarHorariosOcupados() {
    const dataVal = inputData ? inputData.value : '';
    const barbeiroRadio = document.querySelector('input[name="barbeiro"]:checked');
    const barbeiroVal = barbeiroRadio ? barbeiroRadio.value : '';

    if (!dataVal) return;

    fetch(`../php/buscar_horarios.php?data=${dataVal}&barbeiro=${encodeURIComponent(barbeiroVal)}`)
      .then(res => res.json())
      .then(data => {
        const ocupados = data.ocupados || [];

        timePills.forEach(pill => {
          const horaTexto = pill.textContent.trim();

          if (ocupados.includes(horaTexto)) {
            pill.classList.add('disabled');
            pill.classList.remove('selected');
            pill.disabled = true;
          } else {
            pill.classList.remove('disabled');
            pill.disabled = false;
          }
        });

        if (inputHorario && ocupados.includes(inputHorario.value)) {
          inputHorario.value = '';
        }
      })
      .catch(err => console.error('Erro ao verificar horários:', err));
  }

  const barbeiroRadios = document.querySelectorAll('input[name="barbeiro"]');
  barbeiroRadios.forEach(radio => {
    radio.addEventListener('change', atualizarHorariosOcupados);
  });

  // Validação estrita no envio do formulário (Telefone e E-mail completos)
  const form = document.getElementById('form-agendamento');
  if (form) {
    form.addEventListener('submit', (e) => {
      const telefoneLimpo = telefoneInput ? telefoneInput.value.replace(/\D/g, '') : '';
      const emailInput = document.getElementById('email');
      const emailValor = emailInput ? emailInput.value.trim() : '';
      
      // Regex que valida se o e-mail possui estrutura completa (ex: texto@dominio.com)
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(emailValor)) {
        e.preventDefault();
        alert('Por favor, informe um endereço de e-mail completo e válido (ex: seuemail@gmail.com).');
        emailInput.focus();
        return;
      }

      if (telefoneLimpo.length < 10 || telefoneLimpo.length > 11) {
        e.preventDefault();
        alert('O número de telefone deve conter o DDD seguido do número correto (10 ou 11 dígitos no total).');
        telefoneInput.focus();
        return;
      }

      if (!inputData || !inputData.value) {
        e.preventDefault();
        alert('Por favor, escolha um dia no calendário.');
        return;
      }
      if (!inputHorario || !inputHorario.value) {
        e.preventDefault();
        alert('Por favor, selecione um horário disponível.');
        return;
      }
    });
  }

  renderCalendar();
});