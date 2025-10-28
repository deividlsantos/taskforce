<?php

namespace Source\Controllers;

use Source\Models\Emp2;
use Source\Models\Log;
use Source\Models\Plconta;
use Source\Models\Recorrencias;
use Source\Models\Servico;

class ServicoController extends Controller
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

        $servico = "";
        $servico = (new Servico())->find()->fetch(true);

        $front = [
            "titulo" => "Serviços - Taskforce",
            "user" => $this->user,
            "secTit" => "Serviços"
        ];

        echo $this->view->render("tcsistemas.os/servico/servicoList", [
            "front" => $front,
            "servico" => $servico
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);
        $recorrenciasPadrao = (new Recorrencias())->find("padrao = :padrao", "padrao=1", "*", false)->fetch(true);
        $recorrenciasEmpresa = (new Recorrencias())->find("id_emp2 = :id_emp2 AND padrao = :padrao", "id_emp2={$id_empresa}&padrao=0")->fetch(true);
        $recorrencias = array_merge($recorrenciasPadrao ?? [], $recorrenciasEmpresa ?? []);
        $plconta = (new Plconta())->find(
            "ativo = :ativo AND tipo = :tipo",
            "ativo=1&tipo=R"
        )->order('descricao')->fetch(true);

        $servico = "";
        $secTit = "Cadastrar";

        if (isset($data['id_servico'])) {
            $id = ll_decode($data['id_servico']);
            $servico = (new Servico())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Serviço"
        ];

        echo $this->view->render("tcsistemas.os/servico/servicoCad", [
            "front" => $front,
            "servico" => $servico,
            "empresa" => $empresa,
            "recorrencias" => $recorrencias,
            "plconta" => $plconta
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_servico = ll_decode($data['id_servico']);

        if (ll_intValida($id_servico)) {
            $servico = (new Servico())->findById($id_servico);
            $antes = clone $servico->data();
            $acao = "U";
        } else {
            $servico = new Servico();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['nome'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NOME'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!ll_intValida($data['tempo'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'TEMPO'. Tente novamente!")->render();
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


        if (!empty($data['intervalo']) && !ll_intValida($data['intervalo'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'INTERVALO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['obs'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'OBSERVAÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (isset($data['recorrente'])) {
            if (empty($data['recorrencia'])) {
                $json['message'] = $this->message->warning("Para o campo 'RECORRÊNCIA' é necessário selecionar uma opção.")->render();
                echo json_encode($json);
                return;
            }

            if (!in_array($data['recorrencia'], [1, 2])) {
                if (empty($data['datafixa'])) {
                    $json['message'] = $this->message->warning("Preencha o campo 'DIA'!")->render();
                    echo json_encode($json);
                    return;
                }

                $data['datafixa'] = (int) ltrim($data['datafixa'], '0');
                if (!ll_intValida($data['datafixa']) || $data['datafixa'] < 1 || $data['datafixa'] > 31) {
                    $json['message'] = $this->message->warning("Dia inválido. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
                $servico->dia = $data['datafixa'];
            } else {
                $servico->dia = null;
            }
        }

        if (isset($data['recor_datalegal'])) {
            $servico->recor_datalegal = $data['recor_datalegal'];

            if ($data['recor_datalegal'] != 'livre') {
                if (empty($data['datalegal'])) {
                    $json['message'] = $this->message->warning("Preencha o campo 'DATA LEGAL'.")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if (!empty($data['datalegal'])) {
                $currentYear = date('Y');
                $datalegalParts = explode('/', $data['datalegal']);
                if (count($datalegalParts) === 2) {
                    $servico->datalegal = "{$currentYear}-{$datalegalParts[1]}-{$datalegalParts[0]}";
                } else {
                    $json['message'] = $this->message->error("Formato inválido para o campo 'DATA LEGAL'.")->render();
                    echo json_encode($json);
                    return;
                }
            }
        }

        $servico->id_emp2 = $id_empresa;
        $servico->nome = $data['nome'];
        $servico->tempo = $data['tempo'] * 60;
        $servico->valor = moedaSql($data['valor']);
        $servico->custo = moedaSql($data['custo']);
        //$servico->intervalo = isset($data['recorrente']) ? $data['intervalo'] : null;
        $servico->recorrente = isset($data['recorrente']) ? 1 : 0;
        $servico->id_recorrencia = isset($data['recorrente']) ? $data['recorrencia'] : null;
        $servico->medicao = isset($data['medicao']) ? 1 : 0;
        $servico->medida = isset($data['medida']) ? $data['medida'] : null;
        $servico->id_plconta = !empty($data['id_plconta']) ? $data['id_plconta'] : null;
        $servico->obs = $data['obs'];
        $servico->id_users = $id_user;

        if (!$servico->save) {
            $error = $servico->fail();
            $errorMessage = $error ? $error->getMessage() : "Erro ao salvar!";
            $json['message'] = $this->message->warning($errorMessage)->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $servico->getEntity(), $servico->id, $antes, $servico->data());

        if (!empty($data['modalsrv']) && $data['modalsrv'] == 'novo') {
            $json['message'] = $this->message->success("Serviço cadastrado com sucesso!")->render();
            $json['select'] = $data['target-select'];
            $json['novosrv'] = true;
            $json['id_servico'] = $servico->id;
            $json['nome'] = $servico->nome;
            $json['valor'] = $servico->valor;
            $json['tempo'] = $servico->tempo;
            $json['medicao'] = $servico->medicao;
            $json['unidade'] = $servico->medida;
            $json['recorrencia'] = $servico->id_recorrencia;
            $json['diarecorrencia'] = $servico->dia;
            $json['datalegal'] = !empty($servico->datalegal) ? calculaDataRecorrente($servico->datalegal, $servico->recor_datalegal) : "";;
        } else {
            if (ll_intValida($id_servico)) {
                $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
            } else {
                $this->message->success("CADASTRADO COM SUCESSO!")->flash();
            }
            $json['redirect'] = url('servico');
        }

        echo json_encode($json);
    }

    public function retornaServicos()
    {
        $id_empresa = $this->user->id_emp2;

        $servicos = (new Servico())->find("", "", "id, nome")->order('nome')->fetch(true);
        $dados = [];

        $servicosData = [];
        if(!empty($servicos)){
         $servicosData = objectsToArray($servicos);
        }

        echo json_encode($servicosData);
    }

    public function excluir($data): void
    {
        $id_servico = ll_decode($data['id_servico']);

        if (ll_intValida($id_servico)) {
            $servico = (new Servico())->findById($id_servico);
            $antes = clone $servico->data();

            if (!$servico->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $servico->getEntity(), $servico->id, $antes, null);

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
