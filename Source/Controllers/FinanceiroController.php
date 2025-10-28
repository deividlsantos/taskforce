<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Baixas;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Oper;
use Source\Models\Pag;
use Source\Models\PagSaldo;
use Source\Models\Plconta;
use Source\Models\Rec;
use Source\Models\RecSaldo;

class FinanceiroController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->financeiro != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $fornecedor = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=2&status=A"
        )->fetch(true);

        $portador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=4&status=A"
        )->fetch(true);

        $plconta = (new Plconta())->find(
            "ativo = :ativo",
            "ativo=1"
        )->fetch(true);

        $baixas = (new Baixas())->find()->fetch(true);
        $operacao = (new Oper())->find()->fetch(true);
        $despesas = (new Pag())->find()->fetch(true);
        $receitas = (new Rec())->find()->fetch(true);

        $lancamentos = [];

        if ($receitas) {
            foreach ($receitas as $rec) {
                $rec->tipo = 'receita';
                $lancamentos[] = $rec->data();
            }
        }

        if ($despesas) {
            foreach ($despesas as $pag) {
                $pag->tipo = 'despesa';
                $lancamentos[] = $pag->data();
            }
        }

        usort($lancamentos, function ($a, $b) {
            return strcmp($b->created_at, $a->created_at);
        });

        $front = [
            "titulo" => "Financeiro - Taskforce",
            "user" => $this->user,
            "secTit" => "Receitas/Despesas"
        ];

        echo $this->view->render("tcsistemas.financeiro/pagar/contas", [
            "front" => $front,
            "dataAtual" => date('m-d-Y'),
            "cliente" => $cliente,
            "fornecedor" => $fornecedor,
            "portador" => $portador,
            "plconta" => $plconta,
            "operacao" => $operacao,
            "lancamentos" => $lancamentos,
            "baixas" => $baixas
        ]);
    }

    public function edtpag($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = ll_decode($data['id']);

        $pag = (new Pag())->findById($id);
        $pag->dataven = date_fmt($pag->dataven, 'Y-m-d');
        $pag->valor = moedaBR($pag->valor);
        $pag->vtotal = moedaBR($pag->vtotal);
        $pag->vdesc = moedaBR($pag->vdesc);
        $pag->voutros = moedaBR($pag->voutros);
        $pag->vpago = moedaBR($pag->vpago);
        $pag->saldo = moedaBR($pag->saldo);

        $pagsaldo = (new PagSaldo())->find(
            "id_pag = :id_pag",
            "id_pag={$id}",
            "*",
            false
        )->order("datapag asc")->fetch(true);

        $json['id'] = $pag->id;
        $json['tipo'] = $pag->tipo;
        $json['titulo'] = $pag->titulo;
        $json['dataven'] = $pag->dataven;
        $json['documento'] = $pag->documento;
        $json['competencia'] = $pag->competencia;
        $json['vtotal'] = $pag->vtotal;
        $json['id_entf'] = $pag->id_entf;
        $json['id_oper'] = $pag->id_oper;
        $json['id_plconta'] = $pag->id_plconta;
        $json['obs1'] = $pag->obs1;
        $json['obs2'] = $pag->obs2;
        $json['autorizante'] = $pag->autorizante;
        $json['valor'] = $pag->valor;
        $json['vdesc'] = $pag->vdesc;
        $json['voutros'] = $pag->voutros;
        $json['vparcial'] = $pag->vpago;
        $json['saldo'] = $pag->saldo;

        if (!empty($pagsaldo)) {
            $json['tabelasaldo'] = array_map(function ($pagSaldo) {
                return [
                    'id' => $pagSaldo->data()->id,
                    'id_emp2' => $pagSaldo->data()->id_emp2,
                    'id_pag' => $pagSaldo->data()->id_pag,
                    'id_ent' => $pagSaldo->data()->id_ent,
                    'datapag' => date_fmt($pagSaldo->data()->datapag, "d/m/Y"),
                    'valor' => moedaBR($pagSaldo->data()->valor),
                    'vdesc' => moedaBR($pagSaldo->data()->vdesc),
                    'voutros' => moedaBR($pagSaldo->data()->voutros),
                    'vpago' => moedaBR($pagSaldo->data()->vpago),
                    'saldo' => moedaBR($pagSaldo->data()->saldo),
                    'obs1' => $pagSaldo->data()->obs1,
                    'created_at' => $pagSaldo->data()->created_at,
                    'updated_at' => $pagSaldo->data()->updated_at
                ];
            }, $pagsaldo);
        }



        echo json_encode($json);
    }

    public function salvarpag($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $pag = new Pag;

        $pag->id_emp2 = $id_empresa;
        $pag->id_entf = $data['fornecedor'];
        $pag->id_plconta = $data['plconta'];
        $pag->documento = $data['documento'];
        $pag->id_oper = $data['operacao'];
        $pag->datacad = date('Y-m-d');
        $pag->obs1 = $data['obs1'];
        $pag->obs2 = $data['obs2'];
        $pag->autorizante = $data['autorizante'];
        $pag->competencia = date_fmt($data['competencia'], "m/Y");
        $pag->baixado = "N";
        $pag->vtotal = moedaSql($data['vtotal']);
        $pag->id_users = $id_user;

        $parcelas = (int)$data['parcelas'];

        if ($parcelas == 1) {
            $pag->titulo = $data['parctitulo_1'];
            $pag->valor = moedaSql($data['parc_1']);
            $pag->saldo = moedaSql($data['parc_1']);
            $pag->dataven = $data['parcven_1'];

            if (!$pag->save) {
                $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                echo json_encode($json);
                return;
            }

            $id_paglote = $pag->id;

            $pag->id_paglote = $id_paglote;
            $pag->save();
        } else {
            $pag->titulo = $data['parctitulo_1'];
            $pag->valor = moedaSql($data['parc_1']);
            $pag->saldo = moedaSql($data['parc_1']);
            $pag->dataven = $data['parcven_1'];

            if (!$pag->save) {
                $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                echo json_encode($json);
                return;
            }

            $id_paglote = $pag->id;
            $pag->id_paglote = $id_paglote;
            $pag->save();

            for ($i = 2; $i <= $parcelas; $i++) {
                $pagParcela = new Pag;
                $pagParcela->id_emp2 = $id_empresa;
                $pagParcela->id_entf = $data['fornecedor'];
                $pagParcela->id_plconta = $data['plconta'];
                $pagParcela->documento = $data['documento'];
                $pagParcela->id_oper = $data['operacao'];
                $pagParcela->datacad = date('Y-m-d');
                $pagParcela->obs1 = $data['obs1'];
                $pagParcela->obs2 = $data['obs2'];
                $pagParcela->autorizante = $data['autorizante'];
                $pagParcela->competencia = date_fmt($data['competencia'], "m/Y");
                $pagParcela->baixado = "N";
                $pagParcela->vtotal = moedaSql($data['vtotal']);
                $pagParcela->id_users = $id_user;

                $pagParcela->titulo = $data['parctitulo_' . $i];
                $pagParcela->valor = moedaSql($data['parc_' . $i]);
                $pagParcela->saldo = moedaSql($data['parc_' . $i]);
                $pagParcela->dataven = $data['parcven_' . $i];
                $pagParcela->id_paglote = $id_paglote;

                if (!$pagParcela->save) {
                    $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                    echo json_encode($json);
                    return;
                }

                $log = new Log();
                $log->registrarLog("C", $pagParcela->getEntity(), $pagParcela->id, null, $pagParcela->data());
            }
        }

        $log = new Log();
        $log->registrarLog("C", $pag->getEntity(), $pag->id, null, $pag->data());

        $json['message'] = $this->message->success("Despesa lançada!")->flash();
        $json['reload'] = true;
        echo json_encode($json);
    }

    public function edtrec($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = ll_decode($data['id']);

        $rec = (new Rec())->findById($id);
        $rec->dataven = date_fmt($rec->dataven, 'Y-m-d');
        $rec->valor = moedaBR($rec->valor);
        $rec->vtotal = moedaBR($rec->vtotal);
        $rec->vdesc = moedaBR($rec->vdesc);
        $rec->voutros = moedaBR($rec->voutros);
        $rec->vpago = moedaBR($rec->vpago);
        $rec->saldo = moedaBR($rec->saldo);

        $recsaldo = (new RecSaldo())->find(
            "id_rec = :id_rec",
            "id_rec={$id}",
            "*",
            false
        )->order("datapag asc")->fetch(true);

        $json['id'] = $rec->id;
        $json['tipo'] = $rec->tipo;
        $json['titulo'] = $rec->titulo;
        $json['dataven'] = $rec->dataven;
        $json['documento'] = $rec->documento;
        $json['competencia'] = $rec->competencia;
        $json['vtotal'] = $rec->vtotal;
        $json['id_entc'] = $rec->id_entc;
        $json['id_oper'] = $rec->id_oper;
        $json['id_plconta'] = $rec->id_plconta;
        $json['obs1'] = $rec->obs1;
        $json['obs2'] = $rec->obs2;
        $json['valor'] = $rec->valor;
        $json['vdesc'] = $rec->vdesc;
        $json['voutros'] = $rec->voutros;
        $json['vparcial'] = $rec->vpago;
        $json['saldo'] = $rec->saldo;

        if (!empty($recsaldo)) {
            $json['tabelasaldo'] = array_map(function ($recSaldo) {
                return [
                    'id' => $recSaldo->data()->id,
                    'id_emp2' => $recSaldo->data()->id_emp2,
                    'id_rec' => $recSaldo->data()->id_rec,
                    'id_ent' => $recSaldo->data()->id_ent,
                    'datapag' => date_fmt($recSaldo->data()->datapag, "d/m/Y"),
                    'valor' => moedaBR($recSaldo->data()->valor),
                    'vdesc' => moedaBR($recSaldo->data()->vdesc),
                    'voutros' => moedaBR($recSaldo->data()->voutros),
                    'vpago' => moedaBR($recSaldo->data()->vpago),
                    'saldo' => moedaBR($recSaldo->data()->saldo),
                    'obs1' => $recSaldo->data()->obs1,
                    'id_users' => $recSaldo->data()->id_users,
                    'created_at' => $recSaldo->data()->created_at,
                    'updated_at' => $recSaldo->data()->updated_at
                ];
            }, $recsaldo);
        }

        echo json_encode($json);
    }

    public function salvarrec($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $rec = new Rec;

        $rec->id_emp2 = $id_empresa;
        $rec->id_entc = $data['cliente'];
        $rec->id_plconta = $data['plconta'];
        $rec->documento = $data['documento'];
        $rec->id_oper = $data['operacao'];
        $rec->datacad = date('Y-m-d');
        $rec->obs1 = $data['obs1'];
        $rec->obs2 = $data['obs2'];
        $rec->vtotal = moedaSql($data['vtotal']);
        $rec->competencia = date_fmt($data['competencia'], "m/Y");
        $rec->baixado = "N";
        $rec->id_os1 = isset($data['id_os1']) ? $data['id_os1'] : null;
        $rec->id_users = $id_user;

        $parcelas = (int)$data['parcelas'];

        if ($parcelas == 1) {
            $rec->titulo = $data['parctitulo_1'];
            $rec->valor = moedaSql($data['parc_1']);
            $rec->saldo = moedaSql($data['parc_1']);
            $rec->dataven = $data['parcven_1'];

            if (!$rec->save) {
                $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                echo json_encode($json);
                return;
            }

            $id_reclote = $rec->id;

            $rec->id_reclote = $id_reclote;
            $rec->save();
        } else {
            $rec->titulo = $data['parctitulo_1'];
            $rec->valor = moedaSql($data['parc_1']);
            $rec->saldo = moedaSql($data['parc_1']);
            $rec->dataven = $data['parcven_1'];

            if (!$rec->save) {
                $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                echo json_encode($json);
                return;
            }

            $id_reclote = $rec->id;
            $rec->id_reclote = $id_reclote;
            $rec->save();

            for ($i = 2; $i <= $parcelas; $i++) {
                $recParcela = new Rec;
                $recParcela->id_emp2 = $id_empresa;
                $recParcela->id_entc = $data['cliente'];
                $recParcela->id_plconta = $data['plconta'];
                $recParcela->documento = $data['documento'];
                $recParcela->id_oper = $data['operacao'];
                $recParcela->datacad = date('Y-m-d');
                $recParcela->obs1 = $data['obs1'];
                $recParcela->obs2 = $data['obs2'];
                $recParcela->vtotal = moedaSql($data['vtotal']);
                $recParcela->competencia = date_fmt($data['competencia'], "m/Y");
                $recParcela->baixado = "N";
                $recParcela->id_users = $id_user;

                $recParcela->titulo = $data['parctitulo_' . $i];
                $recParcela->valor = moedaSql($data['parc_' . $i]);
                $recParcela->saldo = moedaSql($data['parc_' . $i]);
                $recParcela->dataven = $data['parcven_' . $i];
                $recParcela->id_reclote = $id_reclote;

                if (!$recParcela->save) {
                    $json['message'] = $this->message->warning("Erro no lançamento!")->render();
                    echo json_encode($json);
                    return;
                }

                $log = new Log();
                $log->registrarLog("C", $recParcela->getEntity(), $recParcela->id, null, $recParcela->data());
            }
        }

        $log = new Log();
        $log->registrarLog("C", $rec->getEntity(), $rec->id, null, $rec->data());

        $json['message'] = $this->message->success("Receita lançada!")->flash();

        if (empty($data['no_reload'])) {
            $json['reload'] = true;
        } else {
            $json['reload'] = false;
        }

        echo json_encode($json);
    }

    public function salvaredit($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $voutros = 0;
        $vdesc = 0;
        $vparcial = 0;
        $tabelaSaldo = null;
        if ($data['tipo-edit'] == 'despesa') {
            $edit = (new Pag())->findById($data['id-edit']);
            $edit->id_entf = $data['fornecedor-edit'];
            $edit->autorizante = $data['autorizante-edit'];
            $tabelaSaldo = (new PagSaldo())->find(
                "id_pag = :id_pag",
                "id_pag={$edit->id}",
                "sum(voutros) as outros, sum(vdesc) as desconto, sum(vpago) as parcial",
            )->fetch();
        } elseif ($data['tipo-edit'] == 'receita') {
            $edit = (new Rec())->findById($data['id-edit']);
            $edit->id_entc = $data['cliente-edit'];
            $tabelaSaldo = (new RecSaldo())->find(
                "id_rec = :id_rec",
                "id_rec={$edit->id}",
                "sum(voutros) as outros, sum(vdesc) as desconto, sum(vpago) as parcial",
            )->fetch();
        }

        if (!empty($tabelaSaldo)) {
            $voutros = $tabelaSaldo->outros ?? 0;
            $vdesc = $tabelaSaldo->desconto ?? 0;
            $vparcial = $tabelaSaldo->parcial ?? 0;
        }

        $valorEdit = (float)moedaSql($data['valor-edit']);

        $saldo = $valorEdit + $voutros - ($vdesc + $vparcial);

        $antes = clone $edit->data();

        $edit->competencia = $data['competencia-edit'];
        $edit->dataven = $data['dataven-edit'];
        $edit->id_plconta = $data['plconta-edit'];
        //$edit->vtotal = moedaSql($data['vtotal-edit']);
        $edit->id_oper = $data['operacao-edit'];
        $edit->obs1 = $data['obs1-edit'];
        $edit->obs2 = $data['obs2-edit'];
        $edit->valor = moedaSql($data['valor-edit']);
        $edit->saldo = $saldo;
        $edit->id_users = $id_user;

        if (!$edit->save) {
            $json['message'] = $this->message->warning("Erro no registro! Verifique os dados.")->render();
            echo json_encode($json);
            return;
        } else {

            $depois = $edit->data();

            $log = new Log();
            $log->registrarLog("U", $edit->getEntity(), $edit->id, $antes, $depois);

            $json['message'] = $this->message->success("Registro atualizado!")->flash();
            $json['reload'] = true;
            echo json_encode($json);
        }
    }

    public function estorno($data)
    {

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $estornar = null;
        $cliente = null;
        $fornecedor = null;
        $portador = null;

        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $fornecedor = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=2&status=A"
        )->fetch(true);

        $portador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=4&status=A"
        )->fetch(true);


        if (!empty($data)) {

            $tipo = $data['tabelaDados']['0']['tipo'];
            $ids = array_column($data['tabelaDados'], 'id');

            $idsSql = implode(', ', array_map('intval', $ids));

            if ($tipo == 'despesa') {
                $estornar = (new Pag())->find("id IN ({$idsSql})", null, "*", false)->fetch(true);
                foreach ($estornar as $e) {
                    $e->tipo = "despesa";
                }
            } elseif ($tipo == 'receita') {
                $estornar = (new Rec())->find("id IN ({$idsSql})", null, "*", false)->fetch(true);
                foreach ($estornar as $e) {
                    $e->tipo = "receita";
                }
            }
        }

        $dataEstornar = objectsToArray($estornar);
        $dataCliente = objectsToArray($cliente);
        $dataFornecedor = objectsToArray($fornecedor);
        $dataPortador = objectsToArray($portador);

        echo  json_encode([
            "estornar" => $dataEstornar,
            "cliente" => $dataCliente,
            "fornecedor" => $dataFornecedor,
            "portador" => $dataPortador
        ]);
    }

    public function estornar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        foreach ($data as $key => $value) {
            if (strpos($key, 'idEs_') !== false) {

                $id = str_replace('idEs_', '', $key);
                $tipo = isset($data["tipoEs_$id"]) ? $data["tipoEs_$id"] : "";


                if ($tipo == 'receita') {
                    $titulo = (new Rec())->findById($id);
                    $titSaldo = (new RecSaldo())->findByIdRec($id);
                } elseif ($tipo == 'despesa') {
                    $titulo = (new Pag())->findById($id);
                    $titSaldo = (new PagSaldo())->findByIdPag($id);
                }

                foreach ($titSaldo as $t) {
                    $antes = clone $t->data();
                    $t->destroy();
                    $log = new Log();
                    $log->registrarLog("D", $t->getEntity(), $t->id, $antes, null);
                }

                $titulo->saldo = $titulo->valor;
                $titulo->baixado = "N";
                $titulo->id_users = $id_user;
                $antes = clone $titulo->data();

                if (!$titulo->save) {
                    $json['message'] = $this->message->error("ERRO!")->render();
                    echo json_encode($json);
                    return;
                }

                $depois = $titulo->data();

                $log = new Log();
                $log->registrarLog("U", $titulo->getEntity(), $titulo->id, $antes, $depois);
            }
        }

        $this->message->success("Estorno(s) efetuado(s) com sucesso!")->flash();
        $json['redirect'] = url("contas");
        echo json_encode($json);
    }

    public function estornarParcial($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (!empty($data)) {

            $tipo = $data['dados']['0']['tipo'];
            $ids = array_column($data['dados'], 'id');

            if ($tipo == 'despesa') {
                $tabelaSaldo1 = new PagSaldo();
                $tabelaSaldo2 = new PagSaldo();
                $colunaId = 'id_pag';
                $tabelaPai = new Pag();
            } elseif ($tipo == 'receita') {
                $tabelaSaldo1 = new RecSaldo();
                $tabelaSaldo2 = new RecSaldo();
                $colunaId = 'id_rec';
                $tabelaPai = new Rec();
            }

            $saldo = 0;
            $desconto = 0;
            $outros = 0;
            $valorPago = 0;

            $tabelaSaldo1->beginTransaction();

            foreach ($ids as $id) {
                $baixa = $tabelaSaldo1->findById($id);
                $id_titulo = $baixa->id_pag ?? $baixa->id_rec;
                $antes = clone $baixa->data();
                if (!$baixa->destroy()) {
                    $tabelaSaldo1->rollback();
                    $json['status'] = "error";
                    $json['message'] = $this->message->warning("Não foi possível estornar. Contate o suporte")->render();
                    echo json_encode($json);
                    return;
                }
                $log = new Log();
                $log->registrarLog("D", $baixa->getEntity(), $baixa->id, $antes, null);
            }

            $totais = $tabelaSaldo2->find(
                "{$colunaId} = :id",
                "id={$id_titulo}&id_emp2={$id_empresa}",
                "sum(vdesc) as vdesc, sum(voutros) as voutros, sum(vpago) as vpago"
            )->fetch();

            $titulo = $tabelaPai->findById($id_titulo);
            $antes = clone $titulo->data();

            if ($totais) {
                $desconto = $totais->vdesc;
                $outros = $totais->voutros;
                $valorPago = $totais->vpago;
            }

            $titulo->vdesc = $desconto;
            $titulo->voutros = $outros;
            $titulo->vpago = $valorPago;
            $titulo->saldo = $titulo->valor + $outros - ($desconto + $valorPago);
            $titulo->baixado = "N";
            $titulo->id_users = $id_user;

            if (!$titulo->save()) {
                $tabelaSaldo1->rollback();
                $json['status'] = "error";
                $json['message'] = $this->message->warning("Não foi possível estornar. Contate o suporte")->render();
                echo json_encode($json);
                return;
            }
            $depois = $titulo->data();
            $log = new Log();
            $log->registrarLog("U", $titulo->getEntity(), $titulo->id, $antes, $depois);

            // Recalcular saldos dos registros restantes
            $registrosRestantes = $tabelaSaldo2->find(
                "{$colunaId} = :id",
                "id={$id_titulo}",
                "*"
            )->order("datapag ASC")->fetch(true);

            if ($registrosRestantes) {
                $saldoAtual = $titulo->valor + $titulo->voutros; // Saldo inicial

                foreach ($registrosRestantes as $registro) {
                    $saldoAtual = $saldoAtual - ($registro->vdesc + $registro->vpago);

                    $registroParaAtualizar = $tabelaSaldo2->findById($registro->id);
                    $antesRegistro = clone $registroParaAtualizar->data();

                    $registroParaAtualizar->saldo = $saldoAtual;
                    $registroParaAtualizar->id_users = $id_user;

                    if (!$registroParaAtualizar->save()) {
                        $tabelaSaldo1->rollback();
                        $json['status'] = "error";
                        $json['message'] = $this->message->warning("Não foi possível atualizar saldos. Contate o suporte")->render();
                        echo json_encode($json);
                        return;
                    }

                    $depoisRegistro = $registroParaAtualizar->data();
                    $log = new Log();
                    $log->registrarLog("U", $registroParaAtualizar->getEntity(), $registroParaAtualizar->id, $antesRegistro, $depoisRegistro);
                }
            }

            $this->message->success("Estorno(s) efetuado(s) com sucesso!")->flash();
            $json['status'] = "success";
            $tabelaSaldo1->commit();
        }

        echo json_encode($json);
    }

    public function excluirtudo($data): void
    {

        foreach ($data['tabelaDados'] as $d) {
            $id = $d['id'];
            $tipo = $d['tipo'];

            if (ll_intValida($id)) {
                if ($tipo == 'receita') {
                    $del = (new Rec())->findById($id);
                } elseif ($tipo == 'despesa') {
                    $del = (new Pag())->findById($id);
                }
                $antes = clone $del->data();

                if (!$del->destroy()) {
                    $json['message'] = $this->message->warning("Não foi possível excluir. Contate o suporte")->render();
                    echo json_encode($json);
                    return;
                }

                $log = new Log();
                $log->registrarLog("D", $del->getEntity(), $del->id, $antes, null);
            }
        }

        $this->message->success("Lançamento(s) removido(s) com sucesso!")->flash();
        redirect("contas");
    }

    public function excluir($data): void
    {
        $id = $data['id'];
        $tipo = ll_decode($data['tipo']);

        if (ll_intValida($id)) {
            if ($tipo == 'receita') {
                $del = (new Rec())->findById($id);
            } elseif ($tipo == 'despesa') {
                $del = (new Pag())->findById($id);
            }

            $antes = clone $del->data();

            if (!$del->destroy()) {
                $json['message'] = $this->message->warning("Não foi possível excluir. Contate o suporte")->render();
                echo json_encode($json);
                return;
            }

            $log = new Log();
            $log->registrarLog("D", $del->getEntity(), $del->id, $antes, null);

            $json = [
                'mensagem' => $this->message->success("Lançamento removido com sucesso!")->render(),
                'apagado' => true,
                'id' => $data['id'],
                'grid' => $data['grid']
            ];
            echo json_encode($json);
        }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
