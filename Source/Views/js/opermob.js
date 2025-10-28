// JQUERY INIT

$(function () {

  // MAKS

  $(".mask-date").mask('00/00/0000');
  $(".mask-datetime").mask('00/00/0000 00:00');
  $(".mask-month").mask('00/0000', { reverse: true });
  $(".mask-doc").mask('000.000.000-00', { reverse: true });
  $(".mask-fone").mask('(00) 0000-0000');
  $(".mask-cel").mask('(00) 0 0000-0000');
  $(".mask-cnpj").mask('00.000.000/0000-00', { reverse: true });
  $(".mask-cep").mask('00000-000', { reverse: true });
  $(".mask-card").mask('0000  0000  0000  0000', { reverse: true });
  $(".mask-money").mask('000.000.000.000.000,00', { reverse: true, placeholder: "0,00" });

  $(".mask-money").on('blur', function () {
    var value = $(this).val();

    // Verifica se o valor tem uma vírgula, caso contrário, adiciona ",00"
    if (value && value.indexOf(',') === -1) {
      $(this).val(value + ",00");
    }
  });

  function setViewportHeight() {
    // Calcular a altura real da viewport
    let vh = window.innerHeight * 0.01;
    // Aplicar o valor à variável CSS
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }

  // Calcular a altura da viewport no carregamento e no resize da tela
  window.addEventListener('resize', setViewportHeight);
  window.addEventListener('load', setViewportHeight);

  $("form:not('.ajax_off')").submit(function (e) {
    e.preventDefault();

    var form = $(this);

    form.ajaxSubmit({
      url: form.attr("action"),
      type: "POST",
      dataType: "json",
      success: function (response) {
        console.log(response);
        //redirect
        if (response.redirect) {
          window.location.href = response.redirect;
        }

        //reload
        if (response.reload) {
          window.location.reload();
        }

        //message
        if (response.message) {
          alert(response.message);
        }

        if (response.messagereload) {
          alert(response.message);
          window.location.reload();
        }
      },
      complete: function () {
        if (form.data("reset") === true) {
          form.trigger("reset");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log('Error:', textStatus, errorThrown);
      }
    });
  });
});

$(document).ready(function () {
  var triggerTabList = [].slice.call(document.querySelectorAll('#tab-ordens button'))
  triggerTabList.forEach(function (triggerEl) {
    var tabTrigger = new bootstrap.Tab(triggerEl)

    triggerEl.addEventListener('click', function (event) {
      event.preventDefault()
      tabTrigger.show()
    })
  })
});

$(document).on('touchstart', function (e) {
  if ($(window).scrollTop() === 0) {
    //e.preventDefault();  // Impede o início do pull-to-refresh
  }
});


$(document).ready(function () {
  $(".mobsection-button").click(function () {
    var target = $(this).closest(".mobsection-item").find(".mobsection-body");
    var icon = $(this).find("i");

    $(".mobsection-body").not(target).slideUp(); // Fecha todos os outros
    $(".mobsection-button i").not(icon).removeClass("fa-chevron-up").addClass("fa-chevron-down"); // Reseta os ícones dos outros

    target.slideToggle(); // Alterna o estado do atual
    icon.toggleClass("fa-chevron-down fa-chevron-up"); // Alterna o ícone do atual
  });

  $('.toggle-password').click(function () {
    var target = $(this).data('target');
    var input = $(target);
    var icon = $(this).find('i');

    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      input.attr('type', 'password');
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  $(document).off('click', '#submit-operos-aditivo').on('click', '#submit-operos-aditivo', function (e) {
    e.preventDefault(); // evita qualquer comportamento padrão
    e.stopPropagation(); // evita propagação para outros handlers

    var $form = $(this).closest('form'); // pega o formulário da modal
    var url = $form.attr('action');
    var data = $form.serialize();

    $.post(url, data, function (response) {
      console.log(response);

      // Verificar se a resposta é uma string e fazer o parse se necessário
      if (typeof response === 'string') {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Erro ao fazer parse do JSON:", e);
          return;
        }
      }
      if (response.message) {
        alert(response.message);
      } else {
        console.log("No message in response"); // Adicionado para depuração
      }

      if (response.success) {
        alert(response.success);
      } else {
        console.log("No success in response"); // Adicionado para depuração
      }

      if (response.reload) {
        location.reload();
      } else {
        console.log("No reload in response"); // Adicionado para depuração
      }
    });
  });
});

function formatarDataBr(data) {
  var partesData = data.split('-');
  return partesData[2] + '/' + partesData[1] + '/' + partesData[0]; // '01/11/2024'
}

function formatarDataIso(data) {
  var partesData = data.split('/');
  return partesData[2] + '-' + partesData[1] + '-' + partesData[0]; // '2024-11-01'
}

function formatarDataHoraBr(dataHora) {
  if (!dataHora) return ''; // Retorna vazio se não houver valor

  // Cria objeto Date a partir da string recebida
  var data = new Date(dataHora);

  if (isNaN(data.getTime())) return ''; // Retorna vazio se a data for inválida

  var dia = String(data.getDate()).padStart(2, '0');
  var mes = String(data.getMonth() + 1).padStart(2, '0');
  var ano = String(data.getFullYear()).slice(-2); // pega só os dois últimos dígitos
  var hora = String(data.getHours()).padStart(2, '0');
  var minuto = String(data.getMinutes()).padStart(2, '0');

  return dia + '/' + mes + '/' + ano + ' ' + hora + ':' + minuto;
}

function formatarDataHoraIso(dataHoraBr) {
  if (!dataHoraBr) return ''; // Retorna vazio se não houver valor

  // dataHoraBr esperado no formato "dd/mm/aa hh:mm"
  var partes = dataHoraBr.split(' ');
  if (partes.length < 2) return '';

  var data = partes[0].split('/');
  var hora = partes[1].split(':');

  if (data.length < 3 || hora.length < 2) return '';

  var dia = data[0];
  var mes = data[1];
  var ano = data[2].length === 2 ? '20' + data[2] : data[2]; // garante 4 dígitos

  var hh = hora[0];
  var mm = hora[1];

  // Formato válido para datetime-local → "2024-11-01T14:35"
  return ano + '-' + mes.padStart(2, '0') + '-' + dia.padStart(2, '0') +
    'T' + hh.padStart(2, '0') + ':' + mm.padStart(2, '0');
}

function getTodayDate() {
  var today = new Date();
  var year = today.getFullYear();
  var month = String(today.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
  var day = String(today.getDate()).padStart(2, '0');
  return year + '-' + month + '-' + day;
}

function updateCharCount() {
  var maxLength = 500;
  var currentLength = $('#txtObsTarefa').val().length;
  var remaining = maxLength - currentLength;
  $('#charCount1').text(remaining);
}

function addEmptyItems(galleryContainer, viewClass) {
  const items = galleryContainer.children('.gallery-item').not('.empty');
  const itemsPerRow = viewClass === 'gallery-2' ? 2 : viewClass === 'gallery-3' ? 3 : 4;
  const emptyItemsNeeded = itemsPerRow - (items.length % itemsPerRow);

  for (let i = 0; i < emptyItemsNeeded && emptyItemsNeeded < itemsPerRow; i++) {
    galleryContainer.append('<div class="gallery-item empty"></div>');
  }
}

function createActionButtons(tipo, arquivo, icone, container) {
  if (tipo.startsWith('image/')) {
    var imgSrc = arquivo.url + arquivo.arquivo;
    var newGalleryItem = `
      <div class="gallery-item">
        <img data-id="${arquivo.id}" src="${imgSrc}" alt="Imagem">
        <div class="action-buttons hidden">
          <button data-file="img" class="btn-view" data-url="${arquivo.url + arquivo.arquivo}"><span class="fa fa-eye"></span></button>
          <button class="btn-delete" data-id="${arquivo.id}" data-delete="${arquivo.delete}"><span class="fa-solid fa-trash"></button>
        </div>
      </div>`;
    $(container).append(newGalleryItem);
  } else {
    var newGalleryItem = `
      <div class="gallery-item">
        <div data-id="${arquivo.id}" data-href="${arquivo.url + arquivo.arquivo}" class="div-arquivo">
          <span class="${icone}"></span>${arquivo.nome}
        </div>
        <div class="action-buttons hidden">
          <button data-file="file" class="btn-view" data-url="${arquivo.url + arquivo.arquivo}"><span class="fa-solid fa-download"></button>
          <button class="btn-delete" data-id="${arquivo.id}" data-delete="${arquivo.delete}"><span class="fa-solid fa-trash"></button>
        </div>
      </div>`;
    $(container).append(newGalleryItem);
  }
}

function createOs3Item(idMaterial, idos3, descricao, quantidade, container, urldelete, showButtons = true) {
  var newOs3Item = `
    <div class="opermat-item" data-idos3="${idos3}" data-idmat="${idMaterial}">
      <span class="opmat-text">${descricao}</span>
      <span class="opmat-qtde">Qtde: </span><span class="opmat-quantity">${quantidade}</span>
      <span class="opmat-id" style="display: none;">${idos3}</span>
      ${showButtons ? `
        <button class="edit-item"><i class="fa fa-pencil"></i></button>
        <button class="delete-item" data-url="${urldelete}" data-delete="${idos3}"><i class="fa-solid fa-trash"></i></button>
        ` : ''}
    </div>
    `;
  $(container).append(newOs3Item);
}

function createMedicaoItem(idTarefa, idMedicao, datai, dataf, quantidade, container, urldelete, urledit, showButtons = true) {
  var newMedicaoItem = `
    <div class="opermedicao-item" data-idos2="${idTarefa}" data-idmedicao="${idMedicao}">
      <span class="medicao-txt-periodo">${datai} até ${dataf}</span>
      <span class="medicao-txt-qtde"></span><span class="opmat-quantity">${quantidade}</span>
      <span class="opmedicao-id" style="display: none;">${idMedicao}</span>
      ${showButtons ? `
        <button class="edit-medicao" data-url="${urledit}/atualiza2"><i class="fa fa-pencil"></i></button>
        <button class="delete-medicao" data-url="${urldelete}" data-delete="${idMedicao}"><i class="fa-solid fa-trash"></i></button>
        ` : ''}
    </div>
    `;
  $(container).append(newMedicaoItem);
}

function limitarTexto(texto, limite = 40, sufixo = '...') {
  return texto.length > limite ? texto.substring(0, limite) + sufixo : texto;
}

$(document).ready(function () {
  $('.bottom-icons').on('wheel', function (e) {
    e.preventDefault();
    $(this).scrollLeft($(this).scrollLeft() + e.originalEvent.deltaY);
  });

  $('body').addClass('noscroll');

  function setVhUnit() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }

  function ajustarAlturaOperTabContent() {
    const operTabContent = document.querySelector('.oper-tab-content');
    const viewportHeight = window.innerHeight;

    // Calcula a altura para deixar uma margem inferior de 20px
    const alturaDesejada = viewportHeight - operTabContent.getBoundingClientRect().top - 20;
    operTabContent.style.maxHeight = `${alturaDesejada}px`;
  }

  // Executa ao carregar a página e ao redimensionar a janela
  window.addEventListener('load', ajustarAlturaOperTabContent);
  window.addEventListener('resize', ajustarAlturaOperTabContent);

  window.addEventListener('load', setVhUnit);
  window.addEventListener('resize', setVhUnit);
});

document.addEventListener('DOMContentLoaded', () => {
  const daysContainer = document.querySelector('.days-selector');
  const display = document.querySelector('.date-display');
  const contents = document.querySelectorAll('.days-content .content');

  let isScrolling = false;
  let scrollingTimeout;

  function updateActiveDay() {
    const containerCenter = daysContainer.offsetWidth / 2;
    let closestDay = null;
    let closestDistance = Infinity;

    const days = document.querySelectorAll('.days-selector .day');
    days.forEach((day) => {
      const dayCenter = day.offsetLeft + day.offsetWidth / 2 - daysContainer.scrollLeft;
      const distance = Math.abs(containerCenter - dayCenter);

      if (distance < closestDistance) {
        closestDistance = distance;
        closestDay = day;
      }
    });

    if (closestDay) {
      days.forEach((day) => day.classList.remove('active'));
      closestDay.classList.add('active');
      display.textContent = closestDay.textContent;

      const dayIndex = closestDay.getAttribute('data-year') + "-" + closestDay.getAttribute('data-month') + "-" + closestDay.getAttribute('data-day');
      updateContent(dayIndex);

      return closestDay;
    }
  }

  function adjustToCenter() {
    const activeDay = updateActiveDay();
    if (activeDay) {
      const containerCenter = daysContainer.offsetWidth / 2;
      const dayOffset = activeDay.offsetLeft + activeDay.offsetWidth / 2;
      const scrollPosition = dayOffset - containerCenter;
      daysContainer.scrollTo({
        left: scrollPosition,
        behavior: 'smooth',
      });
    }
  }

  function padDayIndex(dayIndex) {
    const parts = dayIndex.split("-");
    const year = parts[0];
    const month = parts[1].padStart(2, "0");
    const day = parts[2].padStart(2, "0");
    return `${year}-${month}-${day}`;
  }

  function updateContent(dayIndex) {
    dayIndex = padDayIndex(dayIndex); // <-- corrige o formato

    contents.forEach((content) => content.classList.remove('active'));

    const activeContent = document.querySelector(`#content-${dayIndex}`);
    const noEvent = document.querySelector(`#content-default`);

    console.log(dayIndex);
    console.log(activeContent);
    if (activeContent) {
      activeContent.classList.add('active');
    } else {
      noEvent.classList.add('active');
    }
  }

  function initializeDays() {
    $('.days-selector').on('scroll', function () {
      isScrolling = true;
      updateActiveDay();

      clearTimeout(scrollingTimeout);
      scrollingTimeout = setTimeout(() => {
        isScrolling = false;
        adjustToCenter();
      }, 150);
    });
    // Centralizar o dia atual ao carregar a página
  }

  initializeDays();
  centerToday();

  $('#month-selector').change(function () {
    const month = $(this).val();
    const year = $('#year-selector').val(); // Obtém o ano selecionado
    const url = $(this).data('url');

    $.ajax({
      url: url, // Endpoint no backend
      method: 'POST',
      data: { month, year }, // Envia o mês e o ano
      success: function (response) {
        $('.days-selector').html(response); // Atualizar o container com os novos dias
        initializeDays(); // Reaplica a lógica de inicialização
        updateActiveDay();
      },
      error: function () {
        alert('Erro ao carregar os dias.');
      }
    });
  });

  $('#year-selector').change(function () {
    const month = $('#month-selector').val(); // Obtém o mês selecionado
    const year = $(this).val(); // Obtém o ano selecionado
    const url = $('#month-selector').data('url'); // Usa o mesmo URL configurado no mês

    $.ajax({
      url: url, // Endpoint no backend
      method: 'POST',
      data: { month, year }, // Envia o mês e o ano
      success: function (response) {
        $('.days-selector').html(response); // Atualizar o container com os novos dias
        initializeDays(); // Reaplica a lógica de inicialização
        updateActiveDay();
      },
      error: function () {
        alert('Erro ao carregar os dias.');
      }
    });
  });

  function centerToday() {
    const today = new Date();
    const currentDay = today.getDate();
    const days = document.querySelectorAll('.days-selector .this-month');

    let todayElement = null;
    days.forEach((day) => {
      if (parseInt(day.getAttribute('data-day')) == currentDay) {
        todayElement = day;
        day.classList.add('active');
        display.textContent = day.textContent;
      }
    });

    if (todayElement) {
      const containerCenter = daysContainer.offsetWidth / 2;
      const dayOffset = todayElement.offsetLeft + todayElement.offsetWidth / 2;
      const scrollPosition = dayOffset - containerCenter;
      daysContainer.scrollTo({
        left: scrollPosition,
        behavior: 'auto',
      });
    }
  }

  document.querySelectorAll('.arrow').forEach(button => {
    button.addEventListener('click', function () {
      const targetSelector = this.getAttribute('data-target');
      const direction = this.getAttribute('data-direction');
      const selectElement = document.querySelector(targetSelector);

      if (selectElement) {
        const options = Array.from(selectElement.options);
        const currentIndex = selectElement.selectedIndex;

        let newIndex = currentIndex;

        if (direction === 'prev' && currentIndex > 0) {
          newIndex = currentIndex - 1;
        } else if (direction === 'next' && currentIndex < options.length - 1) {
          newIndex = currentIndex + 1;
        }

        selectElement.selectedIndex = newIndex;
        selectElement.dispatchEvent(new Event('change')); // Trigger change event if necessary
      }
    });
  });
});

$(document).ready(function () {
  // Função para aplicar os filtros (busca e checkboxes)
  function applyFilters() {
    let searchValue = ($('#os1-busca-ordens').val() || '').toLowerCase(); // Valor do campo de busca
    let showConcluidas = $('#os1-chk-concluidas').is(':checked'); // Status do checkbox "Concluídas"
    let showCanceladas = $('#os1-chk-canceladas').is(':checked'); // Status do checkbox "Canceladas"

    $('.item-total').each(function () {
      let item = $(this);
      let status = item.find('.os1-mob-item').data('status').toLowerCase(); // Valor de data-status
      let textContent = item.text().toLowerCase(); // Texto do item

      // Condição para exibir o item:
      let matchesSearch = textContent.includes(searchValue);
      let matchesStatus =
        (status !== 'concluída' || showConcluidas) &&
        (status !== 'cancelada' || showCanceladas);

      if (matchesSearch && matchesStatus) {
        item.show();
      } else {
        item.hide();
      }
    });
  }

  // Eventos para aplicar os filtros
  $('#os1-busca-ordens').on('input', applyFilters); // Busca em tempo real
  $('.os1-chk-status').on('change', applyFilters); // Mudança no estado dos checkboxes

  // Aplica os filtros na inicialização
  applyFilters();
});

$(document).ready(function () {
  const $chkConcluidas = $('#chk-concluidas');
  const $chkCanceladas = $('#chk-canceladas');
  const $chklabels = $('.filtro-status label'); // As labels dos checkboxes
  const $filterSelect = $('#filter');
  const $startDateInput = $('#start-date');
  const $endDateInput = $('#end-date');
  const $buscaOrdens = $('#busca-ordens'); // Input de busca
  const $mobItems = $('.mob-item');
  const statusFromLink = $('#initial-status').val();  // Pega o status do link, caso exista
  const $customDatesDiv = $('#custom-dates');

  function updatePeriodSelect() {
    if (statusFromLink === 'futuras') {
      // Remover "Hoje" e modificar os textos das outras opções
      $filterSelect.find('option[value="hoje"]').remove();
      $filterSelect.find('option[value="last7"]').text('Próximos 7 dias').val('next7');
      $filterSelect.find('option[value="last30"]').text('Próximos 30 dias').val('next30');
    } else {
      // Adicionar novamente a opção "Hoje" se não for "futuras"
      if ($filterSelect.find('option[value="hoje"]').length === 0) {
        $filterSelect.prepend('<option value="hoje">Hoje</option>');
      }
      // Restaurar os textos das outras opções
      $filterSelect.find('option[value="last7"]').text('Últimos 7 dias');
      $filterSelect.find('option[value="last30"]').text('Últimos 30 dias');
    }
  }

  updatePeriodSelect();

  // Função para aplicar os filtros
  function applyFilters() {
    const filterStatus = {
      'concluidas': $chkConcluidas.is(':checked'),
      'canceladas': $chkCanceladas.is(':checked')
    };

    const selectedFilter = $filterSelect.val();
    const startDate = $startDateInput.val();
    const endDate = $endDateInput.val();

    // Filtro de período
    let dateFilterApplied = false;
    let startDateObj = null;
    let endDateObj = null;

    if (selectedFilter === 'hoje') {
      const today = new Date();
      // Formata a data de hoje para o formato YYYY-MM-DD
      const todayFormatted = today.toISOString().split('T')[0];
      startDateObj = todayFormatted;
      endDateObj = todayFormatted;
      dateFilterApplied = true;
    } else if (selectedFilter === 'last7') {
      const today = new Date();
      startDateObj = new Date(today.setDate(today.getDate() - 7)); // 7 dias atrás
      endDateObj = new Date();
      dateFilterApplied = true;
    } else if (selectedFilter === 'last30') {
      const today = new Date();
      startDateObj = new Date(today.setDate(today.getDate() - 30)); // 30 dias atrás
      endDateObj = new Date();
      dateFilterApplied = true;
    } else if (selectedFilter === 'next7') {
      const today = new Date();
      endDateObj = new Date();
      endDateObj.setDate(today.getDate() + 7); // 7 dias à frente
      startDateObj = today;
      dateFilterApplied = true;
    } else if (selectedFilter === 'next30') {
      const today = new Date();
      endDateObj = new Date();
      endDateObj.setDate(today.getDate() + 30); // 30 dias à frente
      startDateObj = today;
      dateFilterApplied = true;
    } else if (selectedFilter === 'custom' && startDate && endDate) {
      startDateObj = new Date(startDate);
      endDateObj = new Date(endDate);
      dateFilterApplied = true;
    }

    // Aplica o filtro de status e data
    $mobItems.each(function () {
      const $item = $(this);
      const itemStatus = $item.data('status');
      const itemDate = $item.data('date'); // Data no formato YYYY-MM-DD
      let showItem = true;

      // Filtro de status baseado no link ou checkbox
      if (statusFromLink && itemStatus !== statusFromLink) {
        showItem = false; // Se o status não corresponder, esconde o item
      }

      if (!statusFromLink) { // Quando não há status no link
        // Esconde os itens "Concluídas" e "Canceladas" por padrão
        if (itemStatus === 'concluidas' && !filterStatus['concluidas']) {
          showItem = false;
        }
        if (itemStatus === 'canceladas' && !filterStatus['canceladas']) {
          showItem = false;
        }
      }

      // Filtro de período (data)
      if (dateFilterApplied) {
        if (itemDate < startDateObj || itemDate > endDateObj) {
          showItem = false; // Se a data não corresponder ao intervalo, esconde o item
        }
      }

      // Filtro de busca (procurando na descrição ou informações do item)
      const searchQuery = ($buscaOrdens.val() || '').toLowerCase();
      const itemText = $item.find('.mob-txt').text().toLowerCase() + " " + $item.find('.mob-stxt').text().toLowerCase(); // Concatenando os textos dos itens
      if (searchQuery && !itemText.includes(searchQuery)) {
        showItem = false; // Se a busca não corresponder, esconde o item
      }

      // Mostrar ou esconder o item
      $item.toggle(showItem);
    });
  }

  // Verifica se há status no link e esconde ou mostra os checkboxes e labels
  if (statusFromLink) {
    // Se houver um status no link, esconde os checkboxes e labels
    $chkConcluidas.hide();
    $chkCanceladas.hide();
    $chklabels.hide(); // Esconde as labels também
  } else {
    // Se não houver status no link, exibe os checkboxes e labels
    $chkConcluidas.show();
    $chkCanceladas.show();
    $chklabels.show(); // Exibe as labels
  }

  // Evento para exibir a div #custom-dates quando a opção 'custom' for selecionada
  $filterSelect.on('change', function () {
    const selectedFilter = $(this).val();
    if (selectedFilter === 'custom') {
      $customDatesDiv.removeClass('d-none');
    } else {
      $customDatesDiv.addClass('d-none');
    }

    // Após selecionar o filtro, aplica os filtros (sem o filtro de data personalizado até que as datas sejam preenchidas)
    applyFilters();
  });

  // Eventos para aplicar filtros
  $chkConcluidas.on('change', applyFilters);
  $chkCanceladas.on('change', applyFilters);
  $startDateInput.on('input', applyFilters);
  $endDateInput.on('input', applyFilters);

  // Evento para aplicar filtro de busca em tempo real
  $buscaOrdens.on('input', function () {
    applyFilters(); // Aplica os filtros sempre que o usuário digitar
  });

  // Aplica os filtros na carga inicial
  applyFilters();
});



$(document).ready(function () {
  function backdropModal(modal) {
    // Evento para adicionar um novo backdrop ao abrir a segunda modal
    $(modal).on('show.bs.modal', function () {
      const $backdrop = $('<div>')
        .addClass('modal-backdrop fade show')
        .css('z-index', '1055'); // Um pouco acima do backdrop da primeira modal
      $('body').append($backdrop);
    });

    // Evento para remover o backdrop extra ao fechar a segunda modal
    $(modal).on('hidden.bs.modal', function () {
      const $backdrops = $('.modal-backdrop');
      if ($backdrops.length > 1) {
        $backdrops.last().remove();
      }
    });
  }

  function backdropModal2(modal) {
    // Evento para adicionar um novo backdrop ao abrir a segunda modal
    $(modal).on('show.bs.modal', function () {
      const $backdrop = $('<div>')
        .addClass('modal-backdrop fade show')
        .css('z-index', '2055'); // Um pouco acima do backdrop da primeira modal
      $('body').append($backdrop);
    });

    // Evento para remover o backdrop extra ao fechar a segunda modal
    $(modal).on('hidden.bs.modal', function () {
      const $backdrops = $('.modal-backdrop');
      if ($backdrops.length > 1) {
        $backdrops.last().remove();
      }
    });
  }

  backdropModal('#modalAssinatura');
  backdropModal('#opermatModal');
  backdropModal('#obsModal');
  backdropModal('#anexosModal');
  backdropModal('#aditivoModal');
  backdropModal('#opermedicaoModal');
  backdropModal2('#editModal');
  backdropModal2('#editMedicaoModal');
});


$(document).ready(function () {

  // $("body").on("click", "#os-refresh", function () {
  //   location.reload();
  // });

  $('#dash-andamento').on('click', function () {
    var id = this.getAttribute('data-id');
    var url = this.getAttribute('data-url');
    var dia = this.getAttribute('data-dia');

    const hoje = getTodayDate();

    var dhoje = new Date(hoje);
    var dtarefa = new Date(dia);

    var link = "";

    if (hoje == dia) {
      link = url + "/hoje/" + dia;
    } else if (dhoje > dtarefa) {
      link = url + "/pendentes/" + dia;
    } else if (dhoje < dtarefa) {
      link = url + "/futuras/" + dia;
    }

    window.location.href = link;
  });

  // Função para obter o valor de um parâmetro da URL
  function getParameterByName(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
    var results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }

  // Obtém o data-id da URL
  var specificId = getParameterByName('data-id');

  if (specificId) {
    // Seleciona o elemento .mob-item com o data-id específico
    var item = $('.mob-item[data-id="' + specificId + '"]');

    // Verifica se o elemento foi encontrado
    if (item.length) {
      // Aciona o evento de clique no elemento encontrado
      item.click();
    } else {
      console.log('Elemento com data-id ' + specificId + ' não encontrado.');
    }
  }
});

$(document).ready(function () {

  function telaTarefa(response) {
    // Variáveis dinâmicas
    var status = "";
    if (response.status == 'A') {
      status = 'AGUARDANDO INÍCIO';
    } else if (response.status == 'C') {
      status = 'CONCLUÍDO';
    } else if (response.status == 'I') {
      status = 'EM ANDAMENTO';
    } else if (response.status == 'P') {
      status = 'PAUSADO';
    } else if (response.status == 'D') {
      status = 'CANCELADO';
    }

    const idTarefaCript = response.idCript;
    const idTarefa = response.id;
    const statusTarefa = status;
    const dataTarefa = formatarDataBr(response.dataexec);
    const horaTarefa = response.horaexec;
    const cliente = response.cliente;
    const tarefa = response.servico;
    const unidade = response.unidade;
    const quantidade = response.qtde;
    const os = response.id_os1;
    const medicao = response.medicao;
    let assinatura = "Colher Assinatura";
    const obs = response.obs;
    const obslabel = response.obslabel;
    var signClass = "naoassinado";
    let isDisabled = false; //** Criado pra desabilitar elementos quando a tarefa estiver cancelada - também desabilita o botão de assinatura caso já exista uma assinatura*/
    let isDisabled2 = false; //** Criado pra desabilitar elementos quando a tarefa estiver aguardando início */    

    let aditivoTarefaDesabilitado = false;

    if (response.statusOS1 != '2' && response.statusOS1 != '3') {
      aditivoTarefaDesabilitado = true;
      isDisabled = true;
      isDisabled2 = true;
    } else {
      if (response.assinado == "S") {
        signClass = "assinado";
        assinatura = "Assinado";
        isDisabled = true;
      }
      if (response.status == "D") {
        signClass = "sign-canceled";
        isDisabled = true;
      }
      if (response.status == "A") {
        isDisabled2 = true;
      }
    }

    // Criação do HTML dinamicamente
    const tarefaSection = $("<div>").addClass("tarefa-section");
    const tarefaItem = $("<div>").addClass("fcad-form-row tarefa-item");

    // Botões
    const buttonsContainer = $("<div>").addClass("fcad-form-row").append(
      $("<button>").addClass("btn btn-info btn-tarefa tarefa-stt").text("INICIAR").hide().attr("data-id", idTarefa).attr("data-acao", "stt").prop("disabled", response.statusOS1 === 8),
      $("<button>").addClass("btn btn-warning btn-tarefa tarefa-psr").text("PAUSAR").hide().attr("data-id", idTarefa).attr("data-acao", "psr").prop("disabled", response.statusOS1 === 8),
      $("<button>").addClass("btn btn-success btn-tarefa tarefa-end").text("CONCLUIR").hide().attr("data-id", idTarefa).attr("data-acao", "end").prop("disabled", response.statusOS1 === 8),
      $("<button>").addClass("btn btn-success btn-tarefa tarefa-res").text("RETOMAR").hide().attr("data-id", idTarefa).attr("data-acao", "res").prop("disabled", response.statusOS1 === 8),
      $("<button>").addClass("btn btn-secondary btn-tarefa tarefa-can").text("CANCELAR").attr("data-id", idTarefa).attr("data-acao", "can").prop("disabled", response.statusOS1 === 8)
    );

    if (response.status == 'A') {
      buttonsContainer.find(".tarefa-stt").show(); // Exibe o botão "INICIAR"
    } else if (response.status == 'I') {
      buttonsContainer.find(".tarefa-psr, .tarefa-end").show(); // Exibe os botões "PAUSAR" e "CONCLUIR"
    } else if (response.status == 'P') {
      buttonsContainer.find(".tarefa-res").text("RETOMAR").show(); // Exibe o botão "RETOMAR"
    } else if (response.status == 'D' || response.status == 'C') {
      buttonsContainer.find(".tarefa-can").hide(); // Esconde o botão "CANCELAR"
    }

    tarefaItem.append(buttonsContainer);

    // Status da Tarefa
    tarefaItem.append(
      $("<div>").addClass("fcad-form-group group-status").append(
        $("<label>").html("<strong>Status</strong>"),
        $("<div>").addClass("tarefa-description tarefa-status").text(statusTarefa)
      )
    );

    // Data/Hora
    tarefaItem.append(
      $("<div>").addClass("fcad-form-group group-data").append(
        $("<label>").html("<strong>Data/Hora</strong>"),
        $("<div>").addClass("tarefa-description").text(dataTarefa + " " + horaTarefa)
      )
    );

    var url = $("#modalOrdemMob").data('url');

    // OS e botão "Visualizar"

    tarefaItem.append(
      $("<div>").addClass("fcad-form-group-tarefa group-osview").append(
        $("<a>")
          .attr("href", url + '/' + idTarefaCript) // Adiciona o atributo href
          .attr("target", "_blank") // Abre em uma nova aba
          .attr("data-id", os)
          .attr("id", "os-visualizar")
          .addClass("btnqtde fa-solid fa-eye")
      )
    );

    var controleGroup = response.controle ?
      $("<div>").addClass("fcad-form-group direita").append(
        $("<label>").html("<strong>Controle</strong>"),
        $("<div>").addClass("tarefa-description").text(response.controle)
      ) : null;

    var rowContent = $("<div>").addClass("fcad-form-group").append(
      $("<label>").html("<strong>OS</strong>"),
      $("<div>").addClass("tarefa-description fcad-form-row").text(os)
    );

    if (controleGroup) {
      rowContent = [$("<div>").addClass("fcad-form-group").append(
        $("<label>").html("<strong>OS</strong>"),
        $("<div>").addClass("tarefa-description fcad-form-row").text(os)
      ), controleGroup];
    }

    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(rowContent)
    );

    // Segmento

    if (response.segmento) {
      tarefaItem.append(
        $("<div>").addClass("fcad-form-row tarefa-item").append(
          $("<div>").addClass("fcad-form-group").append(
            $("<label>").html("<strong>" + response.labelFiliais + "</strong>"),
            $("<div>").addClass("tarefa-description").text(response.segmento)
          )
        )
      );
    }

    // Cliente
    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group").append(
          $("<label>").html("<strong>Cliente</strong>"),
          $("<div>").addClass("tarefa-description").text(cliente)
        ),
        $("<div>").addClass("fcad-form-group btnOs3 direita").append(
          $("<label>").html("<strong>Add Tarefa</strong>")
            .css("margin-left", "auto"),
          $("<button>")
            .html("<i class='fa fa fa-plus'></i>")
            .attr("id", "tarefa-adicional")
            .attr("data-id", idTarefa)
            .attr("data-toggle", "modal")
            .attr("data-target", "#aditivoModal")
            .addClass("btnqtde")
            .attr("disabled", aditivoTarefaDesabilitado)
            .css("margin-left", "auto")
        )
      )
    );

    // Tarefa e Quantidade
    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group").append(
          $("<label>").html("<strong>Tarefa</strong>"),
          $("<div>").addClass("tarefa-description").text(tarefa)
        ),
        $("<div>").addClass("fcad-form-group direita").append(
          $("<label>").html("<strong>Qtde</strong>"),
          $("<div>").addClass("tarefa-description").text(quantidade)
        )
      )
    );

    $("#aditivoModalLabel").text("Tarefa Adicional OS " + os);
    $("#aditivoModal input[name='aditivoCliente']").val(response.cliente);
    $("#aditivoModal input[name='aditivoOS1']").val(os);
    $("#aditivoModal select[name='aditivoOperador']").val(response.id_colaborador);

    // Medições

    let botoesResultadosMedicoes = true; //** Variável pra controlar os botões de editar e excluir medições da grid .opermedicao-container */

    if (response.status == 'A' || response.status == 'C' || response.status == 'D') {
      $("#insertMedicaoForm").hide();
      botoesResultadosMedicoes = false; //** Desabilita os botões de editar e excluir medições da grid .opermedicao-container */
    } else {
      $("#insertMedicaoForm").show();
    }

    if (medicao == "1") {

      $("#tarefaMedicao").text(tarefa);

      var medicaoTotalContratado = 0;
      var medicaoTotalRealizado = 0;
      var medicaoTotalPendente = 0;

      medicaoTotalContratado = quantidade;

      // Lista de medições
      var medicaoContainer = $('.opermedicao-container');

      medicaoContainer.show();
      medicaoContainer.children().not('.opermedicao-cabecalho').remove();

      var ultimaMedicao = "";

      if (response.medicoes.length > 0) {
        response.medicoes.forEach(function (medicoes) {
          let qtde = parseFloat(medicoes.qtde);
          let qtdeDecimal = qtde - Math.floor(qtde);

          ultimaMedicao = medicoes.dataf;

          if (qtdeDecimal > 0) {
            qtde = qtde.toFixed(2);
          } else {
            qtde = qtde.toFixed(0);
          }

          medicaoTotalRealizado += parseFloat(medicoes.qtde);

          createMedicaoItem(medicoes.id_os2, medicoes.id, medicoes.datai, medicoes.dataf, qtde, medicaoContainer, medicoes.delete, medicoes.edit, botoesResultadosMedicoes);
        });
      } else {
        medicaoContainer.hide();
      }

      medicaoTotalPendente = medicaoTotalContratado - medicaoTotalRealizado;

      tarefaItem.append(
        $("<div>").addClass("fcad-form-row tarefa-item").append(
          $("<div>").addClass("fcad-form-group").append(
            $("<label>").html("<strong>Medição</strong>"),
            $("<div>")
              .addClass("medicao-description")
              .attr("id", "tarefa-item-medicao-parcial")
              .text(medicaoTotalRealizado + "/" + quantidade + " " + unidade)
          ),
          $("<div>").addClass("fcad-form-group").append(
            $("<label>").html("<strong>Data</strong>"),
            $("<div>")
              .addClass("medicao-description")
              .text(ultimaMedicao)
          ),
          $("<div>").addClass("fcad-form-group btnOs3").append(
            $("<button>")
              .html("<i class='fa fa fa-plus'></i>")
              .attr("id", "medicao-modal")
              .attr("data-id", idTarefa)
              .attr("data-toggle", "modal")
              .attr("data-target", "#opermedicaoModal")
              .addClass("btnqtde")
              .attr("disabled", isDisabled2)
          )
        )
      );

      $("#medicaoTotal").text(medicaoTotalContratado);
      $("#medicaoRealizado").text(medicaoTotalRealizado);
      $("#medicaoPendente").text(medicaoTotalPendente);
      $(".opmedicao-unidade").text(" " + unidade);
    }

    // Material e Quantidade

    if (response.status == 'C' || response.status == 'D') {
      matIcone = "fa fa-eye";
      showButtons = false;
    } else {
      matIcone = "fa fa-plus";
      showButtons = true;
    }

    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group coluna10").append(
          $("<label>").html("<strong>Produtos/Materiais</strong>")

        ),
        $("<div>").addClass("fcad-form-group btnOs3 esquerda").append(
          $("<button>")
            .html("<i class='fa " + matIcone + "'></i>")
            .attr("id", "os3-modal")
            .attr("data-id", idTarefa)
            .attr("data-toggle", "modal")
            .attr("data-target", "#opermatModal")
            .attr("data-status", response.status)
            .addClass("btnqtde")
            .attr("disabled", isDisabled2)
        )
      )
    );

    var select = $("#selectItem");
    select.empty();

    if (response.materiais.length > 0) {
      select.append(new Option("Selecione um item", "", true, true));
      response.materiais.forEach(function (material) {
        select.append(new Option(material.descricao, material.id, false, false));
      });
      select.select2({
        dropdownParent: $('#opermatModal')
      });
    } else {
      select.append(new Option("Não existem itens cadastrados.", "", true, true));
    }

    // Lista de materiais OS3
    var container = $('.opermat-container');
    container.empty();

    if (response.os3.length > 0) {
      response.os3.forEach(function (os3) {
        let qtde = parseFloat(os3.qtde);
        let qtdeDecimal = qtde - Math.floor(qtde);

        if (qtdeDecimal > 0) {
          qtde = qtde.toFixed(2);
        } else {
          qtde = qtde.toFixed(0);
        }
        createOs3Item(os3.id_materiais, os3.id, os3.descricao, qtde, container, os3.delete, showButtons);
      });
    }

    // Anexos

    if (response.status == 'C' || response.status == 'D') {
      attachIcone = "fa fa-eye";
    } else {
      attachIcone = "fa fa-paperclip";
    }

    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group").append(
          $("<label>").html("<strong>Anexos</strong>")
        ),
        $("<div>").addClass("fcad-form-group btnOs3 esquerda").append(
          $("<button>")
            .html("<i class='fa " + attachIcone + "'></i>")
            .attr("id", "btn-attach-modal")
            .attr("data-id", idTarefa)
            .attr("data-toggle", "modal")
            .attr("data-target", "#anexosModal")
            .attr("data-status", response.status)
            .addClass("btnqtde")
            .attr("disabled", isDisabled2)
        )
      )
    );

    $('#galleryContainer').children().not('.attach-plus').remove();

    if (response.arquivos.length > 0) {
      const galleryContainer = $('#galleryContainer');
      const viewClass = galleryContainer.attr('class').split(' ').pop();
      response.arquivos.forEach(function (arquivo) {
        var tipo = arquivo.tipo;
        var icone = "fa fa-file";

        if (tipo.includes('pdf')) {
          icone = "fa fa-file-pdf";
        } else if (tipo.includes('msword') || tipo.includes('vnd.openxmlformats-officedocument.wordprocessingml.document')) {
          icone = "fa fa-file-word";
        } else if (tipo.includes('vnd.ms-excel') || tipo.includes('vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
          icone = "fa fa-file-excel";
        } else if (tipo.includes('text/plain')) {
          icone = "fa fa-file-alt";
        } else if (
          tipo.includes('x.zip-compressed') ||
          tipo.includes('x-rar-compressed') ||
          tipo.includes('x-tar') ||
          tipo.includes('x-7z-compressed') ||
          tipo.includes('x-zip-compressed') ||
          tipo.includes('x-rar') ||
          tipo.includes('octet-stream')) {
          icone = "fa-regular fa-file-zipper";
        }

        createActionButtons(tipo, arquivo, icone, galleryContainer);

      });

      addEmptyItems(galleryContainer, viewClass);
    }

    // Obs
    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group").append(
          $("<label>").html("<strong>Obs</strong>"),
          $("<button>").addClass("mob-input")
            .attr("id", "mob-obs")
            .attr("data-id", idTarefa)
            .attr("data-status", response.status)
            .text(obslabel)
            .attr("disabled", isDisabled2)
        )
      )
    );

    $("#txtObsTarefa").val(obs);

    // Assinatura
    // Cria o botão
    let button = $("<button>")
      .addClass("btn " + signClass)
      .attr("id", "mob-signbtn")
      .attr("data-id", idTarefa)
      .text(assinatura)
      .attr("disabled", isDisabled); // Adiciona o atributo disabled se isDisabled for true

    if (response.status == 'A') {
      button.prop('disabled', true);
    }

    // Adiciona o ícone FontAwesome se isDisabled for true
    if (isDisabled && response.status == 'D') {
      button.prepend($("<i>").addClass("sign-check"));
    } else if (isDisabled && response.status == 'C') {
      button.prepend($("<i>").addClass("sign-check fa-solid fa-check"));
    } else {
      button.prepend($("<i>").addClass("sign-check fa-solid fa-signature"));
    }

    tarefaItem.append(
      $("<div>").addClass("fcad-form-row tarefa-item").append(
        $("<div>").addClass("fcad-form-group").append(
          $("<label>").html("<strong>Assinatura</strong>"),
          button
        )
      )
    );

    // Adiciona a estrutura na página
    tarefaSection.append(tarefaItem);
    $(".ordemopermob").append(tarefaSection);
  }

  $(document).on('click', '#os3-modal', function () {
    if ($(this).data('status') == 'C' || $(this).data('status') == 'D') {
      $("#opermatBody").prop('hidden', true);

      var id = $(this).data('id');
      var url = $("#opermatModal").data('url');

      $.ajax({
        url: url,
        method: 'POST',
        data: { verificar: id },
        dataType: 'json',
        success: function (response) {
          if (response.dados) {
            var container = $(response.container);
            container.empty();
            var newLinha = `
              <div class="opermat-item">
                <span class="opmat-text">Nenhum produto/material usado nesta tarefa!</span>
              </div>
            `;
            $(container).append(newLinha);
          }
        },
        error: function () {
          alert('ERRO');
        }
      });
    } else {
      $("#opermatBody").prop('hidden', false);
    }
  });

  $(document).on('click', '#btn-attach-modal', function () {
    if ($(this).data('status') == 'C' || $(this).data('status') == 'D') {
      $(".attach-plus").prop('hidden', true);

      var id = $(this).data('id');
      var url = $("#opermatModal").data('url');

      $.ajax({
        url: url,
        method: 'POST',
        data: { verificar2: id },
        dataType: 'json',
        success: function (response) {
          if (response.dados) {
            var container = $(response.container);
            container.empty();
            var newLinha = `
              <div class="opermat-item">
                <span class="opmat-text">Nenhum anexo nesta tarefa!</span>
              </div>
            `;
            $(container).append(newLinha);
          }
        },
        error: function () {
          alert('ERRO');
        }
      });
    } else {
      $(".attach-plus").prop('hidden', false);
    }
  });


  $(".mob-item").on('click', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');

    $.ajax({
      url: url, // Endpoint no backend
      method: 'POST',
      data: { id: id }, // Envia o mês e o ano
      dataType: 'json',
      success: function (response) {
        $('#modalOrdemMob').modal('show'); // Abre a modal para criação de um novo evento        
        $('.title-ordem').text('Tarefa #' + id);
        $('.ordemopermob').empty();
        telaTarefa(response);
      },
      error: function () {
        alert('Erro ao carregar os dias.');
      }
    });
  })

  if (navigator.userAgent.includes("EdgA")) { // Edge no Android
    var container = $("#days-content");
    container.removeClass('days-content');
    container.addClass('days-content-edge');
  }

});

