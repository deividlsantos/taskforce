<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Equipamentos;
use Source\Models\Log;
use Source\Models\Materiais;
use Source\Models\Obras;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Os3;
use Source\Models\Os5;
use Source\Models\Os6;
use Source\Models\Servico;
use Source\Models\Users;


class OperOrdens extends OperController
{

    public function __construct()
    {
        // $this->view = new Engine(CONF_APP_PATH . "Views/tcsistemas.os-oper/", "php");
        // $this->message = new Message();
        // $this->user = Auth::user();

        // if (!$this->user) {
        //     $this->message->error("Para acessar é preciso logar-se")->flash();
        //     redirect("");
        // }

        parent::__construct();
    }

    public function index($data): void
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $hoje = date("Y-m-d");
        $hrAtual = date('H:i');
        $horaAtualSegundos = timeToSeconds($hrAtual);

        $status = $data['status'] ?? null;

        $os2 = (new Os2())->findByOper($id_func);

        $cliente = (new ent())->find(
            "tipo = :tipo",
            "tipo=1"
        )->fetch(true);

        $servicos = (new Servico())->find()->fetch(true);

        $equipamentos = (new Equipamentos())->find()->fetch(true);

        $ent = (new Ent())->find(
            "tipo = :tipo",
            "tipo=3"
        )->fetch(true);

        $opers = (new Users())->find(
            "tipo = :tipo",
            "tipo=2"
        )->fetch(true);

        foreach ($ent as $item) {
            foreach ($opers as $oper) {
                if ($item->id == $oper->id_ent) {
                    $operador[] = $oper;
                }
            }
        }

