<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp1;
use Source\Models\Emp2;
use Source\Models\Oper;
use Source\Models\Plconta;
use Source\Models\Tipo;

class Emp2Controller
{

    private $view;
    private $message;
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

        if ($this->user->tipo < 5) {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {

        //$empresas555 = (new emp2())->find("plano <> :plano or plano IS NULL", "plano=vencido" , "*", false);
        $empresas =  (new emp2())->find(null, null, "*", false)->fetch(true) ?? [];
        $grupos = (new Emp1())->find(null, null, "*", false)->fetch(true);


        $front = [
            "titulo" => "Cadastro de empresas - Task Force",
            "user" => $this->user,
            "secTit" => "Cadastro de empresas"
        ];

        echo $this->view->render("tcsistemas.adm/empresas/empresaList", [
            "front" => $front,
            "empresas" => $empresas, // Passando variável para view
            "grupos" => $grupos
        ]);
    }


    public function form($data)
    {
        $empresa = "";
        if (isset($data["id_emp2"])) {
            $id = $data["id_emp2"];
            $empresa = (new emp2())->findById($id);
        };

        $grupos = (new Emp1())->find(null, null, "*", false)->fetch(true) ?? [];

        $front = [
            "titulo" => "Cadastro de empresas - Task Force",
            "user" => $this->user,
            "secTit" => ""
        ];

        echo $this->view->render("tcsistemas.adm/empresas/empresaCad", [
            "front" => $front,
            "empresa" => $empresa,
            "grupos" => $grupos
        ]);
    }


    public function salvar($data)
    {
        $emp = new Emp2();
        $novo = true;
        if (!empty($data['id'])) {
            $id = $data['id'];
            $emp = $emp->findById($id);
            $novo = false;
        }

        if (empty($data['grupo'])) {
            $resposta['message'] = $this->message->warning("Informe o grupo da empresa")->render();
            echo json_encode($resposta);
            return;
        }

        $emp->beginTransaction();

        $emp->id_emp1 = $data['grupo'];
        $emp->razao = $data['razao'];
        $emp->fantasia = $data['fantasia'];
        $emp->cnpj = $data['cnpj'];
        $emp->endereco = $data['logradouro'];
        $emp->numero = $data['numero'];
        $emp->bairro = $data['bairro'];
        $emp->cidade = $data['cidade'];
        $emp->uf = $data['uf'];
        $emp->cep = $data['cep'];
        $emp->fone1 = $data['telefone1'];
        $emp->fone2 = $data['telefone2'];
        $emp->email = $data['email'];
        $emp->plano = 1;
        $emp->qtdeadm = 1;
        $emp->qtdeoper = 1;

        if ($emp->save()) {
            $json['message'] = $this->message->success("Cadastro salvo com sucesso")->flash();
            $json['redirect'] = url("/emp2");
        } else {
            $json['message'] = $this->message->error("Erro ao salvar o cadastro")->render();
            $emp->rollback();
            echo json_encode($json);
            return;
        }

        //CADASTRO DO TIPO DE OS DEFAULT PARA NOVAS EMPRESAS
        if($novo){
            $tipo = new Tipo();
            $tipo->id_emp2 = $emp->id;
            $tipo->id_users = $this->user->id;
            $tipo->descricao = "PADRÃO";

            if(!$tipo->save()){
                $emp->rollback();
                $json['message'] = $this->message->error("Erro ao salvar o cadastro de tipo padrão")->render();
                echo json_encode($json);
                return;
            }
        }
        $emp->commit();

        echo json_encode($json);
    }

    public function verificaPadroes(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $plconta = (new Plconta())->find()->fetch(true);
        $operacoes = (new Oper())->find()->fetch(true);

        if(empty($plconta) || empty($operacoes)){
            $json['message'] = $this->message->warning("Para marcar essa opção, é necessário que existam planos de conta e operações cadastradas no sistema.")->render();
            $json['status'] = false;
            echo json_encode($json);
            return;
        }

        $json['status'] = true;
        echo json_encode($json);
    }


    public function excluir(array $data): void
    {
        $id = $data['id_emp2'];

        if (ll_intValida($id)) {
            $emp = (new Emp2())->findById($id);
            $emp->plano = 'vencido';
            if (!($emp->save())) {
                $json['message'] = $this->message->warning("Não foi possível excluir essa empresa")->render();
                echo json_encode($json);
                return;
            } else {
                $json['message'] = $this->message->success("Empresa inativada")->flash();
                $json['reload'] = true;
                echo json_encode($json);
            };
        }
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