$(document).ready(function () {
  $('#decreaseQuantity').on('click', function () {
    let quantity = parseInt($('#quantityInput').val());
    if (quantity > 1) {
      $('#quantityInput').val(quantity - 1);
    }
  });

  $('#increaseQuantity').on('click', function () {
    let quantity = parseInt($('#quantityInput').val());
    $('#quantityInput').val(quantity + 1);
  });

  $(document).on('click', '[data-toggle="modal"]', function () {
    var target = $(this).attr('data-target');
    $(target).modal('show');
  });

  $(document).on('click', '#close-anexos', function () {
    $("#anexosModal").modal('hide');
  });

  $(document).on('click', '#close-obs', function () {
    $("#obsModal").modal('hide');
  });

  $(document).on('click', '#close-opermat', function () {
    $("#opermatModal").modal('hide');
  });

  $(document).on('click', '#close-opermedicao', function () {
    $("#opermedicaoModal").modal('hide');
  });

  $(document).on('click', '#close-aditivo', function () {
    $("#aditivoModal").modal('hide');
  });

  $(document).on('click', '#close-solmob', function () {
    $("#modalSolicitacao").modal('hide');
  });
});

$(document).ready(function () {
  $("body").on('click', '#mob-obs', function (e) {
    e.preventDefault();
    $('#obsModal').modal('show');
    updateCharCount();
    if ($(this).data('status') == 'C' || $(this).data('status') == 'D') {
      $('#txtObsTarefa').prop('disabled', true);
      $('#saveObs').prop('disabled', true);
    }
  });

  $('#txtObsTarefa').on('input', function () {
    updateCharCount();
    const maxLength = 500;
    if ($(this).val().length > maxLength) {
      $(this).val($(this).val().slice(0, maxLength));
    }
  });

  $("body").on('click', '#saveObs', function (e) {
    e.preventDefault();
    var id = $('#mob-obs').data('id');
    var obs = $('#txtObsTarefa').val();
    var url = $("#obsModal").data('url');

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        id: id,
        obs: obs
      },
      dataType: 'json',
      success: function (response) {
        $('#obsModal').modal('hide');
        $("#mob-obs").text(response.obs);
        alert(response.message);
      },
      error: function () {
        alert('Erro ao carregar os dias.');
      }
    });

  });
});

