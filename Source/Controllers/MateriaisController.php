<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\Materiais;

class MateriaisController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->os != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $materiais = "";
        $materiais = (new Materiais())->find()->fetch(true);

        $front = [
            "titulo" => "Produtos/Materiais - Taskforce",
            "user" => $this->user,
            "secTit" => "Produtos/Materiais"
        ];

        echo $this->view->render("tcsistemas.os/materiais/materiaisList", [
            "front" => $front,
            "materiais" => $materiais
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $materiais = "";
        $secTit = "Cadastrar";

        if (isset($data['id_materiais'])) {
            $id = ll_decode($data['id_materiais']);
            $materiais = (new Materiais())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Produto/Material"
        ];

        echo $this->view->render("tcsistemas.os/materiais/materiaisCad", [
            "front" => $front,
            "materiais" => $materiais
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_materiais = ll_decode($data['id_materiais']);

        if (ll_intValida($id_materiais)) {
            $materiais = (new Materiais())->findById($id_materiais);
            $antes = clone $materiais->data();
            $acao = "U";
        } else {
            $materiais = new Materiais();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['unidade'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'UNIDADE'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!float_verify($data['valor'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'VALOR'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!float_verify($data['custo'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CUSTO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $materiais->id_emp2 = $id_empresa;
        $materiais->descricao = $data['descricao'];
        $materiais->unidade = $data['unidade'];
        $materiais->valor = moedaSql($data['valor']);
        $materiais->custo = moedaSql($data['custo']);
        $materiais->id_users = $id_user;

        if (!$materiais->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $materiais->getEntity(), $materiais->id, $antes, $materiais->data());

        if (ll_intValida($id_materiais)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('materiais');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_materiais = ll_decode($data['id_materiais']);

        if (ll_intValida($id_materiais)) {
            $materiais = (new Materiais())->findById($id_materiais);

            $antes = clone $materiais->data();

            if (!$materiais->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $materiais->getEntity(), $materiais->id, $antes, null);

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
