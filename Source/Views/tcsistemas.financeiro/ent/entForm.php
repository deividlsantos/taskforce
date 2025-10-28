<input type="text" id="id_ent" name="id_ent" value="<?= ($ent != "") ? ll_encode($ent->id) : ''; ?>" hidden>
<div class="entidade-form">
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna20" style="display: none;">
            <label for="ent_tipo">Tipo</label>
            <select id="ent_tipo" name="ent_tipo" class="issuer-select">
                <option value="">Selecione</option>
                <option value="1" <?= ($ent != "" && $ent->tipo == 1) || ($ent == "" && $tipo == 1) ? 'selected' : ''; ?>>Cliente</option>
                <option value="2" <?= ($ent != "" && $ent->tipo == 2) || ($ent == "" && $tipo == 2) ? 'selected' : ''; ?>>Fornecedor</option>
                <option value="3" <?= ($ent != "" && $ent->tipo == 3) || ($ent == "" && $tipo == 3) ? 'selected' : ''; ?>>Colaborador</option>
                <option value="4" <?= ($ent != "" && $ent->tipo == 4) || ($ent == "" && $tipo == 4) ? 'selected' : ''; ?>>Portador</option>
            </select>
        </div>

        <div class="checkbox-group display-right" <?= !empty($hidden) && $hidden != "" ? $hidden : ""; ?>>
            <label for="ent_status">Ativo</label>
            <input type="checkbox" id="ent_status" name="ent_status" <?= (empty($ent) || $ent->status != "I") ? 'checked' : ''; ?>>
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna20">
            <label for="ent_fisjur">Pessoa</label>
            <select id="ent_fisjur" name="ent_fisjur">
                <option value="1" <?= ($ent != "" && $ent->fisjur == 1) ? 'selected' : ''; ?>>Física</option>
                <option value="2" <?= ($ent != "" && $ent->fisjur == 2) ? 'selected' : ''; ?>>Jurídica</option>
            </select>
        </div>
        <div id="inptcpf" class="fcad-form-group coluna45">
            <label for="ent_cpf">CPF:</label>
            <input type="text" data-url="<?= url("ent/verificar"); ?>" id="ent_cpf" name="ent_cpf" class="mask-doc" value="<?= ($ent != "" && $ent->fisjur == 1) ? $ent->cpfcnpj : ''; ?>">
            <span id="tooltipCpf" class="tooltip-text">Já existe um registro com este CPF.</span>
        </div>
        <div id="inptcnpj" class="fcad-form-group coluna45">
            <label for="ent_cnpj">CNPJ:</label>
            <input type="text" data-url="<?= url("ent/verificar"); ?>" id="ent_cnpj" name="ent_cnpj" class="mask-cnpj" value="<?= ($ent != "" && $ent->fisjur == 2) ? $ent->cpfcnpj : ''; ?>">
            <span id="tooltipCnpj" class="tooltip-text">Já existe um registro com este CNPJ.</span>
        </div>

        <div class="fcad-form-group">
            <label for="ent_inscrg">RG:</label>
            <input type="text" id="ent_inscrg" name="ent_inscrg" maxlength="16" value="<?= ($ent != "") ? $ent->inscrg : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna45">
            <label for="ent_nome">Nome:</label>
            <input type="text" id="ent_nome" name="ent_nome" value="<?= ($ent != "") ? $ent->nome : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="ent_fantasia">Apelido:</label>
            <input type="text" id="ent_fantasia" name="ent_fantasia" value="<?= ($ent != "") ? $ent->fantasia : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna80">
            <label for="ent_endereco">Endereço:</label>
            <input type="text" id="ent_endereco" name="ent_endereco" value="<?= ($ent != "") ? $ent->endereco : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="ent_numero">Número:</label>
            <input type="text" id="ent_numero" name="ent_numero" value="<?= ($ent != "") ? $ent->numero : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna40">
            <label for="ent_complemento">Complemento:</label>
            <input type="text" id="ent_complemento" name="ent_complemento" value="<?= ($ent != "") ? $ent->complemento : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="ent_bairro">Bairro:</label>
            <input type="text" id="ent_bairro" name="ent_bairro" value="<?= ($ent != "") ? $ent->bairro : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna60">
            <label for="ent_cidade">Cidade:</label>
            <input type="text" id="ent_cidade" name="ent_cidade" value="<?= ($ent != "") ? $ent->cidade : ''; ?>">
        </div>

        <div class="fcad-form-group coluna10">
            <label for="ent_uf">UF:</label>
            <select id="ent_uf" name="ent_uf">
                <option value="">...</option>
                <option value="AC" <?= ($ent != "" && $ent->uf == "AC") ? 'selected' : ''; ?>>AC</option>
                <option value="AL" <?= ($ent != "" && $ent->uf == "AL") ? 'selected' : ''; ?>>AL</option>
                <option value="AP" <?= ($ent != "" && $ent->uf == "AP") ? 'selected' : ''; ?>>AP</option>
                <option value="AM" <?= ($ent != "" && $ent->uf == "AM") ? 'selected' : ''; ?>>AM</option>
                <option value="BA" <?= ($ent != "" && $ent->uf == "BA") ? 'selected' : ''; ?>>BA</option>
                <option value="CE" <?= ($ent != "" && $ent->uf == "CE") ? 'selected' : ''; ?>>CE</option>
                <option value="DF" <?= ($ent != "" && $ent->uf == "DF") ? 'selected' : ''; ?>>DF</option>
                <option value="ES" <?= ($ent != "" && $ent->uf == "ES") ? 'selected' : ''; ?>>ES</option>
                <option value="GO" <?= ($ent != "" && $ent->uf == "GO") ? 'selected' : ''; ?>>GO</option>
                <option value="MA" <?= ($ent != "" && $ent->uf == "MA") ? 'selected' : ''; ?>>MA</option>
                <option value="MT" <?= ($ent != "" && $ent->uf == "MT") ? 'selected' : ''; ?>>MT</option>
                <option value="MS" <?= ($ent != "" && $ent->uf == "MS") ? 'selected' : ''; ?>>MS</option>
                <option value="MG" <?= ($ent != "" && $ent->uf == "MG") ? 'selected' : ''; ?>>MG</option>
                <option value="PA" <?= ($ent != "" && $ent->uf == "PA") ? 'selected' : ''; ?>>PA</option>
                <option value="PB" <?= ($ent != "" && $ent->uf == "PB") ? 'selected' : ''; ?>>PB</option>
                <option value="PR" <?= ($ent != "" && $ent->uf == "PR") ? 'selected' : ''; ?>>PR</option>
                <option value="PE" <?= ($ent != "" && $ent->uf == "PE") ? 'selected' : ''; ?>>PE</option>
                <option value="PI" <?= ($ent != "" && $ent->uf == "PI") ? 'selected' : ''; ?>>PI</option>
                <option value="RJ" <?= ($ent != "" && $ent->uf == "RJ") ? 'selected' : ''; ?>>RJ</option>
                <option value="RN" <?= ($ent != "" && $ent->uf == "RN") ? 'selected' : ''; ?>>RN</option>
                <option value="RS" <?= ($ent != "" && $ent->uf == "RS") ? 'selected' : ''; ?>>RS</option>
                <option value="RO" <?= ($ent != "" && $ent->uf == "RO") ? 'selected' : ''; ?>>RO</option>
                <option value="RR" <?= ($ent != "" && $ent->uf == "RR") ? 'selected' : ''; ?>>RR</option>
                <option value="SC" <?= ($ent != "" && $ent->uf == "SC") ? 'selected' : ''; ?>>SC</option>
                <option value="SP" <?= ($ent != "" && $ent->uf == "SP") ? 'selected' : ''; ?>>SP</option>
                <option value="SE" <?= ($ent != "" && $ent->uf == "SE") ? 'selected' : ''; ?>>SE</option>
                <option value="TO" <?= ($ent != "" && $ent->uf == "TO") ? 'selected' : ''; ?>>TO</option>
            </select>
        </div>

        <div class="fcad-form-group">
            <label for="ent_cep">Cep:</label>
            <input type="text" id="ent_cep" name="ent_cep" class="mask-cep" value="<?= ($ent != "") ? $ent->cep : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna100">
            <label for="ent_email">Email:</label>
            <input type="email" id="ent_email" name="ent_email" value="<?= ($ent != "") ? $ent->email : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row ">
        <div class="fcad-form-group coluna45">
            <label for="ent_fone1">Fone:</label>
            <input type="text" id="ent_fone1" name="ent_fone1" class="mask-fone" value="<?= ($ent != "") ? $ent->fone1 : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="ent_fone2">Celular:</label>
            <input type="text" id="ent_fone2" name="ent_fone2" class="mask-cel" value="<?= ($ent != "") ? $ent->fone2 : ''; ?>">
        </div>
    </div>