$(document).ready(function () {

  $("body").on('click', '#mob-signbtn', function (e) {
    e.preventDefault();
    $('#modalAssinatura').modal('show');
  });

  const canvas = document.getElementById('signature-pad');
  const ctx = canvas ? canvas.getContext('2d') : null;
  if (!ctx) {
    console.warn('Canvas element not found. Skipping drawing initialization.');
    return;
  }
  let isDrawing = false;

  function adjustCanvasSize() {
    const modalBody = document.querySelector('#modalAssinatura .modal-body');
    canvas.width = modalBody.offsetWidth; // Largura do canvas
    canvas.height = modalBody.offsetHeight; // Altura do canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas
    toggleSaveButton(); // Atualiza o estado do botão de salvar
  }

  // Ao abrir a modal, ajusta o tamanho do canvas
  $('#modalAssinatura').on('shown.bs.modal', function () {
    adjustCanvasSize();
  });

  // Ajusta o tamanho do canvas ao mudar a orientação da tela
  $(window).on('resize orientationchange', function () {
    adjustCanvasSize();
  });

  // Captura evento de início do desenho
  $(canvas).on('mousedown touchstart', function (e) {
    isDrawing = true;
    const { x, y } = getEventPosition(e);
    ctx.beginPath();
    ctx.moveTo(x, y);
  });

  // Captura evento de movimento do desenho
  $(canvas).on('mousemove touchmove', function (e) {
    if (!isDrawing) return;
    const { x, y } = getEventPosition(e);
    ctx.lineTo(x, y);
    ctx.stroke();
    toggleSaveButton(); // Atualiza o estado do botão de salvar
  });

  // Captura evento de fim do desenho
  $(canvas).on('mouseup touchend', function () {
    isDrawing = false;
    ctx.closePath();
    toggleSaveButton(); // Atualiza o estado do botão de salvar
  });

  // Função para obter posição do evento no canvas
  function getEventPosition(event) {
    const rect = canvas.getBoundingClientRect();
    const touchEvent = event.touches ? event.touches[0] : event;

    // Calculando a posição no canvas com as dimensões ajustadas
    const x = (touchEvent.clientX - rect.left) * (canvas.width / rect.width);
    const y = (touchEvent.clientY - rect.top) * (canvas.height / rect.height);

    return { x, y };
  }

  // Função para verificar se o canvas está vazio
  function isCanvasBlank() {
    const blankCanvas = document.createElement('canvas');
    blankCanvas.width = canvas.width;
    blankCanvas.height = canvas.height;
    return canvas.toDataURL() === blankCanvas.toDataURL();
  }

  // Função para habilitar/desabilitar o botão de salvar
  function toggleSaveButton() {
    $('#savesign').prop('disabled', isCanvasBlank());
  }

  // Limpar o canvas
  $('#limpasign').on('click', function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    toggleSaveButton(); // Atualiza o estado do botão de salvar
  });

  // Salvar assinatura como imagem
  $('#savesign').on('click', function () {
    const signatureData = canvas.toDataURL('image/png');
    const id = $('#mob-signbtn').data('id');
    var url = $("#modalOrdemMob").data('url');

    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: {
        signature: signatureData,
        id: id
      },
      success: function (response) {

        if (response.message) {
          alert(response.message);
          return;
        }

        $('#modalAssinatura').modal('hide');
        $('#mob-signbtn').text('Assinado');
        $('#mob-signbtn').addClass('assinado');
        $('#mob-signbtn').prop('disabled', true);
        $('#mob-signbtn').prepend($("<i>").addClass("sign-check fa-solid fa-check"));
        alert("Assinatura salva com sucesso.");

      },
      error: function (error) {
        console.error('Erro ao salvar a assinatura:', error);
        alert('Erro ao salvar a assinatura.');
      }
    });
  });
});

