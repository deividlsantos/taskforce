<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="tabela-responsive" style="width:80%;">
        <?php
        if (!empty($feriadosResult)) : ?>
            <div class="input-novo-feriado">
                <form class="form-cadastros" action="<?= url('ponto/novo'); ?>">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna50">
                            <label for="descricao">Feriado</label>
                            <input type="text" id="descricao" name="descricao" value="">
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label for="dias">Dia</label>
                            <input type="date" id="dias" name="dias" value="">
                        </div>
                        <div class="fcad-form-group fdsturno">
                            <label for="recorrente">Recorrente</label>
                            <input type="checkbox" id="recorrente" name="recorrente">
                        </div>
                        <div class="fcad-form-group coluna05">
                            <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </form>

                <div class="func-container">
                    <table class="table table-striped table-hover bordered table-vendas tablesorter table-feriados">
                        <thead>
                            <tr>
                                <th>Dia</th>
                                <th>Feriado</th>
                                <th>Recorrente</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($feriadosResult as $key => $feriado) :
                                $partes = explode("@", $feriado);
                                $btn = "<button class='btn btn-secondary list-del' id='f_del' name='f_del' data-id='" . ll_encode($partes[3]) . "'
                                    data-post='" . url('ponto/excluirFeriado') . "'
                                    data-confirm='Tem certeza que deseja excluir esse registro?'>
                                    <i class='fa fa-trash'></i>
                            </button>";
                            ?>
                                <tr>
                                    <td><?= $partes[1]; ?></td>
                                    <td><?= date_fmt($key, "d/m/Y"); ?></td>
                                    <td><?= ($partes[0] == 1) ? "SIM" : "NÃO" ?></td>
                                    <td><?= ($partes[2] == 1) ? "-" : $btn; ?></td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>


                </div>
            </div>
            <div class="rodape" hidden>
                <span class="required">*</span> = Campos Obrigatórios
            </div>
        <?php endif; ?>
    </div>
</div>