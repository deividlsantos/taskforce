<?php if (!empty($dados)): ?>
    <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
        <thead>
            <tr>
                <th>Cliente</th>
                <th><?= str_to_single($empresa->labelFiliais); ?></th>
                <th>Tarefa</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Quantidade</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados as $medicao):
            ?>
                <tr>
                    <td><?= $medicao->cliente; ?></td>
                    <td><?= $medicao->obra; ?></td>
                    <td><?= $medicao->tarefa; ?></td>
                    <td><?= $medicao->inicio; ?></td>
                    <td><?= $medicao->fim; ?></td>
                    <td><?= fmt_numeros($medicao->qtde) . " " . $medicao->medida; ?></td>
                    <td><?= $medicao->responsavel; ?></td>
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
    });
</script>