</div>

<div class="func-form">
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna45">
            <label for="func_matricula">Matrícula:</label>
            <input type="text" id="func_matricula" name="func_matricula" value="<?= ($ent != "" && $ent->tipo == 3 && $entFilha != "") ? $entFilha->matricula : ''; ?>">
        </div>
        <div class="fcad-form-group">
            <label for="func_ctps">CTPS:</label>
            <input type="text" id="func_ctps" name="func_ctps" value="<?= ($ent != "" && $ent->tipo == 3  && $entFilha != "") ? $entFilha->ctps : ''; ?>">
        </div>

        <?php
        if (!empty($turno)) :
        ?>
            <div class="fcad-form-group">
                <div class="fcad-form-group coluna40">
                    <label for="func_turno">Turno</label>
                    <select id="func_turno" name="func_turno">
                        <option value="">Selecione</option>
                        <?php
                        foreach ($turno as $t):
                            $temp = "";
                            if ($ent != "" && $ent->tipo == 3):
                                if ($entFilha->id_turno == $t->id) :
                                    $temp = "selected";
                                endif;
                            endif;
                        ?>
                            <option value="<?= $t->id; ?>" <?= $temp; ?>><?= $t->nome; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
        <?php
        endif;
        ?>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna30">
            <label for="func_setor">Setor:</label>
            <input type="text" id="func_setor" name="func_setor" value="<?= ($ent != "" && $ent->tipo == 3  && $entFilha != "") ? $entFilha->depto : ''; ?>">
        </div>
        <div class="fcad-form-group coluna40">
            <label for="func_cargo">Cargo:</label>
            <input type="text" id="func_cargo" name="func_cargo" value="<?= ($ent != "" && $ent->tipo == 3  && $entFilha != "") ? $entFilha->cargo : ''; ?>">
        </div>
        <div class="fcad-form-group">
            <label for="func_salario">Salário:</label>
            <input type="text" id="func_salario" name="func_salario" class="mask-money" value="<?= ($ent != "" && $ent->tipo == 3  && $entFilha != "") ? $entFilha->salario : ''; ?>">
        </div>
        <div class="fcad-form-group">
            <label for="func_admissao">Admissão:</label>
            <input type="date" id="func_admissao" name="func_admissao" value="<?= ($ent != "" && $ent->tipo == 3  && $entFilha != "")  ? $entFilha->admissao : ''; ?>">
        </div>
    </div>
    <?php if (!empty($arquivos)): ?>        
        <!-- Collapse Container -->
        <div class="fcad-form-row" style="margin-top: 20px;">
            <div class="fcad-form-group">
                <a class="btn btn-secondary" data-bs-toggle="collapse" href="#extraInfo" role="button" aria-expanded="false"
                    aria-controls="extraInfo">
                    Arquivos do Colaborador
                </a>
                <div class="collapse mt-3" id="extraInfo">
                    <div class="card card-body">
                        <!-- Lista com barra de rolagem -->
                        <div class="scrollable-list" style="max-height: 200px; overflow-y: auto;">
                            <ul class="list-group">
                                <?php foreach ($arquivos as $arq): ?>
                                    <li class="list-group-item">
                                        <a href="<?= FTP_URL . '/tcponto/docs/emp_' . $id_emp . "/" . $arq->nome_arquivo . "." . $arq->extensao; ?>"
                                            class="" target="_blank"><?= $arq->descricao ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fim do Collapse Container -->

    <?php endif; ?>
