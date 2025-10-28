<?php

namespace Source\Controllers;

use DateTime;
use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Equipamentos;
use Source\Models\Log;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Servico;

class MedicaoController
{

    protected $view;
    protected $message;
    protected $user;

    public function __construct()
    {
        $this->view = new Engine(CONF_APP_PATH . "Views/", "php");
        $this->message = new Message();
        $this->user = Auth::user();

        if (!$this->user) {
            $this->message->error("Para acessar é preciso logar-se")->flash();
            redirect("");
        }
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $parametroEquipamentos = (new Emp2())->findById($id_empresa)->servicosComEquipamentos;

        // Verifico se os campos estão preenchidos
        if (empty($data['datai']) || empty($data['dataf']) || empty($data['qtde']) || empty($data['operador'])) {
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->warning("PREENCHA TODOS OS CAMPOS.")->render();
                $json['status'] = "error";
            } else {
                $json['message'] = "PREENCHA TODOS OS CAMPOS.";
            }
            echo json_encode($json);
            return;
        }

        $datai = new DateTime($data['datai']);
        $dataf = new DateTime($data['dataf']);

        // Verifico se a data final não é mais antiga que a data inicial
        if ($dataf < $datai) {
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->warning("A DATA FINAL NÃO PODE SER MAIS ANTIGA QUE A DATA INICIAL.")->render();
                $json['status'] = "error";
            } else {
                $json['message'] = "A DATA FINAL NÃO PODE SER MAIS ANTIGA QUE A DATA INICIAL.";
            }
            echo json_encode($json);
            return;
        }

        // Dados do formulário
        $datai = $data['datai'];
        $dataf = $data['dataf'];
        $tarefa = $data['tarefaId'];
        $quantidade = $data['qtde'];
        $operador = $data['operador'];
        $eqpto = !empty($data['eqp']) && isset($data['eqp']) ? $data['eqp'] : null;
        $obs = $data['obs'];

        // Verifico se existe medição para a tarefa
        $medicao = (new Os2_1())->findByOs2($tarefa);
        // Trago a tarefa
        $os2 = (new Os2())->findById($tarefa);

        $aditivo = "N";
        if (!empty($os2->aditivo)) {
            $aditivo = $os2->aditivo;
        }

        $totalContratado = $os2->qtde;

        $totalMedido = 0;

        if (!empty($medicao)) {
            foreach ($medicao as $m) {
                if (isset($data['id']) && $m->id == ($data['id'])) {
                    continue;
                }
                $totalMedido += $m->qtde;
            }
        }

        $saldo = $totalContratado - $totalMedido;

        if ($saldo < $quantidade) {
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->warning("TOTAL MEDIDO MAIOR QUE O TOTAL CONTRATADO.")->render();
                $json['status'] = "error";
            } else {
                $json['message'] = "TOTAL MEDIDO MAIOR QUE O TOTAL CONTRATADO.";
            }
            echo json_encode($json);
            return;
        }

        $totalMedido = $totalMedido + $quantidade;
        $saldo = $saldo - $quantidade;

        // Se o campo id vier preenchido, é uma atualização
        if (isset($data['id'])) {
            $id = $data['id'];
            $novaMedicao = (new Os2_1())->findById($id);
            $antes = clone $novaMedicao->data();
            $acao = "U";
        } else // Se não, é uma nova medição
        {
            $novaMedicao = new Os2_1();
            $antes = null;
            $acao = "C";
        }

        /***RESTRIÇÃO DAS DATAS RETIRADO**/

        // if (isset($data['id'])) {
        //     // Se for uma atualização, verifico se a alteração não conflita com outra medição
        //     $id = $data['id'];
        //     $registros = (new Os2_1())->find("datai < :dataf AND dataf > :datai AND id_os2 = :id_os2 AND id != :id", "datai={$datai}&dataf={$dataf}&id_os2={$tarefa}&id={$id}", "COUNT(*) as conflitos")->fetch(true);
        //     if ($registros[0]->conflitos > 0) {
        //         if ($data['retaguarda']) {
        //             $json['message'] = $this->message->warning("JÁ EXISTE REGISTRO PRO PERÍODO SELECIONADO.")->render();
        //         } else {
        //             $json['message'] = "JÁ EXISTE REGISTRO PRO PERÍODO SELECIONADO.";
        //         }
        //         echo json_encode($json);
        //         return;
        //     }
        // } else {
        //     // Se for uma nova medição, verifico se a data de início é maior que a data final da última medição
        //     if (!empty($medicao)) {
        //         $ultimaMedicao = end($medicao);
        //         if ($ultimaMedicao->dataf > $datai) {
        //             if ($data['retaguarda']) {
        //                 $json['message'] = $this->message->warning("DATA DE INÍCIO DEVE SER MAIOR QUE A DATA FINAL DA ÚLTIMA MEDIÇÃO.")->render();
        //             } else {
        //                 $json['message'] = "DATA DE INÍCIO DEVE SER MAIOR QUE A DATA FINAL DA ÚLTIMA MEDIÇÃO.";
        //             }
        //             echo json_encode($json);
        //             return;
        //         }
        //     }
        // }

        // Salvo os dados no objeto
        $novaMedicao->id_emp2 = $id_empresa;
        $novaMedicao->id_os1 = $os2->id_os1;
        $novaMedicao->id_os2 = $os2->id;
        $novaMedicao->datai = $datai;
        $novaMedicao->dataf = $dataf;
        $novaMedicao->qtde = $quantidade;
        $novaMedicao->id_operador = $operador;
        $novaMedicao->id_equipamento = $eqpto;
        $novaMedicao->obs = $obs;
        $novaMedicao->id_users = $id_user; 

        // Salvo no banco
        if (!$novaMedicao->save()) {
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                //$json['message'] = $novaMedicao->fail()->getMessage();
                $json['message'] = $this->message->error("ERRO AO TENTAR SALVAR MEDIÇÃO.")->render();
                $json['status'] = "error";
            } else {
                $json['message'] = $novaMedicao->fail()->getMessage();
                //$json['message'] = "ERRO AO TENTAR SALVAR MEDIÇÃO.";
            }
            echo json_encode($json);
            return;
        } else {
            if ($os2->status == "A") {
                $os2antes = clone $os2->data();
                $os2acao = "U";

                $os2->status = "I";

                if ($os2->save()) {
                    $os1 = (new Os1())->findById($os2->id_os1);
                    $os1antes = clone $os1->data();
                    $os1acao = "U";

                    if ($os1->id_status == 2) {
                        $os1->id_status = 3;
                    }

                    $os1->save();
                    $logOs1 = new Log();
                    $logOs1->registrarLog($os1acao, $os1->getEntity(), $os1->id, $os1antes, $os1->data());
                }

                $log = new Log();
                $log->registrarLog($os2acao, $os2->getEntity(), $os2->id, $os2antes, $os2->data());
            }
        }

        $log = new Log();
        $log->registrarLog($acao, $novaMedicao->getEntity(), $novaMedicao->id, $antes, $novaMedicao->data());

        $oper = new Ent();
        $novaMedicao->operador = $oper->findById($operador)->nome;

        $equipamento = new Equipamentos();
        $novaMedicao->equipamento = !empty($eqpto) ? $equipamento->findById($eqpto)->descricao : "";
        $novaMedicao->servicosComEquipamentos = $parametroEquipamentos;

        // Se for uma atualização, retorno a mensagem de sucesso
        if (isset($data['id'])) {
            $json['status'] = "success";
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->success("MEDIÇÃO ATUALIZADA COM SUCESSO.")->render();
                $json['medicao'] = $novaMedicao;
                $json['tarefaId'] = $novaMedicao->id_os2;
            } else {
                $json['message'] = "MEDIÇÃO ATUALIZADA COM SUCESSO.";
            }
        } else // Se for uma nova medição, retorno a mensagem de sucesso e o id da medição
        {
            $json['id'] = $novaMedicao->id;
            $json['tarefaId'] = $novaMedicao->id_os2;
            $json['delete'] = url("medicao/excluir/" . ll_encode($novaMedicao->id));
            $json['edit'] = url("medicao");
            $json['status'] = "success";
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->success("MEDIÇÃO SALVA COM SUCESSO.")->render();
                $novaMedicao->delete = url("medicao/excluir/" . ll_encode($novaMedicao->id));
                $novaMedicao->edit = url("medicao");
                $medicaoData = objectsToArray($novaMedicao);
                $json['medicao'] = $medicaoData;
            } else {
                $json['message'] = "MEDIÇÃO SALVA COM SUCESSO.";
            }
        }

        $json['unidade'] = (new Servico())->findById($os2->id_servico)->medida;
        $json['total'] = fmt_numeros($totalContratado);
        $json['medido'] = fmt_numeros($totalMedido);
        $json['pendente'] = fmt_numeros($saldo);
        $json['aditivo'] = $aditivo;

        // Retorno o json para o usuário
        echo json_encode($json);
    }

    public function atualiza($data)
    {
        $id_empresa = $this->user->id_emp2;
        $id = $data['id'];

        $parametroEquipamentos = (new Emp2())->findById($id_empresa)->servicosComEquipamentos;

        $medicao = (new Os2_1())->findByOs2($id);

        $operador = new Ent();
        $equipamento = new Equipamentos();

        $os2 = (new Os2())->findById($id);

        $servico = (new Servico())->findById($os2->id_servico);

        $totalContratado = $os2->qtde;
        $totalMedido = 0;
        $totalPendente = 0;

        if (!empty($medicao)) {
            foreach ($medicao as $m) {
                $m->operador = !empty($m->id_operador) ? $operador->findById($m->id_operador)->nome : "";
                $m->equipamento = !empty($m->id_equipamento) ? $equipamento->findById($m->id_equipamento)->descricao : "";
                $m->obs = !empty($m->obs) ? $m->obs : "";
                $totalMedido += $m->qtde;
                $m->delete = url("medicao/excluir/" . ll_encode($m->id));
                $m->edit = url("medicao");
                $m->servicosComEquipamentos = $parametroEquipamentos;
            }
            $medicaoData = objectsToArray($medicao);
            $json['status'] = "success";
            $json['tarefaId'] = $os2->id;
            $json['medicao'] = $medicaoData;
            $json['servico'] = $servico->nome;
            $json['total'] = fmt_numeros($totalContratado) . " " . $servico->medida;
            $json['medido'] = fmt_numeros($totalMedido) . " " . $servico->medida;
            $json['pendente'] = fmt_numeros($totalContratado - $totalMedido) . " " . $servico->medida;
        } else {
            $json['tarefaId'] = $os2->id;
            $json['total'] = fmt_numeros($totalContratado) . " " . $servico->medida;
            $json['medido'] = fmt_numeros($totalMedido) . " " . $servico->medida;
            $json['pendente'] = fmt_numeros($totalContratado) . " " . $servico->medida;
            $json['status'] = "error";
            $json['servico'] = $servico->nome;
            $json['message'] = "NENHUM REGISTRO ENCONTRADO.";
        }

        echo json_encode($json);
    }

    public function atualiza2($data)
    {
        $id = $data['id'];

        $medicao = (new Os2_1())->findById($id);

        if (!empty($medicao)) {
            $medicaoData = objectsToArray($medicao);
            $json['status'] = "success";
            $json['medicao'] = $medicaoData;
        } else {
            $json['status'] = "error";
            $json['message'] = "NENHUM REGISTRO ENCONTRADO.";
        }

        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_medicao = ll_decode($data['id_medicao']);

        if (ll_intValida($id_medicao)) {
            $medicao = (new Os2_1())->findById($id_medicao);
            $antes = clone $medicao->data();

            if (!$medicao->destroy()) {
                if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                    $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                } else {
                    $json['message'] = "ERRO AO TENTAR EXCLUIR!";
                }
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $medicao->getEntity(), $medicao->id, $antes, null);

            $medicaoAtt = (new Os2_1())->find("id_os2 = :id_os2", "id_os2={$medicao->id_os2}", "*", false)->fetch(true);
            $os2 = (new Os2())->findById($medicao->id_os2);
            $totalContratado = $os2->qtde;
            $aditivo = "N";

            if (!empty($aditivo)) {
                $aditivo = $os2->aditivo;
            }

            $totalMedido = 0;
            if (!empty($medicaoAtt)) {
                foreach ($medicaoAtt as $m) {
                    $totalMedido += $m->qtde;
                }
            }
            $saldo = $totalContratado - $totalMedido;

            $json['status'] = "success";
            if (isset($data['retaguarda']) && $data['retaguarda'] == true) {
                $json['message'] = $this->message->success("REGISTRO EXCLUÍDO COM SUCESSO.")->render();
                $json['tarefaId'] = $medicao->id_os2;
            } else {
                $json['message'] = "REGISTRO EXCLUÍDO COM SUCESSO.";
            }
            $json['unidade'] = (new Servico())->findById($os2->id_servico)->medida;
            $json['total'] = fmt_numeros($totalContratado);
            $json['medido'] = fmt_numeros($totalMedido);
            $json['pendente'] = fmt_numeros($saldo);
            $json['aditivo'] = $aditivo;

            echo json_encode($json);
        }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
