<?php if (!empty($dados)): ?>
    <div class="fcad-form-row">
        <a href="<?= url("relatorios/pdffunc/" . ll_encode($filtro)) . (!empty($datai) ? "/" . $datai . "/" . $dataf : ""); ?>"
            class="btn btn-secondary list-edt btn-relatorio"
            target="_blank">
            <i class="fa fa-file-pdf"></i>
        </a>
    </div>
    <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
        <thead>
            <tr>
                <th>Início</th>
                <th>Fim</th>
                <th>Cliente</th>
                <th><?= str_to_single($empresa->labelFiliais); ?></th>
                <th>Obs</th>
                <th>Qtde</th>
                <th>Vlr.Unit.(R$)</th>
                <th>Vlr.Total (R$)</th>
                <?php
                if ($filtro == "0"):
                ?>
                    <th>Responsável</th>
                <?php
                endif;
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados as $medicao):                
            ?>
                <tr>
                    <td data-id="<?= $medicao->id; ?>"><?= $medicao->inicio; ?></td>
                    <td><?= $medicao->fim; ?></td>
                    <td><?= mb_strimwidth($medicao->cliente, 0, 30, '...'); ?></td>
                    <td><?= mb_strimwidth($medicao->obra, 0, 30, '...'); ?></td>
                    <td><?= mb_strimwidth($medicao->obs, 0, 30, '...'); ?></td>
                    <td><?= fmt_numeros($medicao->qtde) . " " . $medicao->medida; ?></td>
                    <td>R$ <?= moedaBR($medicao->unitario); ?></td>
                    <td>R$ <?= moedaBR($medicao->total); ?></td>
                    <?php
                    if ($filtro == "0"):
                    ?>
                        <td data-id="<?= $medicao->id_operador; ?>"><?= $medicao->responsavel; ?></td>
                    <?php
                    endif;
                    ?>
                </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhum resultado encontrado.</p>
<?php endif; ?>

<script>
    $(document).ready(function() {

        $.tablesorter.addParser({
            id: 'dataBR',
            is: function(s) {
                return /^\d{2}\/\d{2}\/\d{4}$/.test(s); // Verifica apenas o formato dd/mm/aaaa
            },
            format: function(s) {
                var datePart = s.split('/');
                return new Date(datePart[2], datePart[1] - 1, datePart[0]).getTime();
            },
            type: 'numeric'
        });


        function tableSorter(tabela, options) {
            $(tabela).tablesorter($.extend({
                headers: {}
            }, options)).bind("sortEnd", function() {
                $(this).find("th").removeClass("asc desc");
                $(this).find("th").each(function() {
                    if ($(this).hasClass("tablesorter-headerAsc")) {
                        $(this).addClass("asc");
                    } else if ($(this).hasClass("tablesorter-headerDesc")) {
                        $(this).addClass("desc");
                    }
                });
            });
        }

        tableSorter(".tab-rel-medicao-cli", {
            headers: {
                3: {
                    sorter: 'dataBR'
                },
                4: {
                    sorter: 'dataBR'
                }
            }
        });


        $('#printButton').click(function() {
            var filtro = $(this).data('filtro');
            var tableData = [];
            var url = $(this).data('url');

            $('.tab-rel-medicao-cli tbody tr').each(function(row, tr) {
                tableData[row] = {
                    "id": $(tr).find('td:eq(0)').data('id'),
                    "inicio": $(tr).find('td:eq(0)').text(),
                    "fim": $(tr).find('td:eq(1)').text(),
                    "cliente": $(tr).find('td:eq(2)').text(),
                    "obra": $(tr).find('td:eq(3)').text(),
                    "obs": $(tr).find('td:eq(4)').text(),
                    "qtde": $(tr).find('td:eq(5)').text(),
                    "vlr_unit": $(tr).find('td:eq(6)').text(),
                    "vlr_total": $(tr).find('td:eq(7)').text()
                };
                if ($(tr).find('td:eq(8)').length) {
                    tableData[row]["responsavel"] = $(tr).find('td:eq(8)').data('id');
                }
            });

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    tableData: tableData,
                    filtro: filtro
                },
                success: function(response) {
                    window.open(response, '_blank');
                }
            });

        });
    });
</script>