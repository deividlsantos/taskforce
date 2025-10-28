<!-- Modal -->
<div class="modal fade modal-operaditivo" id="aditivoModal" data-url="<?= url("oper_ordens/aditivo"); ?>" tabindex="-1" role="dialog" aria-labelledby="opermatModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="aditivoModalLabel">Observações</h2>
                <button type="button" id="close-aditivo" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <form class="form-cadastros form-oper" method="post" id="form-opermob" action="<?= url("oper_ordens/aditivo") ?>">
                    <input type="hidden" id="aditivoOS1" name="aditivoOS1" value="">
                    <input type="hidden" id="aditivoTrue" name="aditivoTrue" value="true">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="aditivoCliente">Cliente</label>
                            <input type="text" id="aditivoCliente" name="aditivoCliente" class="" value="" disabled>
                        </div>
                    </div>
                    <div class="fcad-form-group coluna20">
                        <label for="aditivoOperador">Operador</label>
                        <select type="text" class="selectOperador" id="aditivoOperador" name="aditivoOperador" value="" required>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($operador)):
                                foreach ($operador as $oper) :
                            ?>
                                    <option value="<?= $oper->id_ent; ?>"><?= $oper->nome; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-group">
                        <label for="aditivoServico">Tarefa</label>
                        <select type="text" class="selectServico" id="aditivoServico" name="aditivoServico" value="" required>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($servico)):
                                foreach ($servico as $serv) :
                            ?>
                                    <option
                                        value="<?= $serv->id; ?>"
                                        data-valor="<?= $serv->valor; ?>"
                                        data-tempo="<?= $serv->tempo; ?>">
                                        <?= $serv->nome; ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="aditivoQtde">Qtde</label>
                            <input type="number" id="aditivoQtde" name="aditivoQtde" class="" value="" required>
                        </div>
                        <div class="fcad-form-group">
                            <label for="aditivoData">Data</label>
                            <input type="date" id="aditivoData" name="aditivoData" class="" value="" required>
                        </div>
                        <div class="fcad-form-group">
                            <label for="aditivoHora">Hora</label>
                            <input type="time" id="aditivoHora" name="aditivoHora" class="" value="" required>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="aditivoObs">Obs</label>
                            <input type="text" id="aditivoObs" name="aditivoObs" class="" value="" required>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <button type="button" id="submit-operos-aditivo" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>