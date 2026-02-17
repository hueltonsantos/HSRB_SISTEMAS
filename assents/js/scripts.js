/**
 * Scripts para o Sistema de Encaminhamento Clínico
 */

(function ($) {
  "use strict";

  // Ação de toggle para o sidebar
  // Ação de toggle para o sidebar
  $("#sidebarToggle, #sidebarToggleTop").on("click", function (e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");

    // Mobile backdrop logic
    if ($(window).width() < 768) {
      if ($(".sidebar").hasClass("toggled")) {
        $(".sidebar-backdrop").addClass("show");
      } else {
        $(".sidebar-backdrop").removeClass("show");
      }
    }

    if ($(".sidebar").hasClass("toggled")) {
      $(".sidebar .collapse").collapse("hide");
    }
  });

  // Fecha o sidebar ao clicar no backdrop (mobile)
  $(document).on("click", ".sidebar-backdrop", function () {
    if ($(window).width() < 768) {
      $("body").removeClass("sidebar-toggled");
      $(".sidebar").removeClass("toggled");
      $(".sidebar-backdrop").removeClass("show");
    }
  });

  // Fecha qualquer menu aberto quando a janela é redimensionada abaixo de 768px
  $(window).resize(function () {
    if ($(window).width() < 768) {
      $(".sidebar .collapse").collapse("hide");
    } else {
      // Reset mobile specific states on desktop
      $(".sidebar-backdrop").removeClass("show");
      // Ensure sidebar is visible on desktop if it was hidden by mobile logic,
      // but keep 'toggled' class if user intended it to be collapsed.
      // Actually, styles handle visibility, we just need to remove backdrop.
    }

    // Alterna o estado do sidebar toggled quando a janela é redimensionada abaixo de 480px
    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      // $(".sidebar").addClass("toggled"); // Don't auto-toggle class on logic, let CSS handle initial state or user interaction
      // $(".sidebar .collapse").collapse("hide");
    }
  });

  // Previne o wrapper de encolher quando o menu fixo de dropdown está visível
  $("body.fixed-nav .sidebar").on(
    "mousewheel DOMMouseScroll wheel",
    function (e) {
      if ($(window).width() > 768) {
        var e0 = e.originalEvent,
          delta = e0.wheelDelta || -e0.detail;
        this.scrollTop += (delta < 0 ? 1 : -1) * 30;
        e.preventDefault();
      }
    },
  );

  // Botão de voltar ao topo
  $(document).on("scroll", function () {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $(".scroll-to-top").fadeIn();
    } else {
      $(".scroll-to-top").fadeOut();
    }
  });

  // Animação suave ao clicar no botão de voltar ao topo
  $(document).on("click", "a.scroll-to-top", function (e) {
    var $anchor = $(this);
    $("html, body")
      .stop()
      .animate(
        {
          scrollTop: $($anchor.attr("href")).offset().top,
        },
        500,
        "easeInOutExpo",
      );
    e.preventDefault();
  });

  // Inicializa os tooltips do Bootstrap
  $('[data-toggle="tooltip"]').tooltip();

  // Inicializa os popovers do Bootstrap
  $('[data-toggle="popover"]').popover();

  // Fecha outros dropdowns ao abrir um novo
  $(".dropdown").on("show.bs.dropdown", function () {
    var $this = $(this);
    $(this)
      .siblings(".dropdown")
      .each(function () {
        if ($(this).hasClass("show") && $(this) !== $this) {
          $(this).dropdown("toggle");
        }
      });
  });

  // Fecha alertas automaticamente após 5 segundos
  window.setTimeout(function () {
    $(".alert-dismissible")
      .fadeTo(500, 0)
      .slideUp(500, function () {
        $(this).remove();
      });
  }, 5000);

  // Confirmação de exclusão
  $(".btn-delete").on("click", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var name = $(this).data("name");
    var form = $(this).closest("form");

    // Atualiza o modal com as informações corretas
    $("#deleteModal .modal-body p").text(
      'Deseja realmente excluir "' + name + '"?',
    );

    // Configura o botão de confirmação para submeter o formulário
    $("#confirmDelete")
      .off("click")
      .on("click", function () {
        form.submit();
      });

    // Exibe o modal
    $("#deleteModal").modal("show");
  });

  // Máscaras para formulários (necessário jQuery Mask Plugin)
  if ($.fn.mask) {
    $(".date").mask("00/00/0000");
    $(".time").mask("00:00:00");
    $(".date_time").mask("00/00/0000 00:00:00");
    $(".cep").mask("00000-000");
    $(".phone").mask("(00) 0000-0000");
    $(".phone_with_ddd").mask("(00) 0000-0000");
    $(".phone_us").mask("(000) 000-0000");
    $(".mixed").mask("AAA 000-S0S");
    $(".cpf").mask("000.000.000-00", { reverse: true });
    $(".cnpj").mask("00.000.000/0000-00", { reverse: true });
    $(".money").mask("000.000.000.000.000,00", { reverse: true });
    $(".money2").mask("#.##0,00", { reverse: true });

    // Celular com 9 dígitos
    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, "").length === 11
          ? "(00) 00000-0000"
          : "(00) 0000-00009";
      },
      spOptions = {
        onKeyPress: function (val, e, field, options) {
          field.mask(SPMaskBehavior.apply({}, arguments), options);
        },
      };

    $(".celphone").mask(SPMaskBehavior, spOptions);
  }

  // Ativa o datepicker do Bootstrap
  // if ($.fn.datepicker) {
  //     $('.datepicker').datepicker({
  //         format: 'dd/mm/yyyy',
  //         language: 'pt-BR',
  //         autoclose: true,
  //         todayHighlight: true
  //     });
  // }

  // Ativa o datepicker
  if ($.fn.datepicker) {
    $(".datepicker, .data-mask").datepicker({
      format: "dd/mm/yyyy",
      language: "pt-BR",
      autoclose: true,
      todayHighlight: true,
    });
  }
  // Consulta CEP via ViaCEP API
  $("#cep").on("blur", function () {
    var cep = $(this).val().replace(/\D/g, "");

    if (cep.length === 8) {
      $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function (data) {
        if (!data.erro) {
          $("#endereco").val(data.logradouro);
          $("#bairro").val(data.bairro);
          $("#cidade").val(data.localidade);
          $("#estado").val(data.uf);
          $("#numero").focus();
        }
      });
    }
  });

  // Ativa o select2 para selects avançados
  if ($.fn.select2) {
    $(".select2").select2({
      language: "pt-BR",
      width: "100%",
    });
  }

  // Configurações para tabelas DataTable
  if ($.fn.DataTable) {
    $(".datatable").DataTable({
      language: {
        sEmptyTable: "Nenhum registro encontrado",
        sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
        sInfoFiltered: "(Filtrados de _MAX_ registros)",
        sInfoPostFix: "",
        sInfoThousands: ".",
        sLengthMenu: "_MENU_ resultados por página",
        sLoadingRecords: "Carregando...",
        sProcessing: "Processando...",
        sZeroRecords: "Nenhum registro encontrado",
        sSearch: "Pesquisar",
        oPaginate: {
          sNext: "Próximo",
          sPrevious: "Anterior",
          sFirst: "Primeiro",
          sLast: "Último",
        },
        oAria: {
          sSortAscending: ": Ordenar colunas de forma ascendente",
          sSortDescending: ": Ordenar colunas de forma descendente",
        },
      },
      responsive: true,
    });
  }

  // Configurações de validação de formulários
  if ($.fn.validate) {
    $("form.validate").validate({
      errorElement: "div",
      errorClass: "invalid-feedback",
      highlight: function (element) {
        $(element).addClass("is-invalid");
      },
      unhighlight: function (element) {
        $(element).removeClass("is-invalid");
      },
      errorPlacement: function (error, element) {
        error.insertAfter(element);
      },
    });
  }

  // Exibe/Oculta campo de senha do convênio
  $("#tem_convenio").on("change", function () {
    if ($(this).is(":checked")) {
      $("#convenio_group").slideDown();
    } else {
      $("#convenio_group").slideUp();
    }
  });

  // Inicialização do summernote para campos de texto formatado
  if ($.fn.summernote) {
    $(".summernote").summernote({
      height: 200,
      lang: "pt-BR",
      toolbar: [
        ["style", ["style"]],
        ["font", ["bold", "underline", "clear"]],
        ["color", ["color"]],
        ["para", ["ul", "ol", "paragraph"]],
        ["table", ["table"]],
        ["insert", ["link"]],
        ["view", ["fullscreen", "codeview", "help"]],
      ],
    });
  }

  // Funções para o módulo de agendamentos

  // Carrega as especialidades com base na clínica selecionada
  $("#clinica_id").on("change", function () {
    var clinicaId = $(this).val();
    if (clinicaId) {
      $.ajax({
        url: "index.php?module=agendamentos&action=get_especialidades",
        type: "POST",
        data: { clinica_id: clinicaId },
        dataType: "json",
        success: function (response) {
          var options = '<option value="">Selecione...</option>';
          var data = [];

          if (response && response.success && response.data) {
            data = response.data;
          } else if (Array.isArray(response)) {
            data = response;
          } else if (response && response.data) {
            data = response.data;
          }

          $.each(data, function (key, value) {
            var id = value.id || value.Id || value.ID;
            var nome =
              value.nome ||
              value.Nome ||
              value.NOME ||
              value.especialidade ||
              "Sem nome";
            if (id) {
              options += '<option value="' + id + '">' + nome + "</option>";
            }
          });
          $("#especialidade_id").html(options).prop("disabled", false);
        },
        error: function (xhr, status, error) {
          console.error("Erro AJAX especialidades:", status, error);
        },
      });
    } else {
      $("#especialidade_id")
        .html('<option value="">Selecione uma clínica primeiro</option>')
        .prop("disabled", true);
    }
  });

  // Carrega os horários disponíveis com base na data e especialidade selecionada
  $("#data_consulta, #especialidade_id").on("change", function () {
    var data = $("#data_consulta").val();
    var especialidadeId = $("#especialidade_id").val();
    var clinicaId = $("#clinica_id").val();

    if (data && especialidadeId && clinicaId) {
      $.ajax({
        url: "index.php?module=agendamentos&action=get_horarios",
        type: "POST",
        data: {
          data_consulta: data,
          especialidade_id: especialidadeId,
          clinica_id: clinicaId,
        },
        dataType: "json",
        success: function (res) {
          var options = '<option value="">Selecione...</option>';
          var horarios = Array.isArray(res) ? res : res.data || [];

          $.each(horarios, function (key, value) {
            options += '<option value="' + value + '">' + value + "</option>";
          });
          $("#hora_consulta").html(options).prop("disabled", false);
        },
        error: function (xhr, status, error) {
          console.error("Erro AJAX horários:", status, error);
        },
      });
    } else {
      $("#hora_consulta")
        .html('<option value="">Preencha todos os campos</option>')
        .prop("disabled", true);
    }
  });

  // Implementação do recurso de busca rápida de pacientes
  $("#busca_paciente").on("keyup", function () {
    var termo = $(this).val();

    if (termo.length >= 3) {
      $.ajax({
        url: "index.php?module=pacientes&action=ajax_search",
        type: "POST",
        data: { termo: termo },
        dataType: "json",
        success: function (data) {
          var html = "";
          var pacientes = Array.isArray(data) ? data : data.data || [];

          if (pacientes.length > 0) {
            $.each(pacientes, function (key, paciente) {
              var id = paciente.id || paciente.Id || paciente.ID;
              var nome =
                paciente.nome || paciente.Nome || paciente.NOME || "Sem nome";
              var cpf = paciente.cpf || paciente.Cpf || paciente.CPF || "";
              var celular =
                paciente.celular ||
                paciente.Celular ||
                paciente.CELULAR ||
                paciente.telefone ||
                "";

              html += '<div class="resultado-item" data-id="' + id + '">';
              html += "<strong>" + nome + "</strong><br>";
              html += "CPF: " + cpf + " | Tel: " + celular;
              html += "</div>";
            });
          } else {
            html =
              '<div class="sem-resultados">Nenhum paciente encontrado</div>';
          }

          $("#resultados_busca").html(html).show();
        },
      });
    } else {
      $("#resultados_busca").hide();
    }
  });

  // Seleciona um paciente da busca rápida
  $(document).on("click", ".resultado-item", function () {
    var id = $(this).data("id");
    var nome = $(this).find("strong").text();

    $("#paciente_id").val(id);
    $("#busca_paciente").val(nome);
    $("#resultados_busca").hide();
  });

  // Relógio e Data em tempo real
  function updateClock() {
    var now = new Date();
    var options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    };
    var clockElement = document.getElementById("clock-date");
    if (clockElement) {
      // Formata a primeira letra da semana como maiúscula
      var dateStr = now.toLocaleDateString("pt-BR", options);
      clockElement.innerText =
        dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
    }
  }

  if (document.getElementById("clock-date")) {
    setInterval(updateClock, 1000);
    updateClock();
  }
})(jQuery); // Fim da função principal
