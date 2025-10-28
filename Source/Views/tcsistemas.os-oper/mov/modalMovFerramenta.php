<?php
$urlAction = url("oper_mov/solicitar_mov");
$nomeBotao = "Enviar";
?>
<div id="modalOprMovFerramenta" class="modal modal-pag1 modal-solmob" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Envio de Ferramentas</h3>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" type="button"></button>
            </div>

            <div class="modal-body">
                <form id="form-movimentacao-ferramenta" method="post" class="form-oper" action="<?= $urlAction; ?>" data-reset="true">
                    <div class="fcad-form-row">
                        <i class="fa-solid fa-info-circle direita" id="movInfo" data-tooltip="- Os campos de origem e destino são obrigatórios.<br>- Marque 'Entrada' para preencher apenas o destino.<br>- Marque 'Saída' para preencher apenas a origem."></i>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="descFerramenta">Equipamento/Ferramenta:</label>
                            <select data-url="<?= url("oper_mov/verificar_estoque") ?>" id="descFerramenta" name="id_equipamento" required>
                                <option value="">Selecione ferramenta</option>
                                <?php foreach ($equipamentos as $equipamento): ?>
                                    <option value="<?= $equipamento->id; ?>"><?= $equipamento->descricao; ?></option>
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
                    </div>

                    <div class="fcad-form-row">
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
                            <label for="usuarioDestinoSelect">Usuário Destino:</label>
                            <select id="usuarioDestinoSelect" name="id_usuario_destino" required>
                                <option value="">Selecione usuario</option>
                                <?php foreach ($usuarios as $usuario):
                                    if ($user->id == $usuario->id)
                                        continue;
                                    ?>
                                    <option value="<?= $usuario->id; ?>"><?= $usuario->nome; ?></option>
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
                    <div class="form-group quantity-input">
                        <label style="margin-right: 20px; padding-top: 10px;">Qtde:</label>
                        <button type="button" class="btnqtde" id="decreaseQuantity2">-</button>
                        <input type="number" class="form-control" id="quantityInput2" value="1" min="1" name="qtde">
                        <button type="button" class="btnqtde" id="increaseQuantity2">+</button>
                    </div>
                    <!-- Rodapé com botões de ação -->
                    <div class="modal-footer">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group">
                                <button class="btn btn-success">
                                    <i class="fa-solid fa-right-left"></i> <?= $nomeBotao; ?>
                                </button>
                            </div>
                            <div class="fcad-form-group">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>