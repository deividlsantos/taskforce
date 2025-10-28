<?php if (!empty($chkItens)):
    // Agrupar itens por grupo
    $itensPorGrupo = [];
    foreach ($chkItens as $item) {
        $itensPorGrupo[$item->grupo][] = $item;
    }

    // Ordenar cada grupo pelo id_chkitens em ordem crescente
    foreach ($itensPorGrupo as $grupoId => $itens) {
        usort($itensPorGrupo[$grupoId], function ($a, $b) {
            return $a->id_chkitens <=> $b->id_chkitens;
        });
    }

    // Criar array para fácil verificação de itens removidos
    $itensRemovidosIds = !empty($itens_sobrando) ? array_values($itens_sobrando) : [];
?>

    <?php foreach ($itensPorGrupo as $grupoId => $itensDoGrupo): ?>
        <!-- Cabeçalho do Grupo -->
        <div class="grupo-header">
            <h4><?= htmlspecialchars($itensDoGrupo[0]->descGrupo) ?></h4>
        </div>

        <!-- Tabela do Grupo -->
        <table class="table table-striped table-bordered chkitens-table">
            <thead>
                <tr>
                    <th hidden>ID</th>
                    <th width="30%">Item</th>
                    <th width="15%">Conforme</th>
                    <th width="15%">Não Conforme</th>
                    <th width="15%">Não Se Aplica</th>
                    <th width="45%">Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($itensDoGrupo as $item):
                    $itemRemovido = in_array($item->id_chkitens, $itensRemovidosIds);
                    $rowClass = $itemRemovido ? 'table-warning item-removido' : '';
                    $disabledAttr = $itemRemovido ? 'disabled' : '';
                    $tooltipAttr = $itemRemovido ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Este item foi excluído do equipamento"' : '';
                ?>
                    <tr class="<?= $rowClass ?>" <?= $tooltipAttr ?>>
                        <!-- ID do Item -->
                        <td hidden>
                            <?= $item->id ?>
                        </td>
                        <td>
                            <?= $item->descricao ?>
                            <?php if ($itemRemovido): ?>
                                <i class="fa fa-exclamation-triangle text-warning ms-2"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Item excluído do equipamento"></i>
                            <?php endif; ?>
                        </td>

                        <!-- Checkbox Conforme (Status = 1) -->
                        <td class="text-center">
                            <input type="checkbox"
                                name="checklist_<?= $item->id ?>_status"
                                value="1"
                                <?= ($item->status == 1) ? 'checked' : '' ?>
                                <?= $disabledAttr ?>
                                onchange="updateChecklistStatus(<?= $item->id ?>, 1, this)">
                        </td>

                        <!-- Checkbox Não Conforme (Status = 2) -->
                        <td class="text-center">
                            <input type="checkbox"
                                name="checklist_<?= $item->id ?>_status"
                                value="2"
                                <?= ($item->status == 2) ? 'checked' : '' ?>
                                <?= $disabledAttr ?>
                                onchange="updateChecklistStatus(<?= $item->id ?>, 2, this)">
                        </td>

                        <!-- Checkbox Não Aplicável (Status = 3) -->
                        <td class="text-center">
                            <input type="checkbox"
                                name="checklist_<?= $item->id ?>_status"
                                value="3"
                                <?= ($item->status == 3) ? 'checked' : '' ?>
                                <?= $disabledAttr ?>
                                onchange="updateChecklistStatus(<?= $item->id ?>, 3, this)">
                        </td>

                        <!-- Input para Observações -->
                        <td>
                            <input type="text"
                                class="form-control"
                                name="checklist_<?= $item->id ?>_obs"
                                value="<?= htmlspecialchars($item->obs) ?>"
                                placeholder="<?= $itemRemovido ? 'Item excluído - não editável' : 'Digite observações...' ?>"
                                <?= $disabledAttr ?>
                                onchange="updateChecklistObs(<?= $item->id ?>, this.value)">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Espaçamento entre grupos -->
        <div class="mb-4"></div>

    <?php endforeach; ?>

    <!-- Seção de Observações Gerais -->
    <div class="mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa fa-comment-alt me-2"></i>
                    Observações Gerais
                </h5>
            </div>
            <div class="card-body">
                <textarea
                    class="form-control"
                    name="chkobs"
                    id="chkobs"
                    rows="3"
                    placeholder="Digite suas observações gerais sobre o checklist..."><?= isset($obs) ? trim($obs) : '' ?></textarea>
            </div>
        </div>
    </div>

    <script>
        function updateChecklistStatus(itemId, status, checkbox) {
            // Desmarcar outros checkboxes do mesmo item
            const checkboxes = document.querySelectorAll(`input[name="checklist_${itemId}_status"]`);
            checkboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false;
                }
            });

            // Aqui você pode adicionar uma chamada AJAX para salvar o status
            // updateChecklistItem(itemId, status, null);
        }

        function updateChecklistObs(itemId, obs) {
            // Aqui você pode adicionar uma chamada AJAX para salvar as observações
            // updateChecklistItem(itemId, null, obs);
        }

        // Função para atualizar item via AJAX (implementar conforme necessário)
        function updateChecklistItem(itemId, status, obs) {
            // Implementar chamada AJAX para salvar dados
            console.log('Atualizando item:', itemId, 'Status:', status, 'Obs:', obs);
        }

        // Inicializar tooltips do Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .item-removido {
            opacity: 0.6;
        }

        .item-removido input {
            cursor: not-allowed;
        }

        .item-removido td {
            background-color: #fff3cd !important;
        }
    </style>

<?php else: ?>
    <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle"></i>
        Nenhum item de checklist encontrado para este equipamento.
    </div>
<?php endif; ?>