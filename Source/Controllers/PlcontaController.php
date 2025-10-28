<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\Plconta;

class PlcontaController extends Controller
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

        $front = [
            "titulo" => "Plano de Contas - Taskforce",
            "user" => $this->user,
            "secTit" => "Plano de Contas"
        ];

        $conta = (new Plconta())->find(
            "ativo = :ativo",
            "ativo=1"
        )->order('descricao')->fetch(true);

        echo $this->view->render("tcsistemas.financeiro/plconta/plcontaList", [
            "front" => $front,
            "plconta" => $conta
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $conta = "";
        $secTit = "Cadastrar";
        if (isset($data['id_plconta'])) {
            $id = ll_decode($data['id_plconta']);
            $conta = (new Plconta())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Conta"
        ];

        echo $this->view->render("tcsistemas.financeiro/plconta/plcontaCad", [
            "front" => $front,
            "plconta" => $conta
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_plconta = ll_decode($data['id_plconta']);

        if (ll_intValida($id_plconta)) {
            $plconta = (new Plconta())->findById($id_plconta);
            $antes = clone $plconta->data();
            $acao = "U";
        } else {
            $plconta = new Plconta();
            $antes = null;
            $acao = "C";
        }

        $plconta->id_emp2 = $id_empresa;
        $plconta->codigoconta = $data['codigoconta'];
        $plconta->descricao = $data['descricao'];
        $plconta->id_tc = !empty($data['id_tc']) && ll_intValida($data['id_tc']) ? $data['id_tc'] : null;
        $plconta->codigocc = !empty($data['codigocc']) && ll_intValida($data['codigocc']) ? $data['codigocc'] : null;
        $plconta->tipo = $data['tipo'];
        $plconta->subtipo = $data['subtipo'];
        $plconta->ativo = 1;
        $plconta->id_users = $id_user;

        if (!$plconta->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $plconta->getEntity(), $plconta->id, $antes, $plconta->data());

        if (ll_intValida($id_plconta)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            if (isset($data['modalplconta']) && $data['modalplconta'] == 'novo') {
                $json['message'] = $this->message->success("CADASTRADO COM SUCESSO!")->render();
                $json['plconta'] = [
                    "id" => ll_encode($plconta->id),
                    "descricao" => $plconta->descricao
                ];
                $json['plcontamodal'] = true;
                $json['form'] = "#plconta-" . ($plconta->tipo == 'R' ? "rec" : "pag");
                echo json_encode($json);
                return;                
            } else {
                $this->message->success("CADASTRADO COM SUCESSO!")->flash();
            }
        }
        $json['redirect'] = url('plconta');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_plconta = ll_decode($data['id_plconta']);

        if (ll_intValida($id_plconta)) {
            $plconta = (new Plconta())->findById($id_plconta);
            $antes = clone $plconta->data();
            $plconta->id_users = $this->user->id;
            $plconta->ativo = '0';

            if ($plconta->save()) {
                $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
                $json["reload"] = true;
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $plconta->getEntity(), $plconta->id, $antes, $plconta->data());
        }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