        if (!empty($os2)) {
            //** DESCOMENTAR O CÓDIGO ABAIXO PARA TAREFAS DE ORÇAMENTOS NÃO APARECEREM MAIS PRO OPERADOR */          
            // Filtra as tarefas de $os2 removendo aquelas cujo $os1->id_status seja 8
            // $os2 = array_filter($os2, function ($os) {
            //     $os1 = (new Os1())->findById($os->id_os1);
            //     return $os1->id_status != 8; // Exclui tarefas com id_status == 8
            // });

            foreach ($os2 as $os) {
                $os1 = (new Os1())->findById($os->id_os1);

                foreach ($cliente as $cli) {
                    if ($cli->id == $os1->id_cli) {
                        $os->cliente = $cli->nome;
                        $segmento = (new Obras())->find("id_ent_cli = :id_cli", "id_cli={$os1->id_cli}")->fetch();
                        $os->segmento = !empty($segmento) ? $segmento->nome : null;
                    }
                }

                $servico = (new Servico())->findById($os->id_servico);
                $os->servico = $servico->nome;
                $os->controle = $os1->controle;
            }

            $andamento = array_filter($os2, function ($item) {
                return $item->status == "I";
            });

            $oshoje = array_filter($os2, function ($item) use ($hoje) {
                return $item->status == "A" && $item->dataexec == $hoje;
            });

            $pendentes = array_filter($os2, function ($item) use ($hoje) {
                return $item->status == "A" && $item->dataexec <= $hoje;
            });

            $futuras = array_filter($os2, function ($item) use ($hoje) {
                return $item->status == "A" && $item->dataexec > $hoje;
            });

            $concluidas = array_filter($os2, function ($item) {
                return $item->status == "C";
            });

            $pausadas = array_filter($os2, function ($item) {
                return $item->status == "P";
            });

            $canceladas = array_filter($os2, function ($item) {
                return $item->status == "D";
            });

            usort($pendentes, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($futuras, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($concluidas, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($pausadas, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($canceladas, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($andamento, function ($a, $b) {
                if ($a->dataexec === $b->dataexec) {
                    return $a->horaexec <=> $b->horaexec; // Compara horaexec se as datas forem iguais
                }
                return strcmp($a->dataexec, $b->dataexec); // Compara dataexec
            });

            usort($oshoje, function ($a, $b) {
                return $a->horaexec <=> $b->horaexec; // Compara horaexec
            });
        } else {
            $pendentes = [];
            $os2 = [];
            $concluidas = [];
            $andamento = [];
            $pausadas = [];
            $futuras = [];
            $canceladas = [];
            $oshoje = [];
        }

        $empresa = (new Emp2())->findById($id_empresa);

        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "nav" => "Tarefas",
            "navback" => "oper_dash",
            "navlink" => "oper_ordens"
        ];

        echo $this->view->render("ordens/ordensListMob", [
            "front" => $front,
            "pendentes" => $pendentes,
            "os2" => $os2,
            "concluidas" => $concluidas,
            "andamento" => $andamento,
            "pausadas" => $pausadas,
            "hoje" => $oshoje,
            "futuras" => $futuras,
            "canceladas" => $canceladas,
            "status" => $status,
            "servico" => $servicos,
            "operador" => $operador,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
        ]);
    }

    public function aditivo($data)
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);

        $ordemOs1 = $data['aditivoOS1'];
        $ordemOperador = $data['aditivoOperador'];
        $ordemServico = $data['aditivoServico'];

        $where = "";
        $aditivo = "N";
        if ($empresa->tarefasAditivas == 'X') {
            $where = " AND aditivo = 'S' ";
            $aditivo = "S";
        }
        $os2Existente = (new Os2())->find(
            "id_os1 = :id_os1 AND id_colaborador = :id_colaborador AND id_servico = :id_servico {$where}",
            "id_os1={$ordemOs1}&id_colaborador={$ordemOperador}&id_servico={$ordemServico}"
        )->fetch();

        if ($os2Existente) {
            $json['message'] = "Já existe uma tarefa adicional para este operador e serviço nesta OS.";
            echo json_encode($json);
            return;
        }

        $servicos = (new Servico())->find()->fetch(true);

        $valor = 0;
        $tempo = 0;

        foreach ($servicos as $serv) {
            if ($serv->id == $data['aditivoServico']) {
                $valor = $serv->valor;
                $tempo = $serv->tempo;
            }
        }


        $tempoTotal = $tempo * $data['aditivoQtde'];
        $hora = timeToSeconds($data['aditivoHora']);

        $os2 = new Os2();
        $os2->id_emp2 = $id_empresa;
        $os2->id_os1 = $data['aditivoOS1'];
        $os2->id_colaborador = $data['aditivoOperador'];
        $os2->id_servico = $data['aditivoServico'];
        $os2->qtde = $data['aditivoQtde'];
        $os2->dataexec = $data['aditivoData'];
        $os2->horaexec = $hora;
        $os2->obs = $data['aditivoObs'];
        $os2->assinado = "N";
        $os2->vunit = $valor;
        $os2->vtotal = $valor * $data['aditivoQtde'];
        $os2->tempo = $tempoTotal;
        $os2->horafim = $hora + $tempoTotal;
        $os2->aditivo = $aditivo;
        $os2->id_users = $id_user;

        if (!$os2->save()) {
            $json['message'] = "ERRO AO CRIAR TAREFA";
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog("C", $os2->getEntity(), $os2->id, null, $os2->data());

        $os1 = (new Os1())->findById($data['aditivoOS1']);
        $antes = clone $os1->data();
        $totalOs1 = $os1->total + $os2->vtotal;
        $os1->total = $totalOs1;
        $os1->id_users = $id_user;
        $os1->save();

        $log = new Log();
        $log->registrarLog("U", $os1->getEntity(), $os1->id, $antes, $os1->data());

        $json['message'] = "Tarefa adicionada com sucesso";
        $json['reload'] = true;
        echo json_encode($json);
    }

    public function ordem($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);

        $os2 = (new Os2())->findById($data['id']);

        $os1 = (new Os1())->findById($os2->id_os1);

        $materiais = (new Materiais())->find()->fetch(true);

        $materiaisData = objectsToArray($materiais);

        $os3 = (new Os3())->find(
            "id_os1 = :id_os1 AND id_os2 = :id_os2",
            "id_os1={$os2->id_os1}&id_os2={$os2->id}"
        )->fetch(true);


        if (!empty($os3)) {
            foreach ($os3 as $item) {
                foreach ($materiais as $mat) {
                    if ($mat->id == $item->id_materiais) {
                        $item->descricao = $mat->descricao;
                        $item->delete = url("oper_ordens/deletemat");
                    }
                }
            }

            $os3Data = objectsToArray($os3);
        } else {
            $os3Data = [];
        }

        $medicoes = (new Os2_1())->findByOs2($os2->id);


        if (!empty($medicoes)) {
            foreach ($medicoes as $item) {
                $item->datai = date_fmt($item->datai, "d/m/y h:i");
                $item->dataf = date_fmt($item->dataf, "d/m/y h:i");
                $item->delete = url("medicao/excluir/" . ll_encode($item->id));
                $item->edit = url("medicao");
            }
            $medicoesData = objectsToArray($medicoes);
        } else {
            $medicoesData = [];
        }

        $cliente = (new Ent())->findById($os1->id_cli);

        $servico = (new Servico())->findById($os2->id_servico);

        $label = str_to_single(($empresa->labelFiliais));
        $os2->labelFiliais = !empty($label) ? $label : null;
        $segmento = (new Obras())->find("id_ent_cli = :id_cli", "id_cli={$os1->id_cli}")->fetch();
        $os2->segmento = !empty($segmento) ? $segmento->nome : null;

        $os6 = new Os6();
        $arquivos = $os6->findByIdOs2($os2->id);

        $arquivosData = [];
        if ($arquivos) {
            foreach ($arquivos as $arquivo) {

                $nomeCompleto = $arquivo->nome_arquivo;
                // Separamos o nome da extensão
                $nomeSemExtensao = pathinfo($nomeCompleto, PATHINFO_FILENAME);
                $extensao = pathinfo($nomeCompleto, PATHINFO_EXTENSION);

                // Limitamos o nome a 8 caracteres, se necessário
                if (strlen($nomeSemExtensao) > 8) {
                    $nomeFormatado = substr($nomeSemExtensao, 0, 8) . '...' . $extensao;
                } else {
                    $nomeFormatado = $nomeCompleto; // Nome menor que 8 caracteres não é alterado
                }

                $arquivosData[] = [
                    'nome' => $nomeFormatado,
                    'id' => $arquivo->data->id,
                    'tipo' => $arquivo->data->tipo,
                    'arquivo' => $arquivo->data->arquivo,
                    'url' => CONF_FILES_URL,
                    'delete' => url("oper_ordens/deletearq")
                ];
            }
        }

        // Adicionar os dados extraídos ao objeto os2
        $os2->data()->arquivos = $arquivosData;

        $os2->cliente = $cliente->nome;
        $os2->servico = $servico->nome;
        $os2->unidade = $servico->medida;
        $os2->medicao = $servico->medicao;
        $os2->horaexec = secondsToTime($os2->horaexec);
        $os2->obslabel = !empty($os2->obs) ? mb_strimwidth($os2->obs, 0, 30, '...') : "...";
        $os2->materiais = $materiaisData;
        $os2->os3 = $os3Data;
        $os2->idCript = ll_encode($os2->id);
        $os2->medicoes = $medicoesData;
        $os2->statusOS1 = $os1->id_status;
        $os2->controle = $os1->controle;

        echo json_encode($os2->data);
    }

    /**
     * @param [type] $data
     * @param $data['acao'] = stt(iniciar) / psr(pausar) / end(concluir) / res(retomar)
     * @return void
     */
    public function activity($data)
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);

        $id = $data['tarefa'];
        $acao = $data['acao'];

        $os2 = (new Os2())->findById($id);
        $antesOs2 = clone $os2->data();
        $os1 = (new Os1())->findById($os2->id_os1);
        $antesOs1 = clone $os1->data();

        $os2->id_users = $id_user;
        $os1->id_users = $id_user;

        $attOs1 = true; // Flag para atualizar a OS1 caso seja true

