<?php

use Source\Models\Ent;
use Source\Models\Os2;
use Source\Models\Servico;

if (!empty($ordens)):
    foreach ($ordens as $vlr):
        $btnPdf = '<button class="btn btn-secondary list-edt file-pdf file-pdf-ajax" data-url="' . url("ordens/verifica_os") . '" data-id="' . ll_encode($vlr->id) . '">
                                        <i class="fa fa-file-pdf"></i>
                                    </button>';

        //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
        $mostraOs = true;
        if ($user->tipo == 3) {
            $mostraOs = false;
        }

        $execucao = "";
        $tarefas = (new Os2())->findByIdOs($vlr->id);

        if (!empty($tarefas)) {
            foreach ($tarefas as $t) {
                if ($t->id_colaborador == $user->id_ent) {
                    $mostraOs = true;
                    break;
                }
            }
        }

        if (!$mostraOs) {
            continue;
        }
        //* FIM DA VERIFICAÇÃO TEMPORÁRIA *//


        // Formatar execução usando os campos calculados da query
        $execucao = date_fmt($vlr->data_execucao, "d/m/Y");
        if ($vlr->hora_execucao > 0) {
            $execucao .= " " . secondsToTime($vlr->hora_execucao);
        } else {
            $execucao .= " 00:00";
        }

        // Tooltip e idTarefa - buscar apenas se necessário
        $tooltipTarefas = "";
        $idTarefa = "";

        // Se não buscou tarefas ainda (usuário não é tipo 3)
        if ($user->tipo != 3) {
            $tarefas = (new Os2())->findByIdOs($vlr->id);
        }

        if (!empty($tarefas)) {
            foreach ($tarefas as $srv) {
                $srv->colaborador = !empty($srv->id_colaborador) ? (new Ent())->findById($srv->id_colaborador)->nome : "...";
                $srv->servico = !empty($srv->id_servico) ? (new Servico())->findById($srv->id_servico)->nome : "...";

                $tooltipTarefas .= "TAREFA: #" . $srv->id . " | SERVIÇO: " . mb_strimwidth($srv->servico, 0, 30, "...") . " | OPERADOR: " . mb_strimwidth($srv->colaborador, 0, 30, "...") . "<br>";
            }

            // Pegar primeira tarefa para o ID
            $primeiraTask = reset($tarefas);
            $idTarefa = ll_encode($primeiraTask->id);
        }
?>
        <tr data-tooltip="<?= $tooltipTarefas; ?>">
            <td><?= $vlr->id; ?></td>
            <td><?= $vlr->controle; ?></td>
            <td data-status="<?= $vlr->status_order; ?>">
                <?php
                if (!empty($status)) :
                    foreach ($status as $st) :
                        if ($st->id == $vlr->id_status) :
                ?>
                            <div class="stat-os"><span style="background-color: <?= $st->cor; ?>;"><?= $st->descricao ?></span></div>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>

            </td>
            <td>
                <?php
                //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
                if ($user->tipo != 3) :
                    if ($vlr->concluir == 'S' && $vlr->id_status != 5) :
                ?>
                        <div class="stat-os">
                            <span data-tooltip="TAREFAS FINALIZADAS. CONCLUA A OS." class="blinking-text" style="background-color: red;">FINALIZADA</span>
                        </div>
                <?php
                    endif;
                endif;
                ?>
            </td>
            <?php
            if (!empty($tipo)):
                if (count($tipo) > 1):
                    foreach ($tipo as $t):
                        if ($t->id == $vlr->id_tipo):
            ?>
                            <td class="os-tipo"><?= $t->descricao; ?></td>
            <?php
                        endif;
                    endforeach;
                endif;
            endif;
            ?>
            <td> <?= $execucao; ?></td>
            <td style="text-align: left;" data-idcli="<?= $vlr->id_cli; ?>">
                <?php
                if (!empty($cliente)) :
                    foreach ($cliente as $cli) :
                        if ($cli->id == $vlr->id_cli) :
                            echo mb_strimwidth($cli->nome, 0, 50, '...');
                        endif;
                    endforeach;
                endif;
                ?>
            </td>
            <?php
            //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
            if ($user->tipo != 3) :
            ?>
                <td><?= moedaBR($vlr->vtotal); ?></td>
            <?php
            endif;
            ?>
            <td class="coluna-acoes">
                <a href="<?= url("oper_ordens/pdf-os/") . $idTarefa; ?>" target="_blank"
                    class="btn btn-secondary list-edt file-pdf"><i class="fa fa-file-pdf"></i></a>
            </td>
            <td class="coluna-acoes">
                <?= $btnPdf; ?>
            </td>
            <td class="coluna-acoes">
                <a href="<?= url("ordens/form/") . ll_encode($vlr->id); ?>"
                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
            </td>
            <?php
            //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
            if ($user->tipo != 3) :
            ?>
                <td>
                    <a class="btn btn-secondary list-del" title="" href="#"
                        data-post="<?= url("ordens/excluir"); ?>" data-action="delete"
                        data-confirm="Tem certeza que deseja apagar esse registro?"
                        data-id_ordens="<?= ll_encode($vlr->id); ?>"><i class="fa fa-trash"></i></a>
                </td>
            <?php
            endif;
            ?>
        </tr>
    <?php endforeach;
else: ?>
    <tr>
        <td colspan="100%">NENHUM REGISTRO ENCONTRADO</td>
    </tr>
<?php endif; ?>