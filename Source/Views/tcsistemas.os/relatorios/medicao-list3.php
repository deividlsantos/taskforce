<?php if (!empty($dados)): ?>
    <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
        <thead>
            <tr>
                <th><?= str_to_single($empresa->labelFiliais); ?></th>
                <th>Cliente</th>
                <th>Tarefa</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Qtde</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados as $obras):
                foreach ($obras['tarefas'] as $tarefa):
                    foreach ($tarefa['medicoes'] as $med):
            ?>
                        <tr>
                            <td><?= mb_strimwidth($obras['obra'], 0, 30, '...'); ?></td>
                            <td><?= mb_strimwidth($med['cliente'], 0, 30, '...'); ?></td>
                            <td><?= mb_strimwidth($tarefa['tarefa'], 0, 50, '...'); ?></td>
                            <td><?= date_fmt($med['data_inicio'], 'd/m/Y'); ?></td>
                            <td><?= date_fmt($med['data_fim'], 'd/m/Y'); ?></td>
                            <td style="text-align: end;"><?= fmt_numeros($med['qtde']) . " " . $tarefa['medida']; ?></td>
                            <td><?= mb_strimwidth($med['responsavel'], 0, 30, '...'); ?></td>
                        </tr>
            <?php
                    endforeach;
                endforeach;
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
    });
</script>