<div class="modal modal-pag2" id="modalFinSrc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="cabecalho-modal">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna100">
                            <h2 class="modal-title fs-2 titulo-pai" id="title-servico-src">
                                Selecione o serviço
                            </h2>
                        </div>
                    </div>
                    <div class="fcad-form-row input-srv-filtrar">
                        <div class="fcad-form-group coluna10">
                            <label>Filtrar:</label>
                        </div>
                        <div style="height: 5px;" class="fcad-form-group coluna50">
                            <input type="text" id="filtrarFinSrc" class="" name="filtrar" value="">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fornecedor-div" class="listas-fin-src">
                    <?php if (!empty($fornecedor)): ?>
                        <table class="table table-hover table-striped" id="f-for-lst">
                            <tbody>
                                <?php foreach ($fornecedor as $vlr): ?>
                                    <tr>
                                        <td style="width: 10%;">
                                            <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                        </td>
                                        <td style="width: 90%;">
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->nome ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
                <div id="cliente-div" class="listas-fin-src">
                    <?php if (!empty($cliente)): ?>
                        <table class="table table-hover table-striped" id="f-cli-lst">
                            <tbody>
                                <?php foreach ($cliente as $vlr): ?>
                                    <tr>
                                        <td style="width: 10%;">
                                            <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                        </td>
                                        <td style="width: 90%;">
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->nome ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
                <div id="plcontarec-div" class="listas-fin-src">
                    <?php if (!empty($plconta)): ?>
                        <table class="table table-hover table-striped" id="f-port-lst">
                            <tbody>
                                <?php foreach ($plconta as $vlr):
                                    if ($vlr->tipo == 'R'):
                                ?>
                                        <tr>
                                            <td style="width: 10%;">
                                                <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                            </td>
                                            <td style="width: 90%;">
                                                <label for="obs-<?= $vlr->id ?>"><?= $vlr->descricao ?></label>
                                            </td>
                                        </tr>
                                <?php
                                    endif;
                                endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
                <div id="plcontapag-div" class="listas-fin-src">
                    <?php if (!empty($plconta)): ?>
                        <table class="table table-hover table-striped" id="f-port-lst">
                            <tbody>
                                <?php foreach ($plconta as $vlr):
                                    if ($vlr->tipo == 'D'):
                                ?>
                                        <tr>
                                            <td style="width: 10%;">
                                                <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                            </td>
                                            <td style="width: 90%;">
                                                <label for="obs-<?= $vlr->id ?>"><?= $vlr->descricao ?></label>
                                            </td>
                                        </tr>
                                <?php
                                    endif;
                                endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
                <div id="portador-div" class="listas-fin-src">
                    <?php if (!empty($portador)): ?>
                        <table class="table table-hover table-striped" id="f-port-lst">
                            <tbody>
                                <?php foreach ($portador as $vlr): ?>
                                    <tr>
                                        <td style="width: 10%;">
                                            <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                        </td>
                                        <td style="width: 90%;">
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->nome ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
                <div id="operacao-div" class="listas-fin-src">
                    <?php if (!empty($operacao)): ?>
                        <table class="table table-hover table-striped" id="f-oper-lst">
                            <tbody>
                                <?php foreach ($operacao as $vlr): ?>
                                    <tr>
                                        <td style="width: 10%;">
                                            <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pickf-for" id="btn-pickf-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                        </td>
                                        <td style="width: 90%;">
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->descricao ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


</html>