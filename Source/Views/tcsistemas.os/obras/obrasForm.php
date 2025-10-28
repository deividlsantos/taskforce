<input type="text" id="id_obras" name="id_obras" value="<?= ($obras != "") ? ll_encode($obras->id) : ''; ?>" hidden>
<div class="obras-form">
    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna15">
            <label for="nome">Controle:</label>
            <input type="text" id="controle" name="controle" value="<?= ($obras != "") ? $obras->controle : ''; ?>">
        </div>
        <div class="fcad-form-group coluna60">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= ($obras != "") ? $obras->nome : ''; ?>" required>
        </div>
        <div class="fcad-form-group">
            <label for="cliente-obra">Cliente:<span><button type="button" data-div="cliente-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
            <select id="cliente-obra" name="cliente-obra" required>
                <option value="">Selecione</option>
                <?php
                foreach ($cliente as $vlr) :
                    $selected = ($obras != "" && $obras->id_ent_cli == $vlr->id) ? "selected" : "";
                ?>
                    <option value="<?= $vlr->id; ?>" <?= $selected ?>> <?= $vlr->nome; ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna80">
            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= ($obras != "") ? $obras->endereco : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" value="<?= ($obras != "") ? $obras->numero : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row">
        <div class="fcad-form-group coluna40">
            <label for="complemento">Complemento:</label>
            <input type="text" id="complemento" name="complemento" value="<?= ($obras != "") ? $obras->complemento : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" value="<?= ($obras != "") ? $obras->bairro : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row">
        <div class="fcad-form-group coluna60">
            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" value="<?= ($obras != "") ? $obras->cidade : ''; ?>">
        </div>

        <div class="fcad-form-group coluna10">
            <label for="uf">Estado:</label>
            <select id="uf" name="uf" class="issuer-select">
                <option value="">Selecione</option>
                <option value="AC" <?= ($obras != "" && $obras->uf == "AC") ? "selected" : ""; ?>>AC</option>
                <option value="AL" <?= ($obras != "" && $obras->uf == "AL") ? "selected" : ""; ?>>AL</option>
                <option value="AP" <?= ($obras != "" && $obras->uf == "AP") ? "selected" : ""; ?>>AP</option>
                <option value="AM" <?= ($obras != "" && $obras->uf == "AM") ? "selected" : ""; ?>>AM</option>
                <option value="BA" <?= ($obras != "" && $obras->uf == "BA") ? "selected" : ""; ?>>BA</option>
                <option value="CE" <?= ($obras != "" && $obras->uf == "CE") ? "selected" : ""; ?>>CE</option>
                <option value="DF" <?= ($obras != "" && $obras->uf == "DF") ? "selected" : ""; ?>>DF</option>
                <option value="ES" <?= ($obras != "" && $obras->uf == "ES") ? "selected" : ""; ?>>ES</option>
                <option value="GO" <?= ($obras != "" && $obras->uf == "GO") ? "selected" : ""; ?>>GO</option>
                <option value="MA" <?= ($obras != "" && $obras->uf == "MA") ? "selected" : ""; ?>>MA</option>
                <option value="MT" <?= ($obras != "" && $obras->uf == "MT") ? "selected" : ""; ?>>MT</option>
                <option value="MS" <?= ($obras != "" && $obras->uf == "MS") ? "selected" : ""; ?>>MS</option>
                <option value="MG" <?= ($obras != "" && $obras->uf == "MG") ? "selected" : ""; ?>>MG</option>
                <option value="PA" <?= ($obras != "" && $obras->uf == "PA") ? "selected" : ""; ?>>PA</option>
                <option value="PB" <?= ($obras != "" && $obras->uf == "PB") ? "selected" : ""; ?>>PB</option>
                <option value="PR" <?= ($obras != "" && $obras->uf == "PR") ? "selected" : ""; ?>>PR</option>
                <option value="PE" <?= ($obras != "" && $obras->uf == "PE") ? "selected" : ""; ?>>PE</option>
                <option value="PI" <?= ($obras != "" && $obras->uf == "PI") ? "selected" : ""; ?>>PI</option>
                <option value="RJ" <?= ($obras != "" && $obras->uf == "RJ") ? "selected" : ""; ?>>RJ</option>
                <option value="RN" <?= ($obras != "" && $obras->uf == "RN") ? "selected" : ""; ?>>RN</option>
                <option value="RS" <?= ($obras != "" && $obras->uf == "RS") ? "selected" : ""; ?>>RS</option>
                <option value="RO" <?= ($obras != "" && $obras->uf == "RO") ? "selected" : ""; ?>>RO</option>
                <option value="RR" <?= ($obras != "" && $obras->uf == "RR") ? "selected" : ""; ?>>RR</option>
                <option value="SC" <?= ($obras != "" && $obras->uf == "SC") ? "selected" : ""; ?>>SC</option>
                <option value="SP" <?= ($obras != "" && $obras->uf == "SP") ? "selected" : ""; ?>>SP</option>
                <option value="SE" <?= ($obras != "" && $obras->uf == "SE") ? "selected" : ""; ?>>SE</option>
                <option value="TO" <?= ($obras != "" && $obras->uf == "TO") ? "selected" : ""; ?>>TO</option>
            </select>
        </div>
        <div class="fcad-form-group coluna25">
            <label for="cep">Cep:</label>
            <input type="text" id="cep" name="cep" value="<?= ($obras != "") ? $obras->cep : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna30">
            <label for="proprietario">Proprietário:</label>
            <input type="text" id="proprietario" name="proprietario" value="<?= ($obras != "") ? $obras->proprietario : ''; ?>">
        </div>

        <div class="fcad-form-group coluna15">
            <label for="area">Área:</label>
            <input type="text" id="area" name="area" value="<?= ($obras != "") ? $obras->area : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="localizacao">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" value="<?= ($obras != "") ? $obras->localizacao : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <label for="obs">Obs:</label>
            <textarea type="text" id="obs" name="obs"><?= ($obras != "") ? $obras->obs : ''; ?></textarea>
        </div>
    </div>
</div>