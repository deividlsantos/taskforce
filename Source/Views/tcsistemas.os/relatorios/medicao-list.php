<div class="accordion" id="accordionMedicoes">
    <?php if (!empty($os1)): ?>
        <table class="table table-hover bordered tabela-relatorio">
            <thead>
                <tr>
                    <th>OS</th>
                    <th>Status</th>
                    <th>Cliente</th>
                    <th><?= str_to_single($empresa->labelFiliais); ?></th>
                    <th>Tarefas <i class="fa-solid fa-circle-info tooltip-rel" data-text="Quantidade de tarefas que tem medições"></i></th>
                    <th>Realizadas <i class="fa-solid fa-circle-info tooltip-rel" data-text="Quantidade de medições realizadas na respectiva OS."></i></th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($os1 as $index => $ordem):
                ?>
                    <tr>
                        <td><?= $ordem["id"] ?></td>
                        <td><?= $status[$ordem["status"]]; ?></td>
                        <td><?= mb_strimwidth($clientes[$ordem["cliente"]], 0, 30, '...'); ?></td>
                        <td><?= !empty($obras[$ordem["obra"]]) ? mb_strimwidth($obras[$ordem["obra"]], 0, 30, '...') : ""; ?></td>
                        <td style="text-align: center;"><?= $ordem["totalTarefas"] ?></td>
                        <td style="text-align: center;"><?= $ordem["realizadas"] ?></td>
                        <td>
                            <button class="btn toggle-btn" data-target="#collapse<?= $index ?>">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                        </td>
                        <td>
                            <a href="<?= url("relatorios/pdf/" . ll_encode($ordem['id'])); ?>" target="_blank" data-id="<?= $ordem['id'] ?>" class="btn btn-secondary list-edt btn-relatorio"><i class="fa fa-file-pdf"></i></a>
                        </td>
                        <td></td>
                    </tr>
                    <tr id="collapse<?= $index ?>" class="accordion-row" style="display: none; width: 100%;">
                        <td colspan="9">
                            <div class="accordion-content" style="overflow: hidden; height: 0; transition: height 0.4s ease;">
                                <table width="100%" class="tabela-relatorio-item">
                                    <?php
                                    $index = 0;
                                    foreach ($ordem["tarefas"] as $tarefa):
                                        if ($tarefa->mede):
                                            $index++;
                                    ?>
                                            <tr style="margin-top: 10px !important; border-top: 1px solid #ccc;">
                                                <th colspan="6">TAREFA #<?= $tarefa->id . ' - ' . $tarefa->nome . '(' . $index . ')'; ?> - TOTAL ORÇADO: <?= $tarefa->qtde . " " . $tarefa->unidade; ?> - VALOR TOTAL: R$<?= moedaBR($tarefa->vtotal); ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2"></th>
                                                <th>Período</th>
                                                <th>Quantidade (<?= $tarefa->unidade ?>)</th>
                                                <th>Observação</th>
                                                <th>Responsável</th>
                                            </tr>
                                            <?php
                                            if (!empty($tarefa->medicoes)):
                                                foreach ($tarefa->medicoes as $medicao):
                                            ?>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td><?= date_fmt($medicao->data->datai, "d/m/Y") . ' a ' . date_fmt($medicao->data->dataf, "d/m/Y"); ?></td>
                                                        <td><?= fmt_numeros($medicao->data->qtde); ?></td>
                                                        <td><?= mb_strimwidth($medicao->data->obs, 0, 30, '...'); ?></td>
                                                        <td><?= mb_strimwidth($medicao->data->responsavel, 0, 30, '...'); ?></td>
                                                    </tr>
                                                <?php
                                                endforeach;
                                            else:
                                                ?>
                                                <tr>
                                                    <td colspan="6" style="text-align: center;">Nenhuma medição encontrada.</td>
                                                </tr>
                                    <?php
                                            endif;
                                        endif;
                                    endforeach;
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum resultado encontrado.</p>
    <?php endif; ?>
</div>
<script>
    $(document).ready(function() {
        $(".toggle-btn").click(function() {
            var target = $(this).data("target");
            var $row = $(target);
            var $content = $row.find(".accordion-content");
            var $button = $(this);

            // Fecha todas as outras accordions abertas
            $(".accordion-row:visible").not($row).each(function() {
                var $otherRow = $(this);
                var $otherContent = $otherRow.find(".accordion-content");
                var $otherButton = $otherRow.prev("tr").find(".toggle-btn");

                $otherContent.css("height", "0"); // Fecha suavemente
                setTimeout(() => {
                    $otherRow.hide(); // Esconde a linha após a animação
                    $otherButton.closest("tr").css("font-weight", "normal"); // Remove negrito do texto
                }, 400);
            });

            // Alterna o estado da accordion clicada
            if ($row.is(":visible")) {
                $content.css("height", "0"); // Fecha suavemente
                setTimeout(() => {
                    $row.hide();
                    $button.closest("tr").css("font-weight", "normal"); // Remove negrito
                }, 400);
            } else {
                $row.show();
                var contentHeight = $content.prop("scrollHeight") + "px"; // Calcula altura do conteúdo
                $content.css("height", contentHeight); // Crescimento suave
                $button.closest("tr").css("font-weight", "bold"); // Adiciona negrito
            }
        });
    });
</script>