$(document).ready(function () {
  $('#saveMedicao').on('click', function () {

    var tarefaId = $("#medicao-modal").data('id');
    var quantity = $('#quantityInputMedicao').val();
    var datai = $('#medicao-datai').val();
    var dataf = $('#medicao-dataf').val();
    var obs = $('#medicao-obs').val();
    var operador = $('#medicaoOperador').val();
    var equipamento = $('#medicaoEquipamento').val();
    var url = $("#opermedicaoModal").data('url');
    var container = $('.opermedicao-container');

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        tarefaId: tarefaId,
        qtde: quantity,
        datai: datai,
        dataf: dataf,
        obs: obs,
        operador: operador,
        eqp: equipamento
      },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          var container = $('.opermedicao-container');
          container.show();
          createMedicaoItem(response.tarefaId, response.id, formatarDataHoraBr(datai), formatarDataHoraBr(dataf), quantity, container, response.delete, response.edit);
          $('#quantityInputMedicao').val('1');
          $("#medicaoTotal").text(response.total);
          $("#medicaoRealizado").text(response.medido);
          $("#medicaoPendente").text(response.pendente);
          $("#tarefa-item-medicao-parcial").text(response.medido + "/" + response.total + " " + response.unidade);
        }
        alert(response.message);
        $('#medicao-datai').val('');
        $('#medicao-dataf').val('');
        $('#medicao-obs').val('');
        $('#medicaoOperador').val('');
        $('#medicaoEquipamento').val('');
      }
    })
  });

  // Evento de clique para excluir um item
  $(document).on('click', '.delete-medicao', function () {
    var url = $(this).data('url');
    var item = $(this).closest('.opermedicao-item');

    if (confirm('Tem certeza que deseja excluir este item?')) {
      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json', // Define o tipo de dados esperados na resposta
        success: function (response) {
          if (response.status == 'success') {
            item.remove();
            $("#medicaoRealizado").text(response.medido);
            $("#medicaoPendente").text(response.pendente);
            alert(response.message);
          } else {
            alert('Erro ao excluir o item: ' + response.message);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
        }
      });
    }
  });

  $(document).on('click', '.edit-medicao', function () {
    var item = $(this).closest('.opermedicao-item');
    var os2 = item.data('idos2');
    var id = item.find('.opmedicao-id').text();
    var periodo = item.find('.medicao-txt-periodo').text();
    var datai = periodo.substring(0, 14);
    var dataf = periodo.substring(periodo.length - 14);
    var qtde = item.find('.opmat-quantity').text();
    var url = $(this).data('url');

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        id: id
      },
      dataType: 'json',
      success: function (response) {
        console.log(response);
        $("#medicaoOperador-edit").val(response.medicao[0].id_operador);
        $("#medicaoEquipamento-edit").val(response.medicao[0].id_equipamento);
        $("#medicao-obs-edit").val(response.medicao[0].obs);
      },
      error: function () {
        alert('ERRO NA REQUISICAO');
      }
    });

    $("#medicao-datai-edit").val(formatarDataHoraIso(datai));
    $("#medicao-dataf-edit").val(formatarDataHoraIso(dataf));
    $("#quantityInputMedicao-edit").val(item.find('.opmedicao-quantity').text());
    $("#editItemMedicaoId").val(id);
    $("#editOs2Medicao").val(os2);
    $('#quantityInputMedicao2').val(qtde);

    // Exibe a modal de edição
    $('#editMedicaoModal').modal('show');
  });

  // Evento de clique para salvar a edição
  $('#saveMedicaoEdit').on('click', function () {
    var id = $('#editItemMedicaoId').val();
    var operador = $('#medicaoOperador-edit').val();
    var equipamento = $('#medicaoEquipamento-edit').val();
    var obs = $('#medicao-obs-edit').val();
    var newQuantity = $('#quantityInputMedicao2').val();
    var newDatai = $('#medicao-datai-edit').val();
    var newDataf = $('#medicao-dataf-edit').val();
    var url = $("#opermedicaoModal").data('url');
    var os2 = $("#editOs2Medicao").val();

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        id: id,
        tarefaId: os2,
        qtde: newQuantity,
        datai: newDatai,
        dataf: newDataf,
        operador: operador,
        eqp: equipamento,
        obs: obs
      },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          // Atualiza a quantidade do item na lista
          $('.opermedicao-item').each(function () {
            if ($(this).data('idmedicao') == id) {
              $(this).find('.opmat-quantity').text(newQuantity);
              var newPeriodo = formatarDataHoraBr(newDatai) + ' até ' + formatarDataHoraBr(newDataf);
              $(this).find('.medicao-txt-periodo').text(newPeriodo);
              return false; // Sai do loop each
            }
          });

          $("#medicaoTotal").text(response.total);
          $("#medicaoRealizado").text(response.medido);
          $("#medicaoPendente").text(response.pendente);
          $("#tarefa-item-medicao-parcial").text(response.medido + "/" + response.total + " " + response.unidade);

          // Fecha a modal de edição
          $('#editMedicaoModal').modal('hide');
        }
        alert(response.message);
      }
    });
  });

  $(document).on('click', '.sol-mob-item', function () {
    let id = $(this).data('id');
    let url = $(this).data('url');
    let modal = "#modalSolicitacao";

    $(modal + ' #confirmarSolicitacao').prop('hidden', true);
    $(modal + ' #cancelarSolicitacao').prop('hidden', true);

    // Limpa textos da modal
    $(modal + ' .modal-solmob').text('');

    // Abre modal já com botões escondidos
    $(modal).modal('show');

    $.ajax({
      url: url,
      data: { id: id },
      type: 'POST',
      dataType: 'json',
      success: function (response) {
        // Só mostra se a resposta permitir
        if (!response[0].escondeCancelar) {
          $(modal + ' #cancelarSolicitacao').prop('hidden', false);
          $(modal + ' .modal-title').text('Ferramenta Enviada');
          $(modal + ' #sol-info').hide();
        } else if (!response[0].escondeConfirmar) {
          $(modal + ' #confirmarSolicitacao').prop('hidden', false);
          $(modal + ' .modal-title').text('Confirmar Movimentação');
          $(modal + ' #sol-info').show();
        }

        $(modal + ' #id_mov').val(response[0].id);
        $(modal + ' #solmob-eqp').text(limitarTexto(response[0].equipamento_desc));
        $(modal + ' #solmob-lorigem').text(limitarTexto(response[0].local_origem_desc));
        $(modal + ' #solmob-qtde').text(response[0].qtde);
        $(modal + ' #solmob-data').text(response[0].data_formatada);
        $(modal + ' #solmob-udestino').text(response[0].usuario_destino_nome);
        $(modal + ' #solmob-ldestino').text(response[0].local_destino_desc);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log('Falha na requisição: ' + textStatus + ' - ' +
          errorThrown, 5);
      }
    });
  });

  $(document).on('click', '#cancelarSolicitacao', function () {
    let id = $("#id_mov").val();
    let url = $(this).data('url');

    $.ajax({
      url: url,
      data: { id: id },
      type: 'POST',
      dataType: 'json',
      success: function (response) {
        if (response.reload) {
          alert(response.message);
          window.location.reload();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
          errorThrown, 5);
      }
    });

  });

  $(document).on('change', '#descFerramenta', function () {
    let ferramentaId = $(this).val();
    let url = $(this).data('url');

    $.ajax({
      url: url,
      method: 'POST',
      data:
      {
        id: ferramentaId
      },
      dataType: 'json',
      success: function (response) {
        $('#localOrigemSelect option').each(function () {
          let option = $(this);
          let val = Number(option.val());

          // Procura no array de resposta se existe um objeto com essa chave
          let local = response.find(obj => obj.hasOwnProperty(val));

          if (local) {
            let qtde = local[val];

            // Atualiza o texto da opção com a quantidade
            let originalText = option.data('original-text');
            if (!originalText) {
              originalText = option.text();
              option.data('original-text', originalText); // Salva o texto original
            }

            option.text(`${originalText} (${qtde})`);
            option.prop('disabled', false);
          } else {
            option.prop('disabled', true);
          }


        });

        $('#localDestinoSelect option').each(function () {
          let option = $(this);
          let val = Number(option.val());

          // Procura no array de resposta se existe um objeto com essa chave
          let local = response.find(obj => obj.hasOwnProperty(val));

          if (local) {
            let qtde = local[val];

            // Atualiza o texto da opção com a quantidade
            let originalText = option.data('original-text');
            if (!originalText) {
              originalText = option.text();
              option.data('original-text', originalText); // Salva o texto original
            }

            option.text(`${originalText} (${qtde})`);
          }
        });
      },
      error: function () {
        console.log('Erro ao verificar o estoque.');
      }
    });
  });

  function updateSelectOptions(origem, destino) {
    var origemValue = $(origem).val();
    var destinoValue = $(destino).val();

    // Reset all options
    $(origem + ' option, ' + destino + ' option').prop('disabled', false);

    // Disable selected option in the other select
    if (origemValue) {
      $(destino + ' option[value="' + origemValue + '"]').prop('disabled', true);
    }

    if (destinoValue) {
      $(origem + ' option[value="' + destinoValue + '"]').prop('disabled', true);
    }
  }

  $(document).on('change', '#localOrigemSelect, #localDestinoSelect', function () {
    updateSelectOptions('#localOrigemSelect', '#localDestinoSelect');
  });

  $(document).on('change', '#usuarioOrigemSelect, #usuarioDestinoSelect', function () {
    updateSelectOptions('#usuarioOrigemSelect', '#usuarioDestinoSelect');
  });

  // Função para resetar o estado da modal quando ela for aberta
  function resetModalState() {
    // Habilita todos os elementos    
    $('#localOrigemSelect').prop('disabled', false).attr('required', 'required');
    $('#usuarioDestinoSelect').prop('disabled', false).attr('required', 'required');
    $('#localDestinoSelect').prop('disabled', false).attr('required', 'required');

    // Reabilita todas as opções dos selects
    $('#usuarioOrigemSelect option, #usuarioDestinoSelect option').prop('disabled', false);
    $('#localOrigemSelect option, #localDestinoSelect option').prop('disabled', false);

    // Limpa valores dos selects    
    $('#localOrigemSelect').val('');
    $('#usuarioDestinoSelect').val('');
    $('#localDestinoSelect').val('');
  }

  // Adicione esta chamada ao evento de abertura da sua modal
  // Substitua '#suaModal' pelo seletor real da sua modal
  $(document).on('show.bs.modal', '#modalOprMovFerramenta', function () {
    resetModalState();
  });

  $(document).on('click', '#oprmv-sol', function () {
    let modal = "#modalOprMovFerramenta";

    // Limpa textos da modal
    $(modal + ' .modal-solmob').text('');

    // Abre modal já com botões escondidos
    $(modal).modal('show');
  });
});

