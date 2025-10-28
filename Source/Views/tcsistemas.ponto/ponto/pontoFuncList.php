<?php

use Source\Models\EntFun;

 if (!empty($result)): ?>
    <div class="table-responsive">
        <div class="d-flex">
            <div class="flex-grow-1">
                <table class="table table-striped table-hover bordered table-vendas tablesorter"
                    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;">
                    <thead>
                        <tr>
                            <th style="display: none;">ID Ponto</th>
                            <th style="width: 5%;">Matrícula</th>
                            <th>Funcionário</th>
                            <th style="width: 6%;">Competência</th>
                            <th style="width: 5%;">Revisar</th>
                            <th style="width: 5%;">Imprimir</th>
                            <th style="width: 5%;">Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $ponto):
                            $id = ll_encode($ponto->id);

                        ?>
                            <tr>
                                <td style="display: none;"><?= $ponto->id; ?></td>
                                <?php
                                foreach ($func as $vlrFunc):
                                    $entfun = new EntFun();
                                    if ($vlrFunc->id == $ponto->id_func):
                                        $entfun = $entfun->findByIdEnt($vlrFunc->id);
                                ?>
                                        <td style="width: 6%;"><?= $entfun->matricula; ?></td>
                                        <td><?= $vlrFunc->nome . " " . $vlrFunc->fantasia; ?></td>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                                <td><?= $ponto->mes . "/" . $ponto->ano; ?></td>
                                <td>
                                    <a class="btn_ponto_view" id="p_review" name="p_review"
                                        href="<?= url("ponto/editFolhas/{$id}"); ?>">
                                        <img src="<?= url("Source/Images/review.png"); ?>" height="20" width="20">
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" class="btn_ponto_view" id="p_pdf" name="p_pdf"
                                        href="<?= url("ponto/pdf/{$id}"); ?>">
                                        <img src="<?= url("Source/Images/impressora.png"); ?>" height="20" width="20">
                                    </a>
                                </td>
                                <td>
                                    <button class="btn_ponto_view" id="p_del" name="p_del" data-id="<?= $id; ?>"
                                        data-post="<?= url("ponto/excluir") ?>"
                                        data-confirm="Tem certeza que deseja excluir esse registro?">
                                        <img src="<?= url("Source/Images/del.png"); ?>" height="20" width="20">
                                    </button>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>