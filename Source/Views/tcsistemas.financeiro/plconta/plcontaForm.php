<input type="text" id="id_plconta" name="id_plconta" value="<?= ($plconta != "") ? ll_encode($plconta->id) : ''; ?>" hidden>
<div class="oper-form">
    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <label for="codigoconta">Cód.Conta</label>
            <input type="text" id="codigoconta" name="codigoconta" value="<?= $plconta != "" ? $plconta->codigoconta : ""; ?>">
        </div>
        <div class="fcad-form-group">
            <label for="descricao">Descricao:</label>
            <input type="text" id="descricao" name="descricao" value="<?= $plconta != "" ? $plconta->descricao : ""; ?>">
        </div>
    </div>
    <div class="fcad-form-row ">
        <div class="fcad-form-group">
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" value="">
                <option value="R" <?= ($plconta != "" && $plconta->tipo == 'R') ? 'selected' : ''; ?>>Receita</option>
                <option value="D" <?= ($plconta != "" && $plconta->tipo == 'D') ? 'selected' : ''; ?>>Despesa</option>
            </select>
        </div>
        <div class="fcad-form-group">
            <label for="subtipo">Subtipo</label>
            <select id="subtipo" name="subtipo" value="">
                <option value="ATIVA" <?= ($plconta != "" && $plconta->subtipo == 'ATIVA') ? 'selected' : ''; ?>>Ativa</option>
                <option value="VARIAVEL" <?= ($plconta != "" && $plconta->subtipo == 'VARIAVEL') ? 'selected' : ''; ?>>Variável</option>
                <option value="FIXA" <?= ($plconta != "" && $plconta->subtipo == 'FIXA') ? 'selected' : ''; ?>>Fixa</option>
            </select>
        </div>
    </div>
    <div class="tc-info">
        <label class="tc-info-label" for="tc">Informações TC Sistemas</label>
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <div class="fcad-form-group-sub-row">
                    <label for="id_tc" class="coluna30">Grupo de Receita</label>
                    <input class="mask-number coluna10 esquerda" type="text" id="id_tc" name="id_tc" value="<?= $plconta != "" ? $plconta->id_tc : ""; ?>">
                </div>
                <div class="fcad-form-group-sub-row">
                    <label for="codigocc" class="coluna30">Centro de Custo</label>
                    <input class="mask-number coluna10 esquerda" type="text" id="codigocc" name="codigocc" value="<?= $plconta != "" ? $plconta->codigocc : ""; ?>">
                </div>
            </div>
        </div>
    </div>
</div>