<div class="accordion" id="accordionMedicoes">
    <?php if (!empty($dados)): ?>
        <table class="table table-hover bordered tabela-relatorio">
            <thead>
                <tr>
                <th><?= str_to_single($empresa->labelFiliais); ?></th>
                    <th>Endereço</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($dados)):
                    foreach ($dados as $index => $obra):

                ?>
                        <tr>
                            <td><?= $obra['obra'] ?></td>
                            <td><?= $obra['endereco']; ?></td>
                            <td>
                                <button class="btn toggle-btn" data-target="#collapse<?= $index ?>">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </td>
                            <td>
                                <a href="#" data-id="<?= $index ?>" data-url="<?= url("relatorios/pdfobra/" . ll_encode($index)) . (!empty($obra['datai']) ? "/" . $obra['datai'] . "/" . $obra['dataf'] : ""); ?>"
                                    class="btn btn-secondary list-edt btn-relatorio">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                            </td>
                            <td></td>
                        </tr>
                        <tr id="collapse<?= $index ?>" class="accordion-row" style="display: none; width: 100%;">
                            <td colspan="9">
                                <div class="accordion-content" style="overflow: hidden; height: 0; transition: height 0.4s ease;">
                                    <table width="100%" class="tabela-relatorio-item">
                                        <?php
                                        $contador = 0;
                                        foreach ($obra['tarefas'] as $tarefa):
                                        ?>
                                            <tr style="margin-top: 10px !important; border-top: 1px solid #ccc;">
                                                <th colspan="6"><?= $tarefa['tarefa']; ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2"></th>
                                                <th width="20%">Período</th>
                                                <th style="text-align: end;">Qtde</th>
                                                <th>Observação</th>
                                                <th>Responsável</th>
                                                <th>OS</th>
                                            </tr>
                                            <?php
                                            foreach ($tarefa['medicoes'] as $medicao):
                                            ?>
                                                <tr>
                                                    <td colspan="2"></td>
                                                    <td><?= date_fmt($medicao['data_inicio'], "d/m/Y") . " a " . date_fmt($medicao['data_fim'], "d/m/Y"); ?></td>
                                                    <td style="text-align: end;"><?= fmt_numeros($medicao['qtde']) . " " . $tarefa['medida']; ?></td>
                                                    <td><?= $medicao['obs']; ?></td>
                                                    <td><?= $medicao['responsavel']; ?></td>
                                                    <td><?= $medicao['os']; ?></td>
                                                </tr>
                                        <?php
                                            endforeach;
                                        endforeach;
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                <?php
                    endforeach;
                endif; ?>
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

    $(document).ready(function() {
        $(".btn-relatorio").on("click", function(e) {
            e.preventDefault();
            let url = $(this).data("url"); // Obtém a URL original do botão
            $("#confirmarTema").data("url", url);
            $("#modalTemaPdf").modal("show");
        });

        $("#confirmarTema").on("click", function() {
            let tema = $("#temaPdf").val().trim();
            let url = $(this).data("url");

            if (tema !== "") {
                url += (url.includes("?") ? "&" : "?") + "tema=" + encodeURIComponent(tema); // Adiciona o tema na URL
            }

            window.open(url, "_blank");
            $("#modalTemaPdf").modal("hide");
        });
    });
</script>