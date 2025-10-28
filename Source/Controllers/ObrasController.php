<?php

namespace Source\Controllers;

use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Obras;

class ObrasController extends Controller
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

        $empresa = (new Emp2())->findById($id_empresa);

        $obras = "";
        $obras = (new Obras())->find()->fetch(true);

        if (!empty($obras)) {
            foreach ($obras as $vlr) {
                $vlr->cliente = (new Ent())->findById($vlr->id_ent_cli)->nome;
            }
        }

        $front = [
            "titulo" => $empresa->labelFiliais . " - Taskforce",
            "user" => $this->user,
            "secTit" => $empresa->labelFiliais
        ];

        echo $this->view->render("tcsistemas.os/obras/obrasList", [
            "front" => $front,
            "obras" => $obras
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $obras = "";
        $secTit = "Cadastrar";

        $empresa = (new Emp2())->findById($id_empresa);

        if (isset($data['id_obras'])) {
            $id = ll_decode($data['id_obras']);
            $obras = (new Obras())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $clientes = (new Ent())->find(
            "tipo = :tipo",
            "tipo=1"
        )->fetch(true);

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " " . $empresa->labelFiliais
        ];

        echo $this->view->render("tcsistemas.os/obras/obrasCad", [
            "front" => $front,
            "obras" => $obras,
            "cliente" => $clientes
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_obras = ll_decode($data['id_obras']);
        $empresa = (new Emp2())->findById($id_empresa);

        $label = $empresa->labelFiliais;
        if ($label == "Obras") {
            $label = "Obra";
        } else if ($label == "Filiais") {
            $label = "Filial";
        }

        if (ll_intValida($id_obras)) {
            $obras = (new Obras())->findById($id_obras);
            $antes = clone $obras->data();
            $acao = "U";
        } else {
            $obras = new Obras();
            $antes = null;
            $acao = "C";
        }

        if (!empty($data['controle'])) {
            if (!str_verify($data['controle'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CONTROLE'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (empty($data['nome'])) {
            $json['message'] = $this->message->warning("Por favor, preencha o campo 'NOME'.")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['nome'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NOME'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        // if (empty($data['endereco'])) {
        //     $json['message'] = $this->message->warning("Por favor, preencha o campo 'ENDEREÇO'.")->render();
        //     echo json_encode($json);
        //     return;
        // }        

        if (!str_verify($data['endereco'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'ENDEREÇO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['numero'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NÚMERO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['complemento'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'COMPLEMENTO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['bairro'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'BAIRRO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['cidade'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CIDADE'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['uf'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'ESTADO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['cep'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CEP'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['proprietario'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'PROPRIETÁRIO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['area'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'ÁREA'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['localizacao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'LOCALIZAÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['obs'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'OBS'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $obras->id_emp2 = $id_empresa;
        $obras->id_ent_cli = $data['cliente-obra'];
        $obras->controle = $data['controle'];
        $obras->nome = $data['nome'];
        $obras->endereco = $data['endereco'] ?? null;
        $obras->numero = $data['numero'] ?? null;
        $obras->complemento = $data['complemento'] ?? null;
        $obras->bairro = $data['bairro'] ?? null;
        $obras->cidade = $data['cidade'] ?? null;
        $obras->uf = $data['uf'] ?? null;
        $obras->cep = $data['cep'] ?? null;
        $obras->proprietario = $data['proprietario'] ?? null;
        $obras->area = isset($data['area']) && $data['area'] !== '' ? (float)$data['area'] : null;
        $obras->localizacao = $data['localizacao'] ?? null;
        $obras->obs = $data['obs'] ?? null;
        $obras->id_users = $id_user;

        if (!$obras->save) {
            var_dump($obras->fail()->getMessage());
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $obras->getEntity(), $obras->id, $antes, $obras->data());

        if (ll_intValida($id_obras)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
            $json['redirect'] = url('obras');
        } else {
            if (!empty($data['modalobras'])) {
                $json['form'] = "#obra";
                $json['idcli'] = $obras->id;
                $json['nomecli'] = $obras->nome . " - " . $obras->endereco;
                $json['message'] = $this->message->success(mb_strtoupper($label) . " CADASTRADA COM SUCESSO!")->render();
            } else {
                $this->message->success("CADASTRADO COM SUCESSO!")->flash();
                $json['redirect'] = url('obras');
            }
        }

        echo json_encode($json);
    }

    public function listar($data): void
    {
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $segmentos = (new Obras())->find(
            "id_ent_cli = :id_ent_cli",
            "id_ent_cli={$data['id']}"
        )->fetch(true);

        if (!empty($segmentos)) {
            $dataSegmentos = objectsToArray($segmentos);
            $json["status"] = "success";
            $json["segmentos"] = $dataSegmentos;
        } else {
            $json["status"] = "error";
            $json["label"] = str_to_single($empresa->labelFiliais);
        }

        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_obras = ll_decode($data['id_obras']);

        if (ll_intValida($id_obras)) {
            $obras = (new Obras())->findById($id_obras);
            $antes = clone $obras->data();

            if (!$obras->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $obras->getEntity(), $obras->id, $antes, null);

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
