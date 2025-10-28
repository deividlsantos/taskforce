<?php
$urlAction = url("equipamentos/salvar_mov");
$nomeBotao = "Confirmar Movimentação";

if ($empresa->confirmaMovimentacaoEstoque == 'X') {
    $urlAction = url("equipamentos/solicitar_mov");
    $nomeBotao = "Solicitar Movimentação";
}

?>
<div id="modalMovFerramenta" class="modal modal-pag1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-2 titulo-pai">Movimentação de Ferramentas</h3>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" type="button"></button>
            </div>

            <div class="modal-body">
                <form id="form-movimentacao-ferramenta" class="form-cadastros" action="<?= $urlAction; ?>">
                    <div class="fcad-form-row">
                        <i class="fa-solid fa-info-circle direita" id="movInfo" data-tooltip="- Os campos de origem e destino são obrigatórios.<br>- Marque 'Entrada' para preencher apenas o destino.<br>- Marque 'Saída' para preencher apenas a origem."></i>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="descFerramenta">Equipamento/Ferramenta:</label>
                            <select data-url="<?= url("equipamentos/verificar_estoque") ?>" id="descFerramenta" name="id_equipamento" required>
                                <option value="">Selecione ferramenta</option>
                                <?php foreach ($equipamentos as $equipamento): ?>
                                    <option value="<?= $equipamento->id; ?>"><?= $equipamento->descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label for="qtdFerramenta">Quantidade:</label>
                            <input type="text" id="qtdFerramenta" name="qtde" required>
                        </div>
                        <div class="fcad-form-group coluna05">
                            <label for="entrada">Entrada</label>
                            <input type="checkbox" id="entrada" name="entrada" style="transform: scale(0.51);">
                        </div>
                        <div class="fcad-form-group coluna05">
                            <label for="saida">Saída</label>
                            <input type="checkbox" id="saida" name="saida" style="transform: scale(0.51);">
                        </div>
                    </div>

                    <div class="fcad-form-row fornecedor-mov-row">
                        <div class="fcad-form-group">
                            <label for="id_fornecedor">Fornecedor:</label>
                            <select id="id_fornecedor" name="id_fornecedor">
                                <option value="">Selecione o fornecedor</option>
                                <?php foreach ($fornecedores as $fornecedor): ?>
                                    <option value="<?= $fornecedor->id; ?>"><?= $fornecedor->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="usuarioOrigemSelect">Usuario Origem:</label>
                            <select id="usuarioOrigemSelect" name="id_usuario_origem" required>
                                <option value="">Selecione usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->id; ?>"><?= $usuario->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="fcad-form-group">
                            <label for="usuarioDestinoSelect">Usuario Destino:</label>
                            <select id="usuarioDestinoSelect" name="id_usuario_destino" required>
                                <option value="">Selecione usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->id; ?>"><?= $usuario->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="localOrigemSelect">Local Origem:</label>
                            <select id="localOrigemSelect" name="id_local_origem" required>
                                <option value="">Selecione origem</option>
                                <?php foreach ($locais as $local): ?>
                                    <option value="<?= $local->id; ?>"><?= "{$local->descricao}({$local->desc_status})"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="fcad-form-group">
                            <label for="localDestinoSelect">Local Destino:</label>
                            <select id="localDestinoSelect" name="id_local_destino" required>
                                <option value="">Selecione destino</option>
                                <?php foreach ($locais as $local): ?>
                                    <option value="<?= $local->id; ?>"><?= "{$local->descricao}({$local->desc_status})"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="observacao">Observações:</label>
                            <textarea type="text" id="observacao" name="observacao" rows="1"></textarea>
                        </div>
                    </div>
                    <!-- Rodapé com botões de ação -->
                    <div class="">
                        <button class="btn btn-primary">
                            <i class="fa-solid fa-right-left"></i> <?= $nomeBotao; ?>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>