$(document).ready(function () {

  // Verifica se há instrução para clique após recarregar
  let executeClick = localStorage.getItem('executeClick');
  let targetId = localStorage.getItem('targetDataId');

  if (executeClick === 'true' && targetId) {
    localStorage.removeItem('executeClick'); // Remove para evitar cliques futuros indesejados
    localStorage.removeItem('targetDataId');
    $(`.mob-item[data-id="${targetId}"]`).trigger('click'); // Dispara o clique automático no elemento com o data-id especificado
  }

  $("body").on('click', '.btn-tarefa', function (e) {
    e.preventDefault();

    let tarefa = $(this).data('id');
    let acao = $(this).data('acao');
    let url = $("#url-acoes").val();

    if (acao == 'can') {
      confirmacao = confirm("Deseja realmente CANCELAR esta tarefa? Essa ação não poderá ser desfeita.");
    }

    if (acao == 'can' && !confirmacao) {
      return;
    }

    $.ajax({
      url: url,
      method: "POST",
      data: {
        tarefa: tarefa,
        acao: acao
      },
      dataType: "json",
      success: function (response) {
        var btnStt = $('.tarefa-stt');
        var btnPsr = $('.tarefa-psr');
        var btnRes = $('.tarefa-res');
        var btnEnd = $('.tarefa-end');
        var btnCan = $('.tarefa-can');
        var descricao = $('.tarefa-status');

        if (response.denied) {
          alert(response.denied);
          return;
        }

        if (response.acao) {
          if (response.acao == 'stt') {
            alert(response.message);
            btnStt.hide();
            btnPsr.show();
            btnEnd.show();
            descricao.text("EM ANDAMENTO");
          } else if (response.acao == 'psr') {
            alert(response.message);
            btnPsr.hide();
            btnEnd.hide();
            btnRes.show();
            descricao.text("PAUSADO");
          } else if (response.acao == 'end') {
            alert(response.message);
            btnPsr.hide();
            btnEnd.hide();
            btnCan.hide();
            descricao.text("CONCLUÍDO");
          } else if (response.acao == 'res') {
            alert(response.message);
            btnRes.hide();
            btnPsr.show();
            btnEnd.show();
            descricao.text("EM ANDAMENTO");
          } else if (response.acao == 'can') {
            alert(response.message);
            btnStt.hide();
            btnPsr.hide();
            btnRes.hide();
            btnEnd.hide();
            btnCan.hide();
            descricao.text("CANCELADO");
          }
        }

        // Configura para executar o clique após o recarregamento
        localStorage.setItem('executeClick', 'true');
        localStorage.setItem('targetDataId', tarefa); // Salva o data-id do elemento a ser clicado

        // Recarrega a página
        location.reload();
      },
      error: function (error) {
        console.error("Erro ao enviar dados:", error);
      }
    });
  })
})