        if ($acao == 'stt' || $acao == 'res') { //se a ação for iniciar(stt) ou retomar(res)

            //* Verifica se já existe uma tarefa em andamento *

            if ($empresa->bloqueia2tarefasPorOper == 'X') {
                $verStatus = (new Os2())->find(
                    "id_emp2 = :id_emp2 and id_colaborador = :id_colaborador and status = :status",
                    "id_emp2={$os2->id_emp2}&id_colaborador={$id_func}&status=I"
                )->fetch(true);

                if ($verStatus) {
                    $json['denied'] = "JÁ EXISTE UMA TAREFA EM ANDAMENTO";

                    echo json_encode($json);
                    return;
                }
            }

            //* Se a ação for iniciar *//
            if ($acao == 'stt') {
                $os5 = new Os5(); //* Crio novo registro na OS5 e salvo */
                $antes = null;
                $acaolog = "C";
                $os5->id_emp2 = $os2->id_emp2;
                $os5->id_os1 = $os2->id_os1;
                $os5->id_os2 = $os2->id;
                $os5->dhi = date("Y-m-d H:i:s");
                $os5->status = 'T';
                $os5->id_users = $id_user;
                $os5->save();

                $log = new Log();
                $log->registrarLog('C', $os5->getEntity(), $os5->id, $antes, $os5->data());
            } elseif ($acao == 'res') { //* Se a ação for retomar, significa que existe um registro pausa pra tarefa, então entro no laço pra editar esse registro*//
                $os5 = (new Os5())->find(
                    "id_os1 = :id_os1 and id_os2 = :id_os2 and status = :status",
                    "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=P"
                )->order("id DESC")->limit(1)->fetch(); //* Primeiro encontro na tabela OS5 o registro de pausa para informar o término da pausa *//
                $antes = clone $os5->data(); //** Preparo dados antigos pra enviar pro log sobre o update*/
                $acaolog = "U"; //* Informo no log que é um update */
                $os5->dhf = date("Y-m-d H:i:s"); //* Gravo o fim da pausa */
                $tempo = strtotime($os5->dhf) - strtotime($os5->dhi);
                $os5->tempo = $tempo; //* Gravo o tempo que durou a pausa */
                $os5->id_users = $id_user; //* Gravo quem fez a ação */
                $os5->save();

                $log = new Log();
                $log->registrarLog($acaolog, $os5->getEntity(), $os5->id, $antes, $os5->data());

                $os5retomada = new Os5(); //* Depois de atualizar o registro da pausa, crio um novo registro na OS5 para informar a retomada da tarefa */
                $antesRetomada = null; //** Preparo dados pra enviar pro log - como é registro novo então não existem valores antigos */
                $acaoRetomada = "C"; //** Informo no log que é um insert */
                $os5retomada->id_emp2 = $os2->id_emp2; //*Gravo a empresa */
                $os5retomada->id_os1 = $os2->id_os1; //** Gravo a OS1 */
                $os5retomada->id_os2 = $os2->id; //** Gravo a OS2 */
                $os5retomada->dhi = date("Y-m-d H:i:s"); //** Gravo a data e hora de início do registro */
                $os5retomada->status = 'T'; //** Gravo o status como T (tempo desse registro é de produção) */
                $os5retomada->id_users = $id_user;
                $os5retomada->save();

                $log2 = new Log();
                $log2->registrarLog($acaoRetomada, $os5retomada->getEntity(), $os5retomada->id, $antesRetomada, $os5retomada->data());
            }

            $os2->status = 'I'; //* Pra qualquer uma das duas ações acima, o status da terefa passa a ser I(em andamento) */
        } elseif ($acao == 'psr') { //** Se a ação for pausar (psr), então significa que existe um registro de produção aberto pra essa tarefa, então entro no laço pra editar esse registro */
            $os5 = (new Os5())->find(
                "id_os1 = :id_os1 and id_os2 = :id_os2 and status = :status",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=T"
            )->order("id desc")->limit(1)->fetch(); //** Primeiro encontro na tabela OS5 o registro de produção pra informar o término da produção */
            $antes = clone $os5->data(); //** Preparo dados antigos pra enviar pro log sobre o update*/
            $acaolog = "U"; //** Informo no log que é um update */
            $os5->dhf = date("Y-m-d H:i:s"); //** Gravo o fim da produção */
            $tempo = strtotime($os5->dhf) - strtotime($os5->dhi);
            $os5->tempo = $tempo; //** Gravo o tempo que durou a produção */
            $os5->id_users = $id_user; //** Gravo quem fez a ação */
            $os5->save(); //** Atualizo o registro de produção com os dados novos */

            $log = new Log();
            $log->registrarLog($acaolog, $os5->getEntity(), $os5->id, $antes, $os5->data());

            $os5pausa = new Os5(); //** Depois de atualizar o registro de produção, crio um novo registro na OS5 para informar a pausa da tarefa */
            $antesPausa = null; //** Preparo dados pra enviar pro log - como é registro novo então não existem valores antigos */
            $acaoPausa = "C"; //** Informo no log que é um insert */
            $os5pausa->id_emp2 = $os2->id_emp2; //** Gravo a empresa */
            $os5pausa->id_os1 = $os2->id_os1; //** Gravo a OS1 */
            $os5pausa->id_os2 = $os2->id; //** Gravo a OS2 */
            $os5pausa->dhi = date("Y-m-d H:i:s"); //** Gravo a data e hora de início do registro */
            $os5pausa->status = 'P'; //** Gravo o status como P (tempo desse registro é de pausa) */
            $os5pausa->id_users = $id_user; //** Gravo quem fez a ação */
            $os5pausa->save(); //** Crio o registro de pausa na OS5 com os dados novos */

            $log2 = new Log();
            $log2->registrarLog($acaoPausa, $os5pausa->getEntity(), $os5pausa->id, $antesPausa, $os5pausa->data());

            $os2->status = 'P';
        } elseif ($acao == 'end') { //** Se a ação for finalizar (end), então significa que existe um registro de produção aberto pra essa tarefa, então entro no laço pra editar esse registro */
            if ($os2->assinado == "N") { //* Verifica se a tarefa foi assinada */
                $json['denied'] = "Colha a assinatura antes de finalizar a tarefa.";
                echo json_encode($json);
                return;
            }

            $os5 = (new Os5())->find(
                "id_os1 = :id_os1 and id_os2 = :id_os2 and status = :status",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=T"
            )->order("id desc")->limit(1)->fetch(); //** Encontro na tabela OS5 o registro de produção pra informar o término da produção */
            $antes = clone $os5->data(); //** Preparo dados antigos pra enviar pro log sobre o update*/
            $acaolog = "U"; //** Informo no log que é um update */
            $os5->dhf = date("Y-m-d H:i:s"); //** Gravo o fim da produção */
            $tempo = strtotime($os5->dhf) - strtotime($os5->dhi);
            $os5->tempo = $tempo; //** Gravo o tempo que durou a produção */
            $os5->id_users = $id_user; //** Gravo quem fez a ação */
            $os5->save(); //** Atualizo o registro de produção com os dados novos */

            $log = new Log();
            $log->registrarLog($acaolog, $os5->getEntity(), $os5->id, $antes, $os5->data());

            $os2->status = 'C';
        } elseif ($acao == 'can') {
            $os2->status = 'D';
        }

