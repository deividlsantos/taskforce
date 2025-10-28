<?php
$this->layout("_theme", $front);
?>

<head>
    <script src="<?= url("Source/Views/js/timeline.js"); ?>"></script>
</head>

<div class="container mt-4">
    <h1>Agenda do Dia</h1>
    <!-- Selecione o Dia -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="datePicker" class="form-label">Escolha a Data:</label>
            <input type="date" class="form-control" id="datePicker" value="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>

    <!-- Tabela Dinâmica com Barra de Rolagem Horizontal -->
    <div class="table-wrapper">
        <!-- Linha vermelha representando o horário atual -->
        <div class="current-time-line" id="currentTimeLine"></div>
        <table id="scheduleTable" class="table table-bordered table-timeline">
            <thead>
                <tr>
                    <th>Pessoas</th>
                    <!-- Horários serão preenchidos dinamicamente -->
                </tr>
            </thead>
            <tbody>
                <!-- Linhas de pessoas serão preenchidas dinamicamente -->
            </tbody>
        </table>
    </div>

    <div id="dataContainer"
        data-os1='<?= htmlspecialchars(json_encode($os1), ENT_QUOTES, 'UTF-8') ?>'
        data-os2='<?= htmlspecialchars(json_encode($os2), ENT_QUOTES, 'UTF-8') ?>'
        data-func='<?= htmlspecialchars(json_encode($func), ENT_QUOTES, 'UTF-8') ?>'>
    </div>

    <!-- Eventos Disponíveis -->
    <div class="mt-5">
        <h2>Eventos</h2>
        <div id="events" class="d-flex flex-wrap">
            <!-- Eventos que poderão ser arrastados -->
        </div>
    </div>
</div>

<!-- Modal para editar evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Editar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="mb-3">
                        <label for="eventName" class="form-label">Nome do Evento</label>
                        <input type="text" class="form-control" id="eventName">
                    </div>
                    <div class="mb-3">
                        <label for="eventTime" class="form-label">Horário</label>
                        <input type="time" class="form-control" id="eventTime">
                    </div>
                    <div class="mb-3">
                        <label for="eventPerson" class="form-label">Pessoa</label>
                        <select id="eventPerson" class="form-select">
                            <!-- As opções serão preenchidas dinamicamente -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="saveEventChanges">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>
<?= $this->section("js"); ?>