</div>
<div class="port-form">
    <div class="fcad-form-row">
        <?php if ($bank != "") :
        ?>
            <div class="fcad-form-group coluna40">
                <label for="port_banco">Banco</label>
                <select id="port_banco" name="port_banco">
                    <option>Selecione</option>
                    <?php
                    foreach ($bank as $bk):
                        $temp = "";
                        if ($ent != "" && $ent->tipo == 4):
                            if ($entFilha->banco == $bk->banco) :
                                $temp = "selected";
                            endif;
                        endif;
                    ?>
                        <option value="<?= $bk->banco; ?>" <?= $temp; ?>><?= (($bk->codcomp) ? $bk->codcomp . " - " : "") . $bk->banco; ?></option>
                    <?php
                    endforeach;
                    ?>
                </select>
            </div>
        <?php
        endif;
        ?>
        <div class="fcad-form-group coluna20">
            <label for="port_agencia">Agência:</label>
            <input type="text" id="port_agencia" name="port_agencia" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->agencia : ''; ?>">
        </div>
        <div class="fcad-form-group coluna05">
            <label for="port_agdv">DV:</label>
            <input type="text" id="port_agdv" name="port_agdv" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->agenciadv : ''; ?>">
        </div>

        <div class="fcad-form-group direita coluna20">
            <label for="port_conta">Conta:</label>
            <input type="text" id="port_conta" name="port_conta" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->conta : ''; ?>">
        </div>
        <div class="fcad-form-group coluna05">
            <label for="port_cdv">DV:</label>
            <input type="text" id="port_cdv" name="port_cdv" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->contadv : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <label for="port_titular">Titular:</label>
            <input type="text" id="port_titular" name="port_titular" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->titular : ''; ?>">
        </div>
        <div class="fcad-form-group">
            <label for="port_obs">OBS:</label>
            <input type="text" id="port_obs" name="port_obs" value="<?= ($ent != "" && $ent->tipo == 4) ? $entFilha->obs : ''; ?>">
        </div>
    </div>
</div>
<div class="cli-form">
    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <label for="cli_obs">OBS:</label>
            <input type="text" id="cli_obs" name="cli_obs" value="<?= ($ent != "" && $ent->tipo <= 2) ? $entFilha->obs : ''; ?>">
        </div>
    </div>
</div>