        //* ATUALIZAÇÕES NA TABELA OS1 */
        $os2->beginTransaction(); //** Inicia a transação */

        if (!$os2->save()) {
            $os2->rollback(); //** Se não conseguir salvar, desfaz as alterações */
            $json['message'] = "ERRO AO SALVAR STATUS DA TAREFA";
        } else {
            if ($acao == 'stt') { //* Se uma tarefa está sendo iniciada*/
                $json['message'] = "TAREFA INICIADA"; //* Mensagem de sucesso pro navegador*/
                $json['acao'] = 'stt'; //** Ação de iniciar tarefa pro navegador */

                if ($os1->id_status == 2) { //* Verifico se a OS1 está com status 2 (aguardando execução) */
                    $os1->id_status = 3; //** Atualizo o status da OS1 para 3 (em execução) */
                }
            } else if ($acao == 'psr') {
                $json['message'] = "TAREFA PAUSADA";
                $json['acao'] = 'psr';
                $attOs1 = false; //* Se a tarefa for pausada, não atualizo a OS1 */                
            } else if ($acao == 'res') {
                $json['message'] = "TAREFA RETOMADA";
                $json['acao'] = 'res';
                $attOs1 = false; //* Se a tarefa for retomada, não atualizo a OS1 */                
            } else if ($acao == 'end') { //* Quando a ação for de finalização de tarefa */
                $tarefas = (new Os2())->findByIdOs($os2->id_os1);
                foreach ($tarefas as $tarefa) {
                    if ($tarefa->status != 'C' && $tarefa->status != 'D') {
                        $attOs1 = false;
                        break;
                    }
                } //* Verifico se todas as tarefas estão concluídas ou canceladas */

                if ($attOs1) {
                    $os1->concluir = 'S';
                }
                $json['message'] = "TAREFA FINALIZADA";
                $json['acao'] = 'end';
            } else if ($acao == 'can') { //** Quando a ação for de cancelamento de tarefa */
                $tarefas = (new Os2())->findByIdOs($os2->id_os1);

                foreach ($tarefas as $tarefa) {
                    //* Se encontrar algum status diferente de C ou D, podemos sair do loop */
                    if ($tarefa->status != 'C' && $tarefa->status != 'D') {
                        $attOs1 = false;
                        break;
                    }
                }
                if ($attOs1) {
                    $os1->concluir = 'S';
                }
                $json['message'] = "TAREFA CANCELADA";
                $json['acao'] = 'can';
            }

            if ($attOs1) { //* Se a flag de atualização da OS1 for true, então atualizo a OS1 */
                $os1->save(); //** Atualizo a OS1 com os dados novos */

                $logOs1 = new Log();
                $logOs1->registrarLog("U", $os1->getEntity(), $os1->id, $antesOs1, $os1->data());
            }

            $os2->commit();

            $logOs2 = new Log();
            $logOs2->registrarLog("U", $os2->getEntity(), $os2->id, $antesOs2, $os2->data());
        }

        echo json_encode($json);
    }

    public function gerarPdfOs($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);

        $logo = "";
        if (!empty($emp->logo)) {
            $logo = '<span>
                <img class="thumb" src="' . CONF_FILES_URL . $emp->logo . '">
            </span>';
        }

        $id = ll_decode($data['id']);

        $os2 = (new Os2())->findById($id);
        $os1 = (new Os1())->findById($os2->id_os1);
        $os3 = (new Os3())->find(
            "id_os1 = :id_os1",
            "id_os1={$os1->id}",
            "id_materiais, SUM(qtde) as total_qtde, SUM(vtotal) as total_vtotal"
        )->group("id_materiais")->fetch(true);
        $cliente = (new Ent())->findById($os1->id_cli);
        $os2completa = (new Os2())->find(
            "id_os1 = :id_os1",
            "id_os1={$os2->id_os1}"
        )->fetch(true);

        $tarefa = (new Servico())->find()->fetch(true);

        $anexos = (new Os6())->find(
            "id_os1 = :id_os1",
            "id_os1={$os1->id}"
        )->order("CASE WHEN tipo LIKE 'image%' THEN 1 ELSE 2 END, id")->fetch(true);

        $htmlTarefas = "";
        $htmlMateriais = "";
        $htmlMateriaisHeader = "";
        $htmlMateriaisFooter = "";
        $status = "";
        $color = "";
        $operador = "";
        $prev = "";
        $prevf = "";
        $tarefaNome = "";
        $assinatura = "";
        $somaTarefas = 0;
        $somaMateriais = 0;


        foreach ($os2completa as $os) {
            if (!empty($os->id_colaborador)) {
                $operador = (new Ent())->findById($os->id_colaborador);
            }

            if ($os->status == "A") {
                $status = "Aguardando Início";
                $color = "#007BFF";
                $prev = " Prev. ";
                $prevf = " Prev. ";
            } elseif ($os->status == "I") {
                $status = "Em Execução";
                $color = "#FFA500";
                $prevf = " Prev. ";
            } elseif ($os->status == "P") {
                $status = "Pausada";
                $color = "#FF0000";
                $prevf = " Prev. ";
            } elseif ($os->status == "C") {
                $status = "Concluída";
                $color = "#32CD32";
            } elseif ($os->status == "D") {
                $status = "Cancelada";
                $color = "#800080";
            }

            $servico = (new Servico())->findById($os->id_servico);

            $totalMedido = 0;
            $totalContratado = $os->qtde;
            $totalPendente = 0;

            $htmlMedicoes = "";

            foreach ($tarefa as $t) {
                if ($t->id == $os->id_servico) {
                    $tarefaNome = $t->nome;

                    if ($servico->medicao == "1") {
                        $medicoes = (new Os2_1())->findByOs2($os->id);
                        if (!empty($medicoes)) {
                            foreach ($medicoes as $item) {
                                $totalMedido += $item->qtde;
                            }

                            $totalPendente = $totalContratado - $totalMedido;

                            $htmlMedicoes = "<strong>Medido: </strong>" . $totalMedido . " " . $servico->medida . " - <strong>A medir: </strong>" . $totalPendente . " " . $servico->medida;
                        }
                    }
                }
            }

            $htmlObra = "";

            if (!empty($os1->id_obras)) {
                $obra = (new Obras())->findById($os1->id_obras);

                $obs = "";
                if (!empty($obra->obs)) {
                    $obs = "<strong>Obs: </strong>" . $obra->obs;
                }
                $htmlObra = '
                <section class="obra">
                    <h5>Obra</h5>
                    <div class="card fcad-form-row obra-pdf">
                        <div class="fcad-form-group">
                            <p><strong>Nome: </strong>' . $obra->nome . '</p>
                            <p><strong>Endereço: </strong>' . $obra->endereco . '</p>
                            <p>' . $obs . '</p>
                        </div>
                    </div>
                </section>
                ';
            }

            $assinatura = "";

            if ($os->assinado == "S") {
                $arquivo = strrchr($os->assinatura, '/');
                $imagem_original = __DIR__ . "/../../signatures" . $arquivo;
                $unique_id = uniqid();
                $imagem_girada = __DIR__ . "/../../signatures/temp_girada_" . $unique_id . ".png";

                // Verificar se a imagem original existe
                if (file_exists($imagem_original)) {
                    // Carregar a imagem original
                    $imagem = imagecreatefrompng($imagem_original);

                    if ($imagem !== false) {
                        // Criar uma nova imagem com as dimensões invertidas
                        $largura = imagesx($imagem);
                        $altura = imagesy($imagem);

                        // Verificar se a imagem já está em paisagem
                        if ($largura > $altura) {
                            // A imagem já está em paisagem, não precisa girar
                            $imagem_url = URL_LOCAL . "/signatures" . $arquivo;
                        } else {
                            $imagem_rotacionada = imagecreatetruecolor($altura, $largura);

                            // Preservar a transparência
                            imagealphablending($imagem_rotacionada, false);
                            imagesavealpha($imagem_rotacionada, true);

                            // Girar a imagem 90 graus para a esquerda
                            for ($x = 0; $x < $largura; $x++) {
                                for ($y = 0; $y < $altura; $y++) {
                                    imagesetpixel($imagem_rotacionada, $y, $largura - $x - 1, imagecolorat($imagem, $x, $y));
                                }
                            }

                            // Salvar a imagem girada
                            imagepng($imagem_rotacionada, $imagem_girada);

                            // Adicionar o caminho da imagem temporária ao array
                            $temp_images[] = $imagem_girada;

                            // Liberar a memória                        
                            imagedestroy($imagem_rotacionada);

                            // Usar a imagem girada no HTML
                            $imagem_url = URL_LOCAL . "/signatures/temp_girada_" . $unique_id . ".png";
                        }

                        //Liberar a memória
                        imagedestroy($imagem);

                        // Adicionar a imagem ao HTML
                        $assinatura = '
                        <div class="signature-box placeholder-image">
                            <img src="' . $imagem_url . '" alt="Assinatura">
                        </div>
                        ';
                    } else {
                        // Falha ao carregar a imagem
                        // Tratar o erro de carregamento
                        error_log("Erro ao carregar a imagem: " . $imagem_original);
                    }
                } else {
                    // Imagem original não encontrada
                    // Tratar o erro de arquivo não encontrado
                    error_log("Imagem original não encontrada: " . $imagem_original);
                }
            }

            $anexo = "";

            if (!empty($anexos)) {
                foreach ($anexos as $file) {

                    if ($file->id_os2 == $os->id) {
                        $imagem_url = URL_LOCAL . "/storage/uploads/" . $file->arquivo;

                        if (strpos($file->tipo, 'image') !== false) {
                            $anexo .= '
                            <div class="gallery-item">
                                <img src="' . $imagem_url . '" alt="Assinatura">
                            </div>
                            ';
                        } else {
                            $nomeCompleto = $file->nome_arquivo;
                            // Separamos o nome da extensão
                            $nomeSemExtensao = pathinfo($nomeCompleto, PATHINFO_FILENAME);
                            $extensao = pathinfo($nomeCompleto, PATHINFO_EXTENSION);

                            // Limitamos o nome a 8 caracteres, se necessário
                            if (strlen($nomeSemExtensao) > 20) {
                                $nomeFormatado = substr($nomeSemExtensao, 0, 20) . '...' . $extensao;
                            } else {
                                $nomeFormatado = $nomeCompleto;
                            }

                            $anexo .= '
                            <div>
                                <p>' . $nomeFormatado . '</a>
                            </div>
                            ';
                        }
                    }
                }
            }

            $dia = date_fmt($os->dataexec, "d/m/Y");
            $hora = secondsToTime($os->horaexec);
            $fim = secondsToTime($os->horafim);

            $valorTarefa = "";
            $colspan = "colspan='2'";
            $somaTarefas += $os->vtotal;

            if ($emp->mostraValorPdf == "X") {
                $valorTarefa = '<tr><td>' . $htmlMedicoes . '</td><td></td><td><strong>Total: </strong>' . moedaBR($os->vtotal) . '</td></tr>';
                $colspan = "";
            }

            $aditivo = "";
            $colspanAditivo = "";
            if ($os->aditivo == "S") {
                $aditivo = '- <span style="font-weight: bold; color: red;">(Aditivo)</span>';
                $colspanAditivo = "colspan='2'";
            }

            $linhaOperador = "";
            if (!empty($os->id_colaborador)) {
                $linhaOperador = '<td colspan="2"><strong>Responsável: </strong>' . $operador->nome . '</td>';
            }

            $htmlTarefas .= '
                <table>
                <tbody>
                    <tr>
                    <td ' . $colspanAditivo . '><strong>Tarefa #' . $os->id . ' ' . $aditivo . '</strong></td>                    
                    ' . $linhaOperador . '
                    </tr>
                    <tr>
                    <td><strong>Status: </strong><span style="color: ' . $color . ';">' . $status . '</span></td>
                    <td><strong>Início' . $prev . ': </strong>' . $dia . ' ' . $hora . '</td>
                    <td><strong>Término' . $prevf . ': </strong>' .  $dia . ' ' . $fim . '</td>
                    </tr>
                    <tr>
                    <td colspan="2"><strong>Tarefa: </strong>' . mb_strimwidth($tarefaNome, 0, 40, '...') . '</td>
                    <td><strong>Qtde.: </strong>' . $os->qtde . '</td>                    
                    </tr>
                    ' . $valorTarefa . '
                    <tr>
                    <td colspan="3" class="allow-break"><strong>Obs.: </strong>' . $os->obs . '</td>
                    </tr>
                    <tr>
                    <td colspan="3"><strong>Anexos:</strong></td>
                    </tr>
                </tbody>
                </table>
            ' . $anexo . '
                <div class="sign-content">
                    <p><strong>Assinatura:</strong></p>                    
            '
                . $assinatura .
                '
                </div>
            ';
        }

        if (!empty($os1->controle)) {
            $controle = '<br>Controle: ' . $os1->controle;
        } else {
            $controle = '';
        }

        $orçamento = "";
        if ($os1->id_status == 8) {
            $orçamento = '<br>*ORÇAMENTO*';
        }

        $linhaTotalTarefas = "";
        if ($emp->mostraValorPdf == "X") {
            $linhaTotalTarefas = '<table><td><strong>Total Serviços: </strong> R$ ' . moedaBR($somaTarefas) . '</td></table>';
        }

        if (!empty($os3)) {
            $totalMatHeader = "";
            $linhaTotalFooter = "";
            $totalMaterial = "";

            foreach ($os3 as $mat) {
                $materiais = (new Materiais())->findById($mat->id_materiais);
                $somaMateriais += $mat->total_vtotal;
                if ($emp->mostraValorPdf == "X") {
                    $totalMaterial = '<td class="align-right">' . moedaBR($mat->total_vtotal) . '</td>';
                }
                $htmlMateriais .= '
                    <tr>
                        <td>' . $materiais->descricao . '</td>
                        <td class="align-right">' . $mat->total_qtde . '</td>
                        ' . $totalMaterial . '
                    </tr>                    
                ';
            }

            if ($emp->mostraValorPdf == "X") {
                $totalMatHeader = '<th class="align-right">Total</th>';
                $linhaTotalFooter = '<td><strong>Total Produtos/Materiais: </strong> R$ ' . moedaBR($somaMateriais) . '</td>';
            }

            $htmlMateriaisHeader = '
                <section class="forms">
                    <h5>Produtos/Materiais</h5>
                    <div class="card">
                        <table class="no-bordered">
                            <thead>
                                <tr>
                                    <th>Produto/Material</th>
                                    <th class="align-right">Qtde</th>
                                    ' . $totalMatHeader . '
                                </tr>
                            </thead>
                            <tbody class="item3">
                ';
            $htmlMateriaisFooter = '
                            </tbody>
                            <tfoot>
                            ' . $linhaTotalFooter . '
                            </tfoot>
                        </table>
                    </div>
                </section>
            ';
        }

        $rodapeTotais = "";
        if ($emp->mostraValorPdf == "X") {
            $rodapeTotais = '
                <div class="totais">
                <div class="fcad-form-group totalp">
                    <table>                        
                        <tr>
                            <td colspan="3"><strong>Serviços: </strong></td>
                            <td class="align-right">R$</td>
                            <td class="align-right" width="80">' . moedaBR($somaTarefas) . '</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Produtos/Materiais: </strong></td>
                            <td class="align-right">R$</td>
                            <td class="align-right" width="80">' . moedaBR($somaMateriais) . '</td>
                        </tr>
                    </table>
                    <table style="margin-top:10px">
                        <tr>
                            <td colspan="3"><strong>TOTAL OS: </strong></td>
                            <td class="align-right">R$</td>
                            <td width="80" class="align-right">' . moedaBR($somaTarefas + $somaMateriais) . '</td>
                        </tr>
                    </table>
                </div>
                </div>';
        }

        $html = '
        <!DOCTYPE html>
        <html lang="pt-br">

        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Página de Serviço</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f8f9fa;
                color: #333;
            }

            section {
            margin-top: 0;
            margin-bottom: 0;
            }

            .container {
                width: 90%;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px 0;
            }

            p {
                font-size: 0.9em;
                margin: 0;
            }

            .cliente {
                font-size: 1.2em;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #ddd;            
            }

            .emp-name {
                padding-left: 20px;
                padding-bottom: 20px;
                font-size: 1.5rem;
                font-weight: bold;
            }

            .os-number {
                font-weight: bold;
                margin: 0;
                padding-bottom: 20px;
            }

            .os-title {
                margin: 0;
            }

            h5 {
                font-size: 1.2rem;
                margin-top: 10px;
                margin-bottom: 5px;
            }
            
            .fcad-form-row {
                position: relative;                
                height: 100px; /* Altura fixa para garantir que os elementos se alinhem corretamente */
            }
            .logo-container {
                position: absolute;
                left: 10px;
                top: 10px;
                width: 100px; /* Define a largura fixa para a logo */
                height: 100px; /* Define a altura fixa para a logo */
            }
            .fcad-form-group {
                /*position: absolute; */
                /*left: 120px;*/ /* Espaço suficiente para a logo e margem */
                margin-top: 10px;
                top: 0;
                right: 0;                
            }
            .logo {
                height: 100px;
                width: 100px;
            }
            .card {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 5px;
                overflow: hidden; /* Garante que os elementos flutuantes sejam contidos */
            }

            .totais {
                padding: 15px;
                margin-bottom: 5px;
                overflow: hidden; /* Garante que os elementos flutuantes sejam contidos */
            }

            .totalp {
                width: 50%;
                padding: 15px;
                margin-bottom: 5px;
                overflow: hidden; /* Garante que os elementos flutuantes sejam contidos */
            }

            .placeholder-image {
                display: inline-block;
                background-color: #e9ecef;
                border: 1px solid #ddd;
                border-radius: 4px;
                height: 100px;
                width: 100px;
                margin-right: 10px;
            }

            .image-grid {
                text-align: left;
            }

            .tabela-card {
                padding-top: 0;
            }

            .status-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .status-success {
                color: green;
                font-weight: bold;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }

            .sign-content {
                margin-top: 20px;
            }

            .signature-box {
                height: 60px;
                width: 200px;
                margin-bottom: 10px;
            }

            .signature-box img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            ul {
                list-style-type: disc;
                padding-left: 20px;
            }

            table {
                width: 100%;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                margin-bottom: 10px;            
            }

            .no-bordered {
                border-top: none;
            }            

            table td, table th {
                font-size: 0.8em;
                padding: 0;
                text-align: left;
                white-space: nowrap;
            }

            td.allow-break {
                white-space: normal; /* Permite quebra de linha */
            }

            .td1 {														
                padding: 1px;
            }

            table strong{
                font-size: 1.1em;
            }
            
            .item3 tr{
                font-size: 0.9em;            
            }

            .item3 td{
                border-bottom: 1px solid #ddd;
                padding: 5px;
                margin: 0;                
            }

            .gallery-item {
                display: inline-block;
                font-size: 0.8em;
                color: #0B8E36;
                box-sizing: border-box;
                border: 1px solid #0B8E36;
                border-radius: 5px;
                overflow: hidden;
                position: relative;
                margin-top: 15px;
                width: 100px;
                height: 100px;
            }

            .gallery-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                top: 50%;
                left: 50%;
            }

            .header-table {
                border: none;
                width: 100%;
                border-collapse: collapse;
            }

            .header-logo {
                width: 3.625rem;
                height: 3.625rem;
            }

            .os-number {
                text-align: right;
                font-size: 1.2rem;
                vertical-align: middle;
            }

            .thumb {
                width: 3.625rem;
                height: 3.625rem;
            }

            .header-table td {
                vertical-align: middle;
            }

            .client-data .fcad-form-group {
                margin-top: 0;
            }

            .align-right {
                text-align: right;
            }

            .obra-pdf {
                height: auto;
            }

        </style>
        </head>

        <body>
            <div class="container">
                <header class="header">
                    <table class="header-table">
                        <tr>
                            <td class="header-logo">
                                ' . $logo . '
                            </td>
                            <td class="emp-name">
                                ' . $emp->razao . $orçamento . '
                            </td>
                            <td class="os-number">
                                OS ' . $os1->id . $controle . '
                            </td>
                        </tr>
                    </table>               
                </header>

                <section class="client-data">
                    <h5>Dados do cliente</h5>                    
                        <div class="card fcad-form-row">
                            <div class="fcad-form-group">
                                <p><strong class="cliente">' . $cliente->nome . '</strong></p>
                                <p>
                                <strong>Endereço: </strong>' . $cliente->endereco . ', ' . $cliente->numero . ' - <strong>Complemento: </strong>' . $cliente->complemento . ' - <strong>Bairro: </strong>' . $cliente->bairro . '
                                </p>
                                <p>
                                <strong>Cidade: </strong>' . $cliente->cidade . '/' . $cliente->uf . ' - <strong>CEP: </strong>' . $cliente->cep . '
                                </p>
                                <p><strong>Telefone: </strong>' . $cliente->fone1 . ' - ' . $cliente->fone2 . '</p>
                                <p><strong>Email: </strong>' . $cliente->email . '</p>
                            </div>
                        </div>
                </section>' . $htmlObra . '

                <section class="service-info">
                <h5>Serviços</h5>
                <div class="card tabela-card">
                '
            . $htmlTarefas .

            $linhaTotalTarefas .
            '
                </div>
                </section>                
                ' . $htmlMateriaisHeader . $htmlMateriais . $htmlMateriaisFooter . '
                ' . $rodapeTotais . '
            </div>
            
        </body>
        </html>
        ';
        //echo $html;

        ll_pdfGerar($html, "ordemdeservico", "R", "P");
        if (!empty($temp_images)) {
            foreach ($temp_images as $temp_image) {
                if (file_exists($temp_image)) {
                    unlink($temp_image);
                }
            }
        }
    }

    public function sign($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (isset($data['signature'])) {
            $signatureData = $data['signature'];
            $idTarefa = $data['id'];
            $os = (new Os2())->findById($idTarefa);

            if ($os->status == "A") {
                $json['message'] = "Inicie a tarefa antes de assinar.";
                echo json_encode($json);
                return;
            }

            $idOs = $os->id_os1;
            $base64Data = str_replace('data:image/png;base64,', '', $signatureData);
            $base64Data = str_replace(' ', '+', $base64Data);
            $data = base64_decode($base64Data);

            // Define o caminho para salvar a assinatura
            $filePath = SITE_DIR . '/signatures/' . $id_empresa . '_' . $idOs . '_' . $idTarefa .  '_sign_' . time() . '.png';

            // Cria a pasta se não existir
            if (!file_exists(SITE_DIR . '/signatures')) {
                mkdir(SITE_DIR . '/signatures', 0777, true);
            }

            // Salva a assinatura no arquivo
            if (file_put_contents($filePath, $data)) {
                $os->assinado = "S";
                $os->assinatura = $filePath;
                $os->id_users = $id_user;
                $os->save();
                $json = true;
                echo json_encode($json);
            } else {
                http_response_code(500);
                $json['message'] = "Erro ao salvar a assinatura.";
                echo json_encode($json);
            }

            $os->logNote = "Assinatura";

            $log = new Log();
            $log->registrarLog("U", $os->getEntity(), $os->id, $os->data(), $os->data());
        } else {
            http_response_code(400);
            $json['message'] = "Nenhuma assinatura foi enviada.";
            echo json_encode($json);
        }
    }

    public function obs($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $idTarefa = $data['id'];
        $obs = $data['obs'];

        $os = (new Os2())->findById($idTarefa);
        $os->obs = $obs;
        $os->id_users = $id_user;

        if ($os->save()) {
            $json['message'] = "Observação salva com sucesso.";
            $json['obs'] = mb_strimwidth($obs, 0, 30, '...');
            echo json_encode($json);
        } else {
            http_response_code(500);
            $json['message'] = "Erro ao salvar a observação.";
            echo json_encode($json);
        }

        $os->logNote = "Observação";

        $log = new Log();
        $log->registrarLog("U", $os->getEntity(), $os->id, $os->data(), $os->data());
    }

    public function anexos($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $idTarefa = $data['id'];
        $os = (new Os2())->findById($idTarefa);

        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = $file['name'];
            $fileTmp = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];

            if (in_array($fileExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize <= 41943040) {
                        $fileNameNew = $id_empresa . '_' . $idTarefa . '_' . time() . '.' . $fileExt;
                        $fileDestination = CONF_FILES_PATH . $fileNameNew;
                        $fileLink = CONF_FILES_URL . $fileNameNew;

                        if (!file_exists(CONF_FILES_PATH)) {
                            mkdir(CONF_FILES_PATH, 0777, true);
                        }

                        if (move_uploaded_file($fileTmp, $fileDestination)) {
                            $os6 = new Os6();
                            $os6->id_emp2 = $id_empresa;
                            $os6->id_os1 = $os->id_os1;
                            $os6->id_os2 = $os->id;
                            $os6->nome_arquivo = $fileName;
                            $os6->arquivo = $fileNameNew;
                            $os6->caminho = $fileDestination;
                            $os6->tipo = $fileType;
                            $os6->id_users = $id_user;

                            if (!$os6->save()) {
                                http_response_code(500);
                                $json['message'] = "Erro ao salvar o anexo.";
                                echo json_encode($json);
                                return;
                            }

                            $log = new Log();
                            $log->registrarLog("C", $os6->getEntity(), $os6->id, null, $os6->data());

                            $nomeCompleto = $os6->nome_arquivo;
                            // Separamos o nome da extensão
                            $nomeSemExtensao = pathinfo($nomeCompleto, PATHINFO_FILENAME);
                            $extensao = pathinfo($nomeCompleto, PATHINFO_EXTENSION);

                            // Limitamos o nome a 8 caracteres, se necessário
                            if (strlen($nomeSemExtensao) > 8) {
                                $nomeFormatado = substr($nomeSemExtensao, 0, 8) . '...' . $extensao;
                            } else {
                                $nomeFormatado = $nomeCompleto; // Nome menor que 8 caracteres não é alterado
                            }

                            $json['status'] = "success";
                            $json['message'] = "Salvo com sucesso.";
                            $json['arquivo'] = $fileNameNew;
                            $json['id'] = $os6->id;
                            $json['nome'] = $nomeFormatado; // Adiciona o nome do arquivo à resposta JSON
                            $json['url'] = CONF_FILES_URL; // Adiciona o caminho do arquivo à resposta JSON
                            $json['delete'] = url("oper_ordens/deletearq");
                            $json['fileType'] = $fileType; // Adiciona o tipo do arquivo à resposta JSON
                            echo json_encode($json);
                        } else {
                            http_response_code(500);
                            $json['message'] = "Erro ao salvar o anexo.";
                            echo json_encode($json);
                        }
                    } else {
                        http_response_code(400);
                        $json['message'] = "O arquivo é muito grande. Tamanho máximo permitido: 40MB.";
                        echo json_encode($json);
                    }
                } else {
                    http_response_code(400);
                    $json['message'] = "Erro ao fazer upload do arquivo.";
                    echo json_encode($json);
                }
            } else {
                http_response_code(400);
                $json['message'] = "Tipo de arquivo não permitido.";
                echo json_encode($json);
            }
        } else {
            http_response_code(400);
            $json['message'] = "Nenhum arquivo foi enviado.";
            echo json_encode($json);
        }
    }

    public function deleteos6($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = $data['id'];
        $os6 = (new Os6())->findById($id);
        $antes = clone $os6->data();

        if ($os6->destroy) {
            $json['message'] = "Arquivo excluído com sucesso.";
            $json['status'] = "success";
            echo json_encode($json);
        } else {
            http_response_code(500);
            $json['message'] = "Erro ao excluir o arquivo.";
            echo json_encode($json);
        }

        $log = new Log();
        $log->registrarLog("D", $os6->getEntity(), $os6->id, $antes, null);
    }

    public function materiais($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (isset($data['verificar'])) {
            $id = $data['verificar'];
            $os3 = (new Os3())->find(
                "id_os2 = :id_os2",
                "id_os2={$id}"
            )->fetch(true);

            if (!empty($os3)) {
                $json['dados'] = false;
                echo json_encode($json);
                return;
            }

            $json['dados'] = true;
            $json['container'] = ".opermat-container";
            echo json_encode($json);
            return;
        }

        if (isset($data['verificar2'])) {
            $id = $data['verificar2'];
            $os6 = (new Os6())->find(
                "id_os2 = :id_os2",
                "id_os2={$id}"
            )->fetch(true);

            if (!empty($os6)) {
                $json['dados'] = false;
                echo json_encode($json);
                return;
            }

            $json['dados'] = true;
            $json['container'] = "#galleryContainer";
            echo json_encode($json);
            return;
        }

        $idTarefa = $data['tarefaId'];
        $idMaterial = $data['matId'];
        $qtde = $data['qtde'];

        if (isset($data['idos3'])) {
            $os3 = (new Os3())->findById($data['idos3']);
            $antes = clone $os3->data();
            $acao = "U";
        } else {
            $os3 = new Os3();
            $antes = null;
            $acao = "C";
        }

        $os2 = (new Os2())->findById($idTarefa);

        $material = (new Materiais())->findById($idMaterial);
        $valor = $material->valor;
        $total = $valor * $qtde;

        $os3->id_emp2 = $id_empresa;
        $os3->id_os1 = $os2->id_os1;
        $os3->id_os2 = $os2->id;
        $os3->id_materiais = $idMaterial;
        $os3->qtde = $qtde;
        $os3->vunit = $valor;
        $os3->vtotal = $total;
        $os3->id_users = $id_user;

        $os1 = (new Os1())->findById($os3->id_os1);
        $os1Antes = clone $os1->data();
        $os1->vtotal = $os1->vtotal + $total;
        $os1->id_users = $id_user;
        $os1->save();
        $os1->logNote = "Adição de material - Alteração Valor Total";

        $logOs1 = new Log();
        $logOs1->registrarLog("U", $os1->getEntity(), $os1->id, $os1Antes, $os1->data());

        if ($os3->save) {
            if (isset($data['idos3'])) {
                $json['message'] = "Produto/Material atualizado com sucesso.";
            } else {
                $json['message'] = "Produto/Material adicionado com sucesso.";
            }
            $json['status'] = "success";
            $json['id'] = $os3->id;
            $json['delete'] = url("oper_ordens/deletemat");
            echo json_encode($json);
        } else {
            http_response_code(500);
            $json['message'] = "Erro ao adicionar o produto/material.";
            echo json_encode($json);
        }

        $logOs3 = new Log();
        $logOs3->registrarLog($acao, $os3->getEntity(), $os3->id, $antes, $os3->data());
    }

    public function deleteos3($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = $data['id'];
        $os3 = (new Os3())->findById($id);
        $totalMaterial = $os3->vtotal;
        $os1 = (new Os1())->findById($os3->id_os1);

        if ($os3->destroy) {
            $os1->vtotal = $os1->vtotal - $totalMaterial;
            $os1->save();

            $json['message'] = "Item excluído!";
            $json['status'] = "success";
            echo json_encode($json);
        } else {
            http_response_code(500);
            $json['message'] = "Erro ao excluir o arquivo.";
            echo json_encode($json);
        }

        $log = new Log();
        $log->registrarLog("D", $os3->getEntity(), $os3->id, $os3->data(), null);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
};
