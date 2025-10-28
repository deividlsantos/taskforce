<div class="equipamentos-form" id="equipamentosForm">
    <div class="fcad-form-row">
        <div class="checkbox-group display-right">
            <label for="ativo">Ativo</label>
            <input type="checkbox" id="ativo" name="ativo" checked>
        </div>
    </div>
    <input type="text" id="id_equipamento" name="id_equipamento" value="<?= $equipamento ? ll_encode($equipamento->id) : ''; ?>" hidden>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna15">
            <label for="equipamento">Tipo do equipamento</label>
            <select id="equipamento" name="equipamento" required>
                <option value="">Selecione</option>
                <option value="VEÍCULO" <?= $equipamento && $equipamento->equipamento == 'VEÍCULO' ? 'selected' : ''; ?>>Veículo</option>
                <option value="MÁQUINA" <?= $equipamento && $equipamento->equipamento == 'MÁQUINA' ? 'selected' : ''; ?>>Máquina</option>
                <option value="FERRAMENTA" <?= $equipamento && $equipamento->equipamento == 'FERRAMENTA' ? 'selected' : ''; ?>>Ferramenta</option>
                <option value="MEDICO" <?= $equipamento && $equipamento->equipamento == 'MEDICO' ? 'selected' : ''; ?>>Equipamento Médico</option>
            </select>
        </div>

        <div class="fcad-form-group">
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?= $equipamento ? $equipamento->descricao : ''; ?>" required>
        </div>

        <div class="fcad-form-group coluna10">
            <label for="anofab">Ano:</label>
            <input type="text" id="anofab" name="anofab" value="<?= $equipamento ? $equipamento->anofab : ''; ?>">
        </div>
        <div class="fcad-form-group coluna10">
            <label for="modelofab">Modelo:</label>
            <input type="text" id="modelofab" name="modelofab" value="<?= $equipamento ? $equipamento->modelofab : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row linha-veiculo">
        <div class="fcad-form-group coluna35">
            <label for="chassi">Chassi:</label>
            <input type="text" id="chassi" name="chassi" value="<?= $equipamento ? $equipamento->chassi : ''; ?>">
        </div>

        <div class="fcad-form-group coluna35">
            <label for="placa">Placa:</label>
            <input
                type="text"
                id="placa"
                name="placa"
                maxlength="10"
                oninput="this.value = this.value.slice(0, 10)"
                value="<?= $equipamento ? $equipamento->placa : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="renavam">Renavam:</label>
            <input type="text" id="renavam" name="renavam" value="<?= $equipamento ? $equipamento->renavam : ''; ?>">
        </div>
    </div>

    <div class="fcad-form-row linha-geral">
        <div class="fcad-form-group coluna35">
            <label for="serie">Nº.Série:</label>
            <input type="text" id="serie" name="serie" value="<?= $equipamento ? $equipamento->serie : ''; ?>">
        </div>

        <div class="fcad-form-group coluna35">
            <label for="tag">Tag:</label>
            <input type="text" id="tag" name="tag" value="<?= $equipamento ? $equipamento->tag : ''; ?>">
        </div>

        <div class="fcad-form-group">
            <label for="fabricante">Fabricante:</label>
            <input type="text" id="fabricante" name="fabricante" value="<?= $equipamento ? $equipamento->fabricante : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="checkbox-group" style="margin-top: 30px;">
            <label for="inventario">Inventário</label>
            <input type="checkbox" id="inventario" name="inventario"
            <?= $equipamento && !empty($equipamento->id_cli) ? 'disabled' : ''; ?> 
            <?= $equipamento && $equipamento->inventario == '1' ? 'checked' : ''; ?>>
        </div>
        <div class="fcad-form-group coluna40 direita">
            <label for="id_cli">Cliente:</label>
            <select id="id_cli" name="id_cli" required>
                <option value="">Selecione</option>
                <?php
                if (!empty($cliente)):
                    foreach ($cliente as $cli) :
                        $temp = "";
                        if ($equipamento != "" && $equipamento->id_cli == $cli->id):
                            $temp = "selected";
                        endif;
                ?>
                        <option value="<?= $cli->id; ?>" <?= $temp; ?>><?= $cli->nome; ?></option>
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>
        <div class="fcad-form-group coluna40">
            <label for="status">Status</label>
            <input
                name="status"
                id="status"
                type="text"
                placeholder="Digite ou selecione"
                value="<?= $equipamento ? $equipamento->status : ''; ?>"
                autocomplete="off"
                class="awesomplete" />
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna35">
            <label for="classe_equipamento">Classe do equipamento</label>
            <input
                type="text"
                name="classe_equipamento"
                id="classe_equipamento"
                placeholder="Digite ou selecione"
                value="<?= $equipamento ? $equipamento->classe_equipamento : ''; ?>"
                autocomplete="off"
                class="awesomplete" />
        </div>

        <div class="fcad-form-group coluna35">
            <label for="classe_operacional">Classe Operacional</label>
            <input
                id="classe_operacional"
                type="text"
                name="classe_operacional"
                placeholder="Digite ou selecione"
                value="<?= $equipamento ? $equipamento->classe_operacional : ''; ?>"
                autocomplete="off"
                class="awesomplete" />
        </div>

        <div class="fcad-form-group">
            <label for="especie_equipamento">Espécie do equipamento</label>
            <input
                id="especie_equipamento"
                type="text"
                name="especie_equipamento"
                placeholder="Digite ou selecione"
                value="<?= $equipamento ? $equipamento->especie_equipamento : ''; ?>"
                autocomplete="off"
                class="awesomplete" />
        </div>
    </div>

    <div class="fcad-form-row">
        <div class="fcad-form-group coluna20">
            <label for="id_plconta">Grupo de Receita</label>
            <select id="id_plconta" name="id_plconta">
                <option value="">Selecione</option>
                <?php
                if (!empty($plconta)):
                    foreach ($plconta as $grupo) :
                        $temp = "";
                        if ($equipamento != "" && $equipamento->id_plconta == $grupo->id):
                            $temp = "selected";
                        endif;
                ?>
                        <option value="<?= $grupo->id; ?>" <?= $temp; ?>><?= $grupo->descricao; ?></option>
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>
        <div class="fcad-form-group coluna15 justify-content-end tphantom">

            <button type="button" id="chklstOpenBtn" class="chklst-btn-open btn btn-info">Itens Checklist</button>
        </div>
        <div class="fcad-form-group coluna20 direita">
            <label for="combustivel">Combustível</label>
            <select id="combustivel" name="combustivel">
                <option value="">Selecione</option>
                <option value="GASOLINA" <?= $equipamento && $equipamento->combustivel == 'GASOLINA' ? 'selected' : ''; ?>>GASOLINA</option>
                <option value="DIESEL" <?= $equipamento && $equipamento->combustivel == 'DIESEL' ? 'selected' : ''; ?>>DIESEL</option>
                <option value="ETANOL" <?= $equipamento && $equipamento->combustivel == 'ETANOL' ? 'selected' : ''; ?>>ETANOL</option>
                <option value="GNV" <?= $equipamento && $equipamento->combustivel == 'GNV' ? 'selected' : ''; ?>>GNV</option>
                <option value="BIODIESEL" <?= $equipamento && $equipamento->combustivel == 'BIODIESEL' ? 'selected' : ''; ?>>BIODIESEL</option>
                <option value="ELÉTRICO" <?= $equipamento && $equipamento->combustivel == 'ELÉTRICO' ? 'selected' : ''; ?>>ELÉTRICO</option>
            </select>
        </div>

        <div class="fcad-form-group coluna10">
            <label for="unidade">Unidade</label>
            <select id="unidade" name="unidade">
                <option value="">Selecione</option>
                <option value="KM/H" <?= $equipamento && $equipamento->unidade == 'KM/H' ? 'selected' : ''; ?>>Km/H</option>
                <option value="KM/L" <?= $equipamento && $equipamento->unidade == 'KM/L' ? 'selected' : ''; ?>>Km/L</option>
                <option value="H/L" <?= $equipamento && $equipamento->unidade == 'H/L' ? 'selected' : ''; ?>>H/L</option>
                <option value="GL/H" <?= $equipamento && $equipamento->unidade == 'GL/H' ? 'selected' : ''; ?>>Gl/H</option>
                <option value="GL/L" <?= $equipamento && $equipamento->unidade == 'GL/L' ? 'selected' : ''; ?>>Gl/L</option>
                <option value="KW/H" <?= $equipamento && $equipamento->unidade == 'KW/H' ? 'selected' : ''; ?>>kW/H</option>
            </select>
        </div>

        <div class="fcad-form-group coluna10">
            <label for="autonomia">Autonomia:</label>
            <input type="text" id="autonomia" name="autonomia" value="<?= $equipamento && !empty($equipamento->autonomia) ? float_us_to_br($equipamento->autonomia) : ''; ?>">
        </div>
    </div>
</div>

<script>
    $(function() {
        // Função para inicializar Awesomplete dinamicamente
        function initializeAwesomplete(inputSelector, dataList) {
            const $input = $(inputSelector);

            const awesomplete = new Awesomplete($input[0], {
                list: dataList,
                minChars: 0,
                maxItems: Infinity,
                autoFirst: true
            });

            // Exibe todos os itens mesmo se já tiver valor no input
            $input.on("focus", function() {
                const currentValue = $(this).val();

                // Se já tiver valor, limpa temporariamente pra forçar exibição total
                if (currentValue.trim() !== "") {
                    $(this).val("");
                    awesomplete.evaluate();
                    setTimeout(() => {
                        $(this).val(currentValue);
                    }, 10);
                } else {
                    awesomplete.evaluate();
                }
            });

            $input.on("input", function() {
                const self = this;
                setTimeout(() => {
                    self.value = self.value.toUpperCase();
                }, 0);
            });

            $input.css({
                width: "100%",
                maxWidth: "100%",
                boxSizing: "border-box",
                display: "block"
            });
        }

        // Lista de status vinda do PHP
        const listaStatus = [
            <?php foreach ($status as $st): ?> "<?= addslashes($st->status) ?>",
            <?php endforeach; ?>
        ];

        // Lista de classes operacionais vinda do PHP
        const listaClassesOperacionais = [
            <?php foreach ($classeOperacional as $classe): ?> "<?= addslashes($classe->classe_operacional) ?>",
            <?php endforeach; ?>
        ];

        // Lista de espécies de equipamentos vinda do PHP
        const listaEspeciesEquipamentos = [
            <?php foreach ($especieEquipamento as $especie): ?> "<?= addslashes($especie->especie_equipamento) ?>",
            <?php endforeach; ?>
        ];

        const listaClassesEquipamentos = [
            <?php foreach ($classeEquipamento as $classe): ?> "<?= addslashes($classe->classe_equipamento) ?>",
            <?php endforeach; ?>
        ];

        // Inicializa Awesomplete para os campos desejados
        initializeAwesomplete("#status", listaStatus);
        initializeAwesomplete("#classe_equipamento", listaClassesEquipamentos);
        initializeAwesomplete("#classe_operacional", listaClassesOperacionais);
        initializeAwesomplete("#especie_equipamento", listaEspeciesEquipamentos);
    });
</script>