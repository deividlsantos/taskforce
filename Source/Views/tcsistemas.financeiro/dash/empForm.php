<?php
$this->layout("_theme", $front);
?>
<div class="telas-body">
    <form class="form-cadastros" style="width: 80%;" id="form-emp" action="<?= url('emp'); ?>">
        <div class="fcad-form-row" hidden>
            <label for="id_emp">Código</label>
            <input type="text" id="id_emp" name="id_emp" value="<?= ll_encode($emp2->id); ?>">
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group settings_emp_photo">
                <label for="emp_photo" class="thumb radius rounded j_profile_image"
                    style="background-image: url('<?= CONF_FILES_URL . $emp2->logo; ?>')"></label>
                <input id="emp_photo" data-image=".j_profile_image" type="file" class="radius" name="emp_photo" />
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <label for="razao">Empresa / Razão Social <span class="required">*</span></label>
                <input type="text" id="razao" name="razao" class="" value="<?= $emp2->razao; ?>" required>
            </div>
            <div class="fcad-form-group">
                <label for="fantasia">Fantasia <span class="required"></span></label>
                <input type="text" id="fantasia" name="fantasia" class="" value="<?= $emp2->fantasia; ?>">
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <label for="cnpj">CNPJ <span class="required">*</span></label>
                <input type="text" id="cnpj" name="cnpj" class="mask-cnpj" value="<?= $emp2->cnpj; ?>" required>
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna50">
                <label for="endereco">Endereço <span class="required">*</span></label>
                <input type="text" id="endereco" name="endereco" class="" value="<?= $emp2->endereco; ?>" required>
            </div>
            <div class="fcad-form-group coluna10">
                <label for="numero">Número <span class="required">*</span></label>
                <input type="text" id="numero" name="numero" class="" value="<?= $emp2->numero; ?>" required>
            </div>
            <div class="fcad-form-group">
                <label for="bairro">Bairro <span class="required"></span></label>
                <input type="text" id="bairro" name="bairro" class="" value="<?= $emp2->bairro; ?>">
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna60">
                <label for="cidade">Cidade <span class="required">*</span></label>
                <input type="text" id="cidade" name="cidade" class="" value="<?= $emp2->cidade; ?>" required>
            </div>
            <div class="fcad-form-group coluna05">
                <label for="uf">UF <span class="required">*</span></label>
                <input type="text" id="uf" class="" name="uf" value="<?= $emp2->uf; ?>" required>
            </div>
            <div class="fcad-form-group">
                <label for="cep">CEP <span class="required">*</span></label>
                <input type="text" id="cep" name="cep" class="mask-cep" value="<?= $emp2->cep; ?>" required>
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <label for="fone1">Fone 1</label>
                <input type="text" id="fone1" name="fone1" class="phone" value="<?= $emp2->fone1; ?>">
            </div>
            <div class="fcad-form-group">
                <label for="fone2">Fone 2</label>
                <input type="text" id="fone2" name="fone2" class="phone" value="<?= $emp2->fone2; ?>">
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" class="" value="<?= $emp2->email; ?>">
            </div>
        </div>

        <?php
        if ($front['user']->tipo == 5):
        ?>

            <div id="parametros">
                <?php $icon_saved = $emp2->iconeLabel; ?>
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna20">
                        <label for="emp_label">Label Segmento</label>
                        <input type="text" id="emp_label" name="emp_label" class="" value="<?= $emp2->labelFiliais; ?>"
                            required>
                    </div>
                    <div class="fcad-form-group coluna10">
                        <label for="icon_select">Ícone</label>
                        <button type="button" id="open_icon_modal" class="btn btn-light">
                            <span id="selected_icon">
                                <i class="fa <?= $icon_saved; ?>"></i> <!-- Ícone salvo -->
                            </span>
                        </button>
                        <input type="hidden" id="icon_select" name="emp_icone" value="<?= $icon_saved; ?>" required>
                        <div id="icon_preview" style="margin-top: 10px; font-size: 24px; display: none;">
                            <i class="fa <?= $icon_saved; ?>"></i> <!-- Pré-visualização do ícone salvo -->
                        </div>
                    </div>
                </div>
                <div class="fcad-form-row">
                    <div class="coluna40">
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_mostraValorPdf" name="emp_mostraValorPdf"
                                class="coluna10"
                                <?= $emp2->mostraValorPdf == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_mostraValorPdf">Exibir valores (R$) no relatório</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_os_financeiro_auto" name="emp_os_financeiro_auto"
                                class="coluna10"
                                <?= $emp2->os_financeiro_auto == 'X' ? 'checked' : ''; ?>
                                data-url="<?= url('emp/verifica_padroes'); ?>">
                            <label for="emp_os_financeiro_auto">Criar receita ao concluir OS.</label>
                            <button type="button" id="toggle_fin_config_modal" class="btn btn-sm btn-secondary ml-2" disabled>
                                <i class="fa fa-cog"></i> Configurar
                            </button>
                        </div>

                        <!-- Modal de configuração financeira -->
                        <section>
                            <?php
                            $this->insert("tcsistemas.financeiro/dash/empModalFinConfig", [
                                "emp2" => $emp2,
                                "plconta" => $plconta,
                                "operacoes" => $operacoes
                            ]);
                            ?>
                        </section>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_servicosComMedicoes" name="emp_servicosComMedicoes"
                                class="coluna10"
                                <?= $emp2->servicosComMedicoes == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_servicosComMedicoes">Serviços com medições</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_servicosComEquipamentos" name="emp_servicosComEquipamentos"
                                class="coluna10"
                                <?= $emp2->servicosComEquipamentos == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_servicosComEquipamentos">Serviços com Equipamentos</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_tarefasAditivas" name="emp_tarefasAditivas"
                                class="coluna10"
                                <?= $emp2->tarefasAditivas == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_tarefasAditivas">Tarefas Aditivas</label>
                        </div>
                    </div>
                    <div class="column2">
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_mostraDataLegal" name="emp_mostraDataLegal"
                                class="coluna10"
                                <?= $emp2->mostraDataLegal == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_mostraDataLegal">Serviços com data legal</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_bloqueia2tarefasPorOper" name="emp_bloqueia2tarefasPorOper"
                                class="coluna10"
                                <?= $emp2->bloqueia2tarefasPorOper == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_bloqueia2tarefasPorOper">NÃO permitir mais de uma tarefa em andamento por operador</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_equipamentoObrigatorio" name="emp_equipamentoObrigatorio"
                                class="coluna10"
                                <?= $emp2->equipamentoObrigatorio == 'X' ? 'checked' : ''; ?> disabled>
                            <label for="emp_equipamentoObrigatorio">Equipamentos Obrigatórios</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="emp_confirmaMovimentacaoEstoque" name="emp_confirmaMovimentacaoEstoque"
                                class="coluna10"
                                <?= $emp2->confirmaMovimentacaoEstoque == 'X' ? 'checked' : ''; ?>>
                            <label for="emp_confirmaMovimentacaoEstoque">Solicita confirmação do destino nas movimentações de estoque</label>
                        </div>
                    </div>
                </div>
                <!-- Modal para seleção de ícones -->
                <div id="icon_modal" class="icon-modal">
                    <div class="icon-modal-content">
                        <!-- Cabeçalho fixo -->
                        <div class="icon-modal-header">
                            <div class="icon-modal-titlebar">
                                <h3>Selecione um Ícone</h3>
                                <button type="button" id="close_icon_modal" class="icon-modal-close">&times;</button>
                            </div>
                            <input type="text" id="icon_search" class="icon-modal-search" placeholder="Buscar ícone...">
                        </div>

                        <!-- Conteúdo com rolagem -->
                        <div class="icon-modal-body">
                            <div id="icon_grid" class="icon-grid">
                                <!-- Ícones gerados dinamicamente aqui -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        endif;
        ?>

        <div class="fcad-form-row">
            <button class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
        </div>
    </form>
</div>
<div class="rodape" hidden>
    <span class="required">*</span> = Campos Obrigatórios
</div>


</html>