<?= $this->layout('_theme', $front); ?>
<div class="container-fl-review">
    <!-- Barra de controles superior -->
    <div class="fcad-form-row form-buttons">
        <button type="button" class="btn btn-success botao-adicionar-ferramenta" id="nvLocal">
            <i class="fa fa-plus"></i> Locais
        </button>

        <button data-url="<?= url("equipamentos/refresh_local"); ?>" id="btnMoverFerramenta" class="btn btn-warning botao-transporte-ferramentas">
            <i class="fa-solid fa-right-left"></i> Movimentação
        </button>
        <?php
        if ($empresa->confirmaMovimentacaoEstoque == 'X'):            
        ?>
            <button
                data-url="<?= url("equipamentos/listar_solicitacoes"); ?>"
                id="<?= empty($solicitacoes) && empty($enviadas) ? "btnSolicitacoesInfo" : "btnSolicitacoes"; ?>"
                class="btn btn-primary position-relative">
                <i class="fa-solid fa-clipboard"></i> Solicitações
                <?php if (!empty($solicitacoes)):  ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= count($solicitacoes); ?>
                        <span class="visually-hidden">unread messages</span>
                    </span>
                <?php endif; ?>
            </button>
        <?php
        endif;
        ?>
        <a href="<?= url("equipamentos") ?>" class="btn btn-info direita">
            <i class="fa fa-undo"></i> Voltar
        </a>
    </div>


    <!-- Filtro de busca -->
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="buscarFerramentasZeta" name="filtro-busca"
                    placeholder="Digite código, descrição ou status..." value="">
            </div>
        </div>
    </div>

    <!-- Tabela principal de ferramentas -->
    <div class="tabela-responsive">
        <table id="tabelaListagemFerramentas" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Estoque</th>
                    <th>Alocado</th>
                    <th>Manutenção</th>
                    <th>Disponível</th>
                    <th>Histórico</th>
                </tr>
            </thead>
            <tbody>
                <!-- Linha de exemplo 1 -->
                <?php
                if (!empty($equipamentos)):
                    foreach ($equipamentos as $equipamento):
                        $alocados = 0;
                        $manutencao = 0;
                        if (!empty($estoque)):
                            foreach ($estoque as $e) :
                                if ($e->id_equipamento == $equipamento->id) :
                                    if ($e->status == '4') :
                                        $alocados = $alocados + $e->qtde;
                                    elseif ($e->status == '5') :
                                        $manutencao = $manutencao + $e->qtde;
                                    endif;
                                endif;
                            endforeach;
                        endif;

                        $disponivel = $equipamento->qtde - $alocados - $manutencao;
                ?>

                        <tr data-id="<?= $equipamento->id; ?>">
                            <td><?= $equipamento->descricao; ?></td>
                            <td><?= !empty($equipamento->qtde) ? $equipamento->qtde : "0"; ?></td>
                            <td>
                                <button type="button" data-url="<?= url("equipamentos/listar_alocados") ?>" data-id="<?= $equipamento->id; ?>" class="btn btn-secondary listAlocado"><?= $alocados; ?></button>
                            </td>
                            <td>
                                <?= $manutencao; ?>
                            </td>
                            <td>
                                <?= $disponivel; ?>
                            </td>
                            <td><button class="btn btn-light kardex" data-url="<?= url("equipamentos/listar_kardex") ?>" data-id="<?= $equipamento->id; ?>"><i class="fa fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach;
                else:
                    ?>
                    <tr>
                        <td colspan="100%" class="text-center">Nenhum equipamento encontrado.</td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<section>
    <?php
    $this->insert("tcsistemas.os/equipamentos/movFerramenta", [
        'equipamentos' => $equipamentos,
        'usuarios' => $usuarios,
        'locais' => $locais,
        'fornecedores' => $fornecedores,
        'empresa' => $empresa
    ]);
    $this->insert("tcsistemas.os/equipamentos/listLocalizacao", []);
    $this->insert("tcsistemas.os/equipamentos/cadLocalFerramenta", []);
    $this->insert("tcsistemas.os/equipamentos/listKardex", []);
    $this->insert("tcsistemas.os/equipamentos/listSolicitacao", ["user" => $user]);
    ?>
</section>