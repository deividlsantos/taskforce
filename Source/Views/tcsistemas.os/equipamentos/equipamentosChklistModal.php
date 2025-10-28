<?php
$itensMarcados = [];
if (!empty($equipamento) && !empty($equipamento->id_chkitens)) {
    $itensMarcados = array_map('trim', explode(',', $equipamento->id_chkitens));
}
?>

<!-- Modal com animação accordion -->
<div id="chklstModalOverlay" class="chklst-overlay">
    <div class="chklst-container">
        <div class="chklst-header">
            <h3>Itens Checklist Equipamento <strong><?= $equipamento ? $equipamento->descricao : ''; ?></strong></h3>
            <button type="button" id="chklstCloseBtn" class="chklst-close">&times;</button>
        </div>
        <div class="chklst-content">
            <?php if (empty($gruposChklist)): ?>
                <div class="chklst-empty">
                    <p>Não existe nenhum grupo cadastrado.</p>
                </div>
            <?php else: ?>
                <?php foreach ($gruposChklist as $grupo): ?>
                    <div class="chklst-section">
                        <h4><?= htmlspecialchars($grupo->descricao) ?></h4>
                        <div class="chklst-grid">
                            <?php
                            $hasItems = false;
                            foreach ($itensChklist as $item):
                                if ($item->id_chkgrupo == $grupo->id):
                                    $hasItems = true;
                            ?>
                                    <div class="chklst-item">
                                        <input type="checkbox"
                                            id="chklst_item_<?= $item->id ?>"
                                            name="chklist_itens[]"
                                            value="<?= $item->id ?>"
                                            <?= in_array($item->id, $itensMarcados) ? 'checked' : '' ?>>
                                        <label for="chklst_item_<?= $item->id ?>">
                                            <?= htmlspecialchars($item->descricao) ?>
                                        </label>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                            <?php if (!$hasItems): ?>
                                <div class="chklst-empty">
                                    <p>Não contém nenhum item neste grupo.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="chklst-footer">
            <button type="button" id="chklstCancelBtn" class="chklst-btn-cancel">Ok</button>
        </div>
    </div>
</div>