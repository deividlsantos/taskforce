<?php

namespace Source\Controllers;

use Source\Models\Log;
use Source\Models\Oper;

class OperacaoController extends Controller
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
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => "Operações"
        ];

        $operacao = (new Oper())->find()->fetch(true);

        echo $this->view->render("tcsistemas.financeiro/operacao/operacaoList", [
            "front" => $front,
            "operacao" => $operacao
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $operacao = "";
        $secTit = "Cadastrar";

        if (isset($data['id_operacao'])) {
            $id = ll_decode($data['id_operacao']);
            $operacao = (new Oper())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Operação"
        ];

        echo $this->view->render("tcsistemas.financeiro/operacao/operacaoCad", [
            "front" => $front,
            "operacao" => $operacao
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_oper = isset($data['id_oper']) ? ll_decode($data['id_oper']) : null;

        if (ll_intValida($id_oper)) {
            $operacao = (new Oper())->findById($id_oper);
            $antes = clone $operacao->data();
            $acao = "U";
        } else {
            $operacao = new Oper();
            $antes = null;
            $acao = "C";
        }

        $operacao->id_emp2 = $id_empresa;
        $operacao->descricao = $data['descricao'];
        $operacao->id_users = $id_user;

        if (!$operacao->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $operacao->getEntity(), $operacao->id, $antes, $operacao->data());

        if (ll_intValida($id_oper)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            if (isset($data['modal-opr']) && $data['modal-opr'] == "novo") {
                $json['message'] = $this->message->success("CADASTRADO COM SUCESSO!")->render();
                $json['operacao'] = [
                    "id" => $operacao->id,
                    "descricao" => $operacao->descricao
                ];
                $json['modaloperacao'] = true;
                $json['form'] = $data['tipo'] == "D" ? "operacao-pag" : "operacao-rec";
                echo json_encode($json);
                return;
            } else {
                $this->message->success("CADASTRADO COM SUCESSO!")->flash();
            }
        }
        $json['redirect'] = url('operacao');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_oper = ll_decode($data['id_oper']);

        if (ll_intValida($id_oper)) {
            $oper = (new Oper())->findById($id_oper);
            $antes = clone $oper->data();

            if (!$oper->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $oper->getEntity(), $oper->id, $antes, null);

            $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
            $json["reload"] = true;
            echo json_encode($json);
        }
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