$(document).ready(function () {
  let currentViewIndex = 0;
  const viewClasses = ['gallery-3', 'gallery-4', 'gallery-2'];

  $('body').on('click', '#toggleViewButton', function () {
    currentViewIndex = (currentViewIndex + 1) % viewClasses.length;
    const viewClass = viewClasses[currentViewIndex];
    const galleryContainer = $('#galleryContainer');
    galleryContainer.attr('class', 'gallery-container ' + viewClass);

    // Remove elementos vazios existentes
    galleryContainer.find('.gallery-item.empty').remove();

    // Adiciona elementos vazios para completar a linha
    const items = galleryContainer.children('.gallery-item').not('.empty');
    const itemsPerRow = viewClass === 'gallery-2' ? 2 : viewClass === 'gallery-3' ? 3 : 4;
    const emptyItemsNeeded = itemsPerRow - (items.length % itemsPerRow);

    for (let i = 0; i < emptyItemsNeeded && emptyItemsNeeded < itemsPerRow; i++) {
      galleryContainer.append('<div class="gallery-item empty"></div>');
    }
  });
});

$(document).ready(function () {
  $('#fileInput, #pastasInput').on('change', function () {
    var inputId = $(this).attr('id');
    var file = this.files[0];
    var relatedId = $("#btn-attach-modal").data("id");
    var url = $("#anexosModal").data('url');

    if (file) {
      var formData = new FormData();
      formData.append('file', file);
      formData.append('id', relatedId);

      $.ajax({
        url: url, // URL do seu script PHP
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {

          var jsonResponse = JSON.parse(response);
          if (jsonResponse.status === 'success') {
            const galleryContainer = $('#galleryContainer');
            const viewClass = galleryContainer.attr('class').split(' ').pop();
            var tipo = jsonResponse.fileType;

            icone = "";

            if (tipo.includes('pdf')) {
              icone = "fa fa-file-pdf";
            } else if (tipo.includes('msword') || tipo.includes('vnd.openxmlformats-officedocument.wordprocessingml.document')) {
              icone = "fa fa-file-word";
            } else if (tipo.includes('vnd.ms-excel') || tipo.includes('vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
              icone = "fa fa-file-excel";
            } else if (tipo.includes('text/plain')) {
              icone = "fa fa-file-alt";
            } else if (
              tipo.includes('x.zip-compressed') ||
              tipo.includes('x-rar-compressed') ||
              tipo.includes('x-tar') ||
              tipo.includes('x-7z-compressed') ||
              tipo.includes('x-zip-compressed') ||
              tipo.includes('x-rar') ||
              tipo.includes('octet-stream')) {
              icone = "fa-regular fa-file-zipper";
            }

            createActionButtons(tipo, jsonResponse, icone, galleryContainer);

            // Remove elementos vazios existentes
            galleryContainer.find('.gallery-item.empty').remove();

            // Adiciona elementos vazios para completar a linha
            addEmptyItems(galleryContainer, viewClass);
          } else {
            console.error('Erro ao enviar o arquivo:', jsonResponse.message);
          }

        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error('Erro ao enviar o arquivo:', textStatus, errorThrown);
        }
      });
    }
  });
});

$(document).ready(function () {

  // Mostrar modal de visualização de imagem
  function showImageModal(imgSrc) {
    $('#modalImage').attr('src', imgSrc).css('transform', 'scale(1)');
    $('#imageModal').fadeIn();
  }

  $('#close-img').on('click', function () {
    $('#imageModal').fadeOut();
  });

  // Evento para botões de ação
  $('body').on('click', '.btn-view', function () {
    const url = $(this).data('url');
    if ($(this).data('file') == 'img') {
      showImageModal(url);
    } else {
      window.open(url, '_blank');
    }
  });

  $('body').on('click', '.btn-delete', function () {
    const parent = $(this).closest('.gallery-item');
    var id = $(this).data('id');
    var url = $(this).data('delete');

    if (confirm('Tem certeza que deseja deletar este arquivo?')) {
      $.ajax({
        url: url,
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
          if (response.status == 'success') {
            parent.remove();
            alert(response.message);
          } else {
            alert(response.message);
          }
        },
        error: function () {
          alert('Erro ao deletar o arquivo.');
        }
      });
    }
  });

  $('#galleryContainer').on('click', '.gallery-item', function (e) {
    // Verifica se o clique foi na própria div e não nos botões
    if (!$(e.target).is('.btn-view, .btn-delete')) {
      // Alterna a visibilidade da classe .action-buttons
      $(this).find('.action-buttons').toggleClass('hidden');
      // Alterna o efeito branco apenas na imagem
      $(this).find('img').toggleClass('branco');
      $(this).find('.div-arquivo').toggleClass('branco');
    }
  });


  $('#saveOs3').on('click', function () {
    var tarefaId = $("#os3-modal").data('id');
    var selectedId = $('#selectItem').val();
    var selectedText = $('#selectItem option:selected').text();
    var quantity = $('#quantityInput').val();
    var url = $("#opermatModal").data('url');
    var container = $('.opermat-container');

    // Verifica se um item válido foi selecionado
    if (!selectedId || selectedId === "") {
      alert("Por favor, selecione um item válido.");
      return;
    }

    // Verifica se o item já está na lista
    var itemExists = false;
    $('.opermat-item').each(function () {
      if ($(this).data('idmat') == selectedId) {
        itemExists = true;
        return false; // Sai do loop each
      }
    });

    if (itemExists) {
      alert("O item já está na lista. Por favor, edite o item existente.");
      return;
    }

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        tarefaId: tarefaId,
        matId: selectedId,
        qtde: quantity
      },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {

          createOs3Item(selectedId, response.id, selectedText, quantity, container, response.delete);

          // Reseta o select para a opção default
          $('#selectItem').val(null).trigger('change');
          $('#quantityInput').val('1');
          alert(response.message);
        }
      }
    })
  });

  // Evento de clique para excluir um item
  $(document).on('click', '.delete-item', function () {
    var id = $(this).data('delete');
    var url = $(this).data('url');
    var item = $(this).closest('.opermat-item');

    if (confirm('Tem certeza que deseja excluir este item?')) {
      $.ajax({
        url: url,
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
          if (response.status == 'success') {
            item.remove();
            alert(response.message);
          } else {
            alert(response.message);
          }
        }
      });
    }
  });

  $(document).on('click', '.edit-item', function () {
    var item = $(this).closest('.opermat-item');
    var text = item.find('.opmat-text').text();
    var id = item.find('.opmat-id').text();
    var quantity = item.find('.opmat-quantity').text().replace('Qtde: ', '');
    var matId = item.data('idmat');

    // Preenche os campos da modal de edição com os valores do item
    $('#editItemName').text(text);
    $('#editItemId').val(id);
    $('#editItemId').data('id', matId);
    $('#quantityInput2').val(quantity);

    // Exibe a modal de edição
    $('#editModal').modal('show');
  });

  // Evento de clique para salvar a edição
  $('#saveOs3edit').on('click', function () {
    var id = $('#editItemId').val();
    var newQuantity = $('#quantityInput2').val();
    var idTarefa = $("#os3-modal").data('id');
    var url = $("#opermatModal").data('url');
    var matId = $('#editItemId').data('id');

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        idos3: id,
        qtde: newQuantity,
        tarefaId: idTarefa,
        matId: matId
      },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          // Atualiza a quantidade do item na lista
          $('.opermat-item').each(function () {
            if ($(this).data('idos3') == id) {
              $(this).find('.opmat-quantity').text(newQuantity);
              return false; // Sai do loop each
            }
          });

          alert(response.message);

          // Fecha a modal de edição
          $('#editModal').modal('hide');
        }
      }
    });
  });

  // Torna a modal de edição móvel
  $('#editModal').on('shown.bs.modal', function () {
    $(this).find('.modal-dialog').draggable({
      handle: ".modal-body"
    });
  });

  // Eventos para aumentar e diminuir a quantidade na modal de edição
  $('#decreaseQuantity2').on('click', function () {
    var quantity = parseInt($('#quantityInput2').val());
    if (quantity > 1) {
      $('#quantityInput2').val(quantity - 1);
    }
  });

  $('#increaseQuantity2').on('click', function () {
    var quantity = parseInt($('#quantityInput2').val());
    $('#quantityInput2').val(quantity + 1);
  });
});