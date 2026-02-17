<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Calendário de Agendamentos</h1>
    
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Visualização de Calendário</h6>
            <div>
                <a href="index.php?module=agendamentos&action=list" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-list"></i> Visualizar como Lista
                </a>
                <a href="index.php?module=agendamentos&action=new" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Novo Agendamento
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros de busca -->
            <div class="mb-4">
                <form action="index.php" method="get" class="form-inline">
                    <input type="hidden" name="module" value="agendamentos">
                    <input type="hidden" name="action" value="calendar">
                    
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="clinica_id" class="sr-only">Clínica</label>
                        <select class="form-control" id="clinica_id" name="clinica_id">
                            <option value="">Todas as Clínicas</option>
                            <?php foreach ($clinicas as $clinica): ?>
                                <option value="<?php echo $clinica['id']; ?>" <?php echo (isset($_GET['clinica_id']) && $_GET['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($clinica['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="especialidade_id" class="sr-only">Especialidade</label>
                        <select class="form-control" id="especialidade_id" name="especialidade_id">
                            <option value="">Todas as Especialidades</option>
                            <?php foreach ($especialidades as $especialidade): ?>
                                <option value="<?php echo $especialidade['id']; ?>" <?php echo (isset($_GET['especialidade_id']) && $_GET['especialidade_id'] == $especialidade['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($especialidade['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="status_agendamento" class="sr-only">Status</label>
                        <select class="form-control" id="status_agendamento" name="status_agendamento">
                            <option value="">Todos os Status</option>
                            <?php foreach ($statusAgendamento as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($_GET['status_agendamento']) && $_GET['status_agendamento'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                    <a href="index.php?module=agendamentos&action=calendar" class="btn btn-secondary mb-2 ml-2">Limpar</a>
                </form>
            </div>
            
            <!-- Calendário -->
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Link para o FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<!-- Link para o FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados dos eventos
    var eventos = <?php echo $eventosJson; ?>;
    
    // Elemento do calendário
    var calendarEl = document.getElementById('calendar');
    
    // Inicialização do calendário
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: eventos,
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 2,
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6], // Segunda a sábado
            startTime: '08:00',
            endTime: '18:00'
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        dayMaxEvents: true,
        eventClick: function(info) {
            // Os links já estão configurados nos eventos
        },
        dateClick: function(info) {
            // Redireciona para a tela de novo agendamento com a data pré-selecionada
            var formatarData = function(data) {
                var dia = data.getDate().toString().padStart(2, '0');
                var mes = (data.getMonth() + 1).toString().padStart(2, '0');
                var ano = data.getFullYear();
                return dia + '/' + mes + '/' + ano;
            };
            
            window.location.href = 'index.php?module=agendamentos&action=new&data_consulta=' + formatarData(info.date);
        },
        eventDidMount: function(info) {
            // Adiciona tooltip com informações detalhadas
            $(info.el).tooltip({
                title: 'Paciente: ' + info.event.title + 
                      '<br>Clínica: ' + info.event.extendedProps.clinica_nome + 
                      '<br>Status: ' + info.event.extendedProps.status,
                html: true,
                placement: 'top',
                container: 'body'
            });
        }
    });
    
    // Renderiza o calendário
    calendar.render();
});
</script>

<style>
/* Estilos adicionais para o calendário */
.fc-event {
    cursor: pointer;
}

.fc-day-today {
    background-color: #f8f9fa !important;
}

/* Cores para os diferentes status */
.fc-event.status-agendado {
    background-color: #3788d8 !important;
    border-color: #3788d8 !important;
}

.fc-event.status-confirmado {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}

.fc-event.status-realizado {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-event.status-cancelado {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
</style>