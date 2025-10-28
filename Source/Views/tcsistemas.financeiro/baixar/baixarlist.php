<div class="tabela-responsive">
    <table id="baixar-list" class="table table-hover tab-list">
        <thead>
            <tr>
                <th>Título</th>
                <th>Cli/For</th>
                <th>Vencimento</th>
                <th>Saldo</th>
                <th class="valor-baixa"><span id="allcopyBx" class="fa-solid fa-chevron-right"></span>Valor da baixa</th>
                <th style="width: 0.5%;">Desconto</th>
                <th>Outros</th>
                <th>Valor Líquido</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($baixar)) :
                foreach ($baixar as $b) :
                    if ($b->tipo == 'receita') {
                        $cliFor = $b->id_entc;
                        $entidades = $cliente;
                    } else {
                        $cliFor = $b->id_entf;
                        $entidades = $fornecedor;
                    } ?>
                    <tr class="registrosBx">
                        <td hidden><input name="idBx_<?= $b->id; ?>" value="<?= $b->id; ?>"></td>
                        <td hidden><input name="tipoBx_<?= $b->id; ?>" value="<?= $b->tipo; ?>"></td>
                        <td><?= $b->titulo; ?></td>
                        <td>
                            <?php foreach ($entidades as $ent):
                                if ($ent->id == $cliFor):
                                    echo mb_strimwidth($ent->nome, 0, 25, "...");
                                endif;
                            endforeach;
                            ?>
                        </td>
                        <td><?= date_fmt($b->dataven, 'd/m/Y'); ?></td>
                        <td class="inputBx saldoBx"><?= moedaBR($b->saldo); ?></td>
                        <td class="">
                            <div class="inputBx-container">
                                <span class="fa-solid fa-chevron-right"></span>
                                <input name="vbaixaBx_<?= $b->id; ?>" class="inputBx vbaixaBx mask-money" required>
                            </div>
                        </td>
                        <td class=""><input name="vdescBx_<?= $b->id; ?>" class="inputBx descontoBx mask-money"></td>
                        <td class=""><input name="voutrosBx_<?= $b->id; ?>" class="inputBx outrosBx mask-money"></td>
                        <td class="inputBx liquidoBx"></td>
                        <td><button type="button" class="btn btn-acao-small deleteBx"><i class="fa-regular fa-rectangle-xmark vermelho"></i></button></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="100%"><a href="<?= url("contas"); ?>">SELECIONE OS LANÇAMENTOS PARA BAIXAR</a></td>
                </tr>
            <?php
            endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Quantidade de Títulos: <span id="totalRegistrosBx">0</span></strong></td>
                <td><strong id="totalSaldoBx">0,00</strong></td>
                <td class="totaisInputBx"><strong id="totalVbaixaBx">0,00</strong></td>
                <td class="totaisInputBx"><strong id="totalDescontoBx">0,00</strong></td>
                <td class="totaisInputBx"><strong id="totalOutrosBx">0,00</strong></td>
                <td><strong id="totalLiquidoBx">0,00</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>