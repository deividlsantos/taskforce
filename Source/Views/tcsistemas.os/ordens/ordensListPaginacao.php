<div class="os1pg-container">
    <div class="os1pg-info">
        <span>Mostrando <?= (($paginacao['paginaAtual'] - 1) * $paginacao['registrosPorPagina']) + 1; ?> a
            <?= min($paginacao['paginaAtual'] * $paginacao['registrosPorPagina'], $paginacao['totalRegistros']); ?>
            de <?= $paginacao['totalRegistros']; ?> registros</span>
    </div>

    <nav class="os1pg-nav">
        <ul class="os1pg-pagination">
            <!-- Primeira página -->
            <?php if ($paginacao['paginaAtual'] > 1): ?>
                <li class="os1pg-item">
                    <a class="os1pg-link" href="#" data-page="1">
                        <i class="fa fa-angle-double-left"></i>
                    </a>
                </li>
                <li class="os1pg-item">
                    <a class="os1pg-link" href="#" data-page="<?= $paginacao['paginaAtual'] - 1; ?>">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Páginas numeradas -->
            <?php
            $inicio = max(1, $paginacao['paginaAtual'] - 2);
            $fim = min($paginacao['totalPaginas'], $paginacao['paginaAtual'] + 2);

            for ($i = $inicio; $i <= $fim; $i++): ?>
                <li class="os1pg-item <?= ($i == $paginacao['paginaAtual']) ? 'os1pg-active' : ''; ?>">
                    <a class="os1pg-link" href="#" data-page="<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Última página -->
            <?php if ($paginacao['paginaAtual'] < $paginacao['totalPaginas']): ?>
                <li class="os1pg-item">
                    <a class="os1pg-link" href="#" data-page="<?= $paginacao['paginaAtual'] + 1; ?>">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
                <li class="os1pg-item">
                    <a class="os1pg-link" href="#" data-page="<?= $paginacao['totalPaginas']; ?>">
                        <i class="fa fa-angle-double-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="os1pg-select">
        <label for="os1pg-registros-por-pagina">Registros por página:</label>
        <select id="os1pg-registros-por-pagina">
            <option value="25" <?= ($paginacao['registrosPorPagina'] == 25) ? 'selected' : ''; ?>>25</option>
            <option value="50" <?= ($paginacao['registrosPorPagina'] == 50) ? 'selected' : ''; ?>>50</option>
            <option value="100" <?= ($paginacao['registrosPorPagina'] == 100) ? 'selected' : ''; ?>>100</option>
            <option value="200" <?= ($paginacao['registrosPorPagina'] == 200) ? 'selected' : ''; ?>>200</option>
        </select>
    </div>
</div>