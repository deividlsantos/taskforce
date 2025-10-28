<?php

namespace Source\Controllers;

use Source\Models\ChkGrupo;
use Source\Models\ChkItem;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\EqpEstoque;
use Source\Models\EqpKardex;
use Source\Models\EqpLocal;
use Source\Models\EqpMov;
use Source\Models\Equipamentos;
use Source\Models\Log;
use Source\Models\Plconta;
use Source\Models\Users;

class EquipamentosController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $equipamento = (new Equipamentos())->find("ativo = :ativo", "ativo=A")->fetch(true);

        $front = [
            "titulo" => "Cadastro de automóveis - Task Force",
            "user" => $this->user,
            "secTit" => "Cadastro de veículos, máquinas e equipamentos"
        ];

        echo $this->view->render("tcsistemas.os/equipamentos/equipamentosList", [
            "front" => $front,
            "equipamento" => $equipamento
        ]);
    }

    public function form($data)
    {
        $equipamento = "";
        $secTit = "Cadastrar";

        if (isset($data['id_equipamento'])) {
            $secTit = "Editar";
            $id = ll_decode($data["id_equipamento"]);
            $equipamento = (new Equipamentos())->findById($id);
        }

        $gruposChklist = (new ChkGrupo())->find()->fetch(true);
        $itensChklist = (new ChkItem())->find()->fetch(true);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);
        $plconta = (new Plconta())->find(
            "ativo = :ativo AND tipo = :tipo",
            "ativo=1&tipo=R"
        )->order('descricao')->fetch(true);
        $status = (new Equipamentos())->statusList();
        $classeEquipamento = (new Equipamentos())->classeEquipamentoList();
        $classeOperacional = (new Equipamentos())->classeOperacionalList();
        $especieEquipamento = (new Equipamentos())->especieEquipamentoList();

        $front = [
            "titulo" => "Cadastro de automóveis - Task Force",
            "user" => $this->user,
            "secTit" => $secTit . " Equipamento"
        ];

        echo $this->view->render("tcsistemas.os/equipamentos/equipamentosCad", [
            "front" => $front,
            "equipamento" => $equipamento,
            "status" => $status,
            "classeEquipamento" => $classeEquipamento,
            "classeOperacional" => $classeOperacional,
            "especieEquipamento" => $especieEquipamento,
            "cliente" => $cliente,
            "plconta" => $plconta,
            "gruposChklist" => $gruposChklist,
            "itensChklist" => $itensChklist
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = ll_decode($data['id_equipamento']);

        if (ll_intValida($id)) {
            $equipamento = (new Equipamentos())->findById($id);
            $antes = clone $equipamento->data();
            $acao = "U";
        } else {
            $equipamento = new Equipamentos();
            $antes = null;
            $acao = "C";
        }

        if (ll_intValida($id)) {
            if (!empty($equipamento->id_cli && $data['inventario'] == 'on')) {
                $json['message'] = $this->message->warning("Equipamento já vinculado a um cliente. Não é possível adicionar ao inventário!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (empty($data['descricao'])) {
            $json['message'] = $this->message->warning("Preencha o campo 'DESCRIÇÃO'.")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente.")->render();
            echo json_encode($json);
            return;
        }

        if (empty($data['equipamento'])) {
            $json['message'] = $this->message->warning("Selecione o 'TIPO DE EQUIPAMENTO'.")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['anofab'])) {
            if (!ll_intValida($data['anofab'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para 'ANO DE FABRICAÇÃO'.")->render();
                echo json_encode($json);
                return;
            }
        }


        if (isset($data['inventario']) && $data['inventario'] !== 'on') {
            if (empty($data['id_cli'])) {
                $json['message'] = $this->message->warning("Selecione o 'CLIENTE'.")->render();
                echo json_encode($json);
                return;
            }
        }


        if (!empty($data['status'])) {
            if (!str_verify($data['status'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'STATUS'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['modelofab'])) {
            if (!str_verify($data['modelofab'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'MODELO'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['chassi'])) {
            if (!str_verify($data['chassi'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'CHASSI'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['placa'])) {
            if (!str_verify($data['placa'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'PLACA'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['renavam'])) {
            if (!str_verify($data['renavam'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'RENAVAM'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['classe_equipamento'])) {
            if (!str_verify($data['classe_equipamento'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'CLASSE DO EQUIPAMENTO'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['classe_operacional'])) {
            if (!str_verify($data['classe_operacional'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'CLASSE OPERACIONAL'.")->render();
                echo json_encode($json);
                return;
            }
        }
        if (!empty($data['especie_equipamento'])) {
            if (!str_verify($data['especie_equipamento'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'ESPÉCIE DO EQUIPAMENTO'.")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['grupo_rec'])) {
            if (!str_verify($data['grupo_rec'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'GRUPO DE RECEITA'.")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['autonomia'])) {
            if (!str_verify($data['autonomia'])) {
                $json['message'] = $this->message->error("Caracteres inválidos para o campo 'AUTONOMIA'.")->render();
                echo json_encode($json);
                return;
            }
        }

        $equipamento->id_emp2 = $id_empresa;
        $equipamento->id_users = $id_user;
        $equipamento->id_cli = !empty($data['id_cli']) ? $data['id_cli'] : null;
        $equipamento->equipamento = $data['equipamento'];
        $equipamento->descricao = $data['descricao'];
        $equipamento->anofab = !empty($data['anofab']) ? $data['anofab'] : null;
        $equipamento->status = $data['status'];
        $equipamento->modelofab = $data['modelofab'];
        $equipamento->chassi = $data['chassi'];
        $equipamento->placa = $data['placa'];
        $equipamento->renavam = $data['renavam'];
        $equipamento->serie = $data['serie'];
        $equipamento->tag = $data['tag'];
        $equipamento->fabricante = $data['fabricante'];
        $equipamento->classe_equipamento = $data['classe_equipamento'];
        $equipamento->classe_operacional = $data['classe_operacional'];
        $equipamento->especie_equipamento = $data['especie_equipamento'];
        $equipamento->id_plconta = !empty($data['id_plconta']) ? $data['id_plconta'] : null;
        $equipamento->combustivel = !empty($data['combustivel']) ? $data['combustivel'] : null;
        $equipamento->unidade = $data['unidade'];
        $equipamento->autonomia = !empty($data['autonomia']) ? float_br_to_us($data['autonomia']) : null;
        $equipamento->ativo = isset($data['ativo']) && $data['ativo'] === 'on' ? 'A' : 'I';
        $equipamento->inventario = isset($data['inventario']) && $data['inventario'] === 'on' ? '1' : '0';

        if (!empty($data['chklist_itens']) && is_array($data['chklist_itens'])) {
            $equipamento->id_chkitens = implode(', ', $data['chklist_itens']);
        } else {
            $equipamento->id_chkitens = null;
        }

        if (!$equipamento->save()) {
            $equipamento->fail();
            $json['message'] = $this->message->error($equipamento->fail()->getMessage())->render();
            //$json['message'] = $this->message->error("Erro ao salvar o cadastro")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $equipamento->getEntity(), $equipamento->id, $antes, $equipamento->data());

        if (ll_intValida($id)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO!")->flash();
        } else {
            $this->message->success("REGISTRO CADASTRADO COM SUCESSO!")->flash();
        }

        $json['redirect'] = url('equipamentos');
        echo json_encode($json);
    }


    public function excluir(array $data): void
    {
        $id = ll_decode($data['id_equipamento']);

        if (ll_intValida($id)) {
            $equipamento = (new Equipamentos())->findById($id);
            $antes = clone $equipamento->data();

            if ($equipamento->destroy()) {
                $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
                $json["reload"] = true;
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $equipamento->getEntity(), $equipamento->id, $antes, null);
        }
    }

    public function gestaoeqp()
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $user_tipo = $this->user->tipo;

        $empresa = (new Emp2())->findById($id_empresa);

        $equipamentos = (new Equipamentos())->find(
            "inventario = :inventario AND ativo = :ativo",
            "inventario=1&ativo=A"
        )->fetch(true);

        $usuarios = (new Users())->find()->fetch(true);
        $locais = (new EqpLocal())->find()->fetch(true);
        $estoque = (new EqpEstoque())->find()->fetch(true);

        if ($this->user->tipo == '1' || $this->user->tipo == '5') {
            $solicitacoes = (new EqpMov())->find(
                "status = :status",
                "&status=A"
            )->fetch(true);
            $enviadas = [];
        } else {
            $solicitacoes = (new EqpMov())->find(
                "id_user_destino = :id_user_destino AND status = :status",
                "id_user_destino={$id_user}&status=A"
            )->fetch(true);
            $enviadas = (new EqpMov())->find(
                "id_user_origem = :id_user_origem AND status = :status",
                "id_user_origem={$id_user}&status=A"
            )->fetch(true);
        }

        $fornecedores = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=2&status=A"
        )->fetch(true);

        if (!empty($locais)) {
            foreach ($locais as $local) {
                if ($local->status == '1') {
                    $local->desc_status = 'Estoque';
                } elseif ($local->status == '2') {
                    $local->desc_status = 'Entrada';
                } elseif ($local->status == '3') {
                    $local->desc_status = 'Inativo';
                } elseif ($local->status == '4') {
                    $local->desc_status = 'Alocados';
                } elseif ($local->status == '5') {
                    $local->desc_status = 'Manutenção';
                }
            }
        }

        if (!empty($estoque)) {
            foreach ($estoque as $e) {
                foreach ($locais as $local) {
                    if ($local->id == $e->id_local) {
                        $e->status = $local->status;
                    }
                }
            }
        }

        $front = [
            "titulo" => "Equipamentos - Task Force",
            "user" => $this->user,
            "secTit" => "Gestão de Equipamentos/Ferramentas"
        ];

        echo $this->view->render("tcsistemas.os/equipamentos/equipamentosGestaoList", [
            "front" => $front,
            "user" => $user_tipo,
            "equipamentos" => $equipamentos,
            "usuarios" => $usuarios,
            "locais" => $locais,
            "fornecedores" => $fornecedores,
            "estoque" => $estoque,
            "empresa" => $empresa,
            "solicitacoes" => $solicitacoes,
            "enviadas" => $enviadas
        ]);
    }

    public function verificaEstoque($data)
    {
        $id_equipamento = $data['id'];
        $estoque = (new EqpEstoque())->find(
            "id_equipamento = :id_equipamento",
            "id_equipamento={$id_equipamento}"
        )->fetch(true);

        $locais = [];
        if (!empty($estoque)) {
            foreach ($estoque as $e) {
                if ($e->qtde > 0) {
                    $locais[] = [$e->id_local => $e->qtde];
                }
            }
        }
        echo json_encode($locais);
    }

    public function refreshLocal($data)
    {
        if (isset($data['value']) && $data['value'] == "refresh-local") {
            $locais = (new EqpLocal())->find()->fetch(true);
            $localData = [];
            if (!empty($locais)) {
                foreach ($locais as $l) {
                    if ($l->status == '1') {
                        $l->desc_status = 'Estoque';
                    } elseif ($l->status == '2') {
                        $l->desc_status = 'Entrada';
                    } elseif ($l->status == '3') {
                        $l->desc_status = 'Inativo';
                    } elseif ($l->status == '4') {
                        $l->desc_status = 'Alocados';
                    } elseif ($l->status == '5') {
                        $l->desc_status = 'Manutenção';
                    }
                }
                $localData = objectsToArray($locais);
            }
            $json['dados'] = $localData;
            echo json_encode($json);
            return;
        }

        $json['message'] = $this->message->error("Ação não permitida!")->render();
        echo json_encode($json);
    }

    public function listarSolicitacoes()
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if ($this->user->tipo == '1' || $this->user->tipo == '5') {
            $todas = (new EqpMov())->find(
                "status = :status",
                "&status=A"
            )->fetch(true);

            $recebidas = $todas;
            $enviadas = [];
            $tipoUser = 'admin';
        } else {
            $recebidas = (new EqpMov())->find(
                "id_user_destino = :id_user_destino AND status = :status",
                "id_user_destino={$id_user}&status=A"
            )->fetch(true);

            $enviadas = (new EqpMov())->find(
                "id_user_origem = :id_user_origem AND status = :status",
                "id_user_origem={$id_user}&status=A"
            )->fetch(true);
            $tipoUser = 'user';
        }

        $formatar = function ($solicitacoes) {
            if (empty($solicitacoes)) {
                return [];
            }

            foreach ($solicitacoes as $s) {
                $s->data_formatada = date_fmt($s->data_hora, "d/m/Y H:i:s");
                $s->usuario_origem_nome = (new Users())->findById($s->id_user_origem)->nome;
                $s->usuario_destino_nome = (new Users())->findById($s->id_user_destino)->nome;
                $s->equipamento_desc = (new Equipamentos())->findById($s->id_equipamento)->descricao;
                $s->local_origem_desc = (new EqpLocal())->findById($s->id_local_origem)->descricao;
                $s->local_destino_desc = (new EqpLocal())->findById($s->id_local_destino)->descricao;
            }

            return objectsToArray($solicitacoes);
        };

        $json = [
            "recebidas" => $formatar($recebidas ?? []),
            "enviadas" => $formatar($enviadas ?? []),
            "user" => $tipoUser
        ];


        echo json_encode($json);
    }

    public function retornaSolicitacao($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = $data['id'];

        if (!ll_intValida($id)) {
            $json['message'] = $this->message->error("Ação não permitida!")->render();
            echo json_encode($json);
            return;
        }

        $solicitacao = (new EqpMov())->findById($id);
        $solicitacao->data_formatada = date_fmt((new EqpKardex())->find("id_mov = :id_mov AND saida > :saida", "id_mov={$id}&saida=0")->fetch()->data_hora);
        $solicitacao->usuario_origem_nome = (new Users())->findById($solicitacao->id_user_origem)->nome;
        $solicitacao->usuario_destino_nome = (new Users())->findById($solicitacao->id_user_destino)->nome;
        $solicitacao->equipamento_desc = (new Equipamentos())->findById($solicitacao->id_equipamento)->descricao;
        $solicitacao->local_origem_desc = (new EqpLocal())->findById($solicitacao->id_local_origem)->descricao;
        $solicitacao->local_destino_desc = (new EqpLocal())->findById($solicitacao->id_local_destino)->descricao;

        $solicitacao->escondeConfirmar = false;
        $solicitacao->escondeCancelar = false;

        if ($this->user->tipo != '1' && $this->user->tipo != '5') {
            if ($id_user == $solicitacao->id_user_destino) {
                $solicitacao->escondeCancelar = true;
            } else if ($id_user == $solicitacao->id_user_origem) {
                $solicitacao->escondeConfirmar = true;
            }
        }

        $solicitacaoData = objectsToArray($solicitacao);

        echo json_encode($solicitacaoData);
    }

    public function listarAlocados($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_equipamento = $data['id'];

        $tabelaLocal = (new EqpLocal())->getEntity();
        $tabelaEstoque = (new EqpEstoque())->getEntity();

        $estoque = (new EqpEstoque())->join(
            $tabelaLocal,
            "{$tabelaLocal}.id = {$tabelaEstoque}.id_local",
            "LEFT"
        );

        $dados = $estoque->find(
            "{$tabelaEstoque}.id_equipamento = :id_equipamento AND {$tabelaEstoque}.id_emp2 = :id_emp2",
            "id_equipamento={$id_equipamento}&id_emp2={$id_empresa}",
            "*",
            false
        )->fetch(true);

        $locais = (new EqpLocal())->find()->fetch(true);

        $alocados = [];

        if (!empty($locais)) {
            foreach ($locais as $local) {
                if (!empty($dados)) {
                    foreach ($dados as $dado) {
                        if ($local->id == $dado->id_local) {
                            if ($dado->status == '4' && $dado->qtde > 0) {
                                $dado->local_desc = $local->descricao;

                                $kardex = (new EqpKardex())->find(
                                    "id_equipamento = :id_equipamento AND id_local = :id_local AND entrada > :entrada",
                                    "id_equipamento={$id_equipamento}&id_local={$local->id}&entrada=0",
                                    "id_usuario"
                                )->order("id DESC")->limit(1)->fetch();

                                $dado->last_user = !empty($kardex) ? (new Users())->findById($kardex->id_usuario)->nome : "";

                                $alocados[] = $dado->data;
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($alocados);
    }

    public function listarKardex($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_equipamento = $data['id'];

        $kardex = (new EqpKardex())->find(
            "id_equipamento = :id_equipamento",
            "id_equipamento={$id_equipamento}"
        )->order("id DESC")->fetch(true);

        $kardexData = [];

        if (!empty($kardex)) {
            foreach ($kardex as $k) {
                $k->data_formatada = date_fmt($k->data_hora, "d/m/Y");
                $k->usuario_nome = (new Users())->findById($k->id_usuario)->nome;
                $k->local_nome = (new EqpLocal())->findById($k->id_local)->descricao;
            }

            $kardexData = objectsToArray($kardex);
        }

        echo json_encode($kardexData);
    }

    public function salvarLocal(array $data): void
    {
        //aproveite essa função pra devolver os locais cadastrados ao abrir a modal usando esse laço
        if (isset($data['value']) && $data['value'] == "modal") {
            $locais = (new EqpLocal())->find()->fetch(true);
            $localData = [];
            if (!empty($locais)) {
                foreach ($locais as $l) {
                    $l->id_encode = ll_encode($l->id);
                }
                $localData = objectsToArray($locais);
            }
            $json['dados'] = $localData;
            echo json_encode($json);
            return;
        }

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = "";
        if (isset($data['id'])) {
            $id = ll_decode($data['id']);
        }


        if (ll_intValida($id)) {
            $local = (new EqpLocal())->findById($id);
            $antes = clone $local->data();
            $acao = "U";
        } else {
            $local = new EqpLocal();
            $antes = null;
            $acao = "C";
        }

        if (empty($data['descricao'])) {
            $json['message'] = $this->message->warning("Preencha o campo 'DESCRIÇÃO'.")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente.")->render();
            echo json_encode($json);
            return;
        }

        $local->descricao = $data['descricao'];
        $local->status = !empty($data['status']) ? $data['status'] : $local->status;
        $local->id_emp2 = $id_empresa;
        $local->id_users = $id_user;

        if (!$local->save()) {
            $local->fail();
            $json['message'] = $this->message->error($local->fail()->getMessage())->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $local->getEntity(), $local->id, $antes, $local->data());

        $locais = (new EqpLocal())->find()->fetch(true);
        $localData = [];
        if (!empty($locais)) {
            foreach ($locais as $l) {
                $l->id_encode = ll_encode($l->id);
            }
            $localData = objectsToArray($locais);
        }

        if (ll_intValida($id)) {
            $json['message'] =  $this->message->success("Atualizado com sucesso!")->render();
        } else {
            $json['message'] =  $this->message->success("Registrado com sucesso!")->render();
            $json['status'] = "success";
            $json['dados'] = $localData;
        }

        echo json_encode($json);
    }


    public function excluirLocal(array $data): void
    {
        $id = ll_decode($data['id']);

        if (ll_intValida($id)) {
            $local = (new EqpLocal())->findById($id);
            $antes = clone $local->data();

            if ($local->destroy()) {
                $json['message'] = $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->render();
                $json['status'] = "success";
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $local->getEntity(), $local->id, $antes, null);
        }
    }

    private function verificaForm($data)
    {
        if (empty($data['id_equipamento'])) {
            $return['message'] = "Selecione a ferramenta.";
            $return['status'] = false;
            return $return;
        }

        if (empty($data['qtde'])) {
            $return['message'] = "Informe a quantidade.";
            $return['status'] = false;
            return $return;
        }

        $isEntrada = isset($data['entrada']);
        $isSaida = isset($data['saida']);

        if ($isEntrada && $isSaida) {
            $return['message'] = "Marque apenas Entrada ou Saída, ou deixe ambos desmarcados para Transferência.";
            $return['status'] = false;
            return $return;
        }

        if ($isEntrada && empty($data['id_local_destino'])) {
            $return['message'] = "Informe o local de destino para entrada.";
            $return['status'] = false;
            return $return;
        }

        if ($isSaida && empty($data['id_local_origem'])) {
            $return['message'] = "Informe o local de origem para saída.";
            $return['status'] = false;
            return $return;
        }

        if (!$isEntrada && !$isSaida) {
            if (empty($data['id_local_origem']) || empty($data['id_local_destino'])) {
                $return['message'] = "Para transferência, informe local de origem e destino.";
                $return['status'] = false;
                return $return;
            }
        }

        return ['status' => true];
    }

    /**
     * @param mixed $data (dados do formulário)
     * @param mixed $status (estado da movimentação, padrão "F" para finalizada)
     * @return array<bool|string>
     */
    private function submitForm($data, $status = "F") //essa função cria uma solicitação se o status for "A" ou finaliza a movimentação se for "F"
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $isEntrada = isset($data['entrada']);
        $isSaida = isset($data['saida']);

        $confirmacao = false;
        if (isset($data['id_mov']) && ll_intValida($data['id_mov'])) {
            $movimentacao = (new EqpMov())->findById($data['id_mov']);
            if (!$movimentacao) {
                $return['message'] = "Movimentação não encontrada.";
                $return['status'] = false;
                return $return;
            }
            $movimentacao->id_users = $id_user;
            $movimentacao->status = $status;
            $confirmacao = true;

            $antes = clone $movimentacao->data();
            $acao = "U";
        } else {
            $movimentacao = new EqpMov();

            $movimentacao->id_emp2 = $id_empresa;
            $movimentacao->id_users = $id_user;
            $movimentacao->id_equipamento = $data['id_equipamento'];
            $movimentacao->qtde = $data['qtde'];
            $movimentacao->id_fornecedor = !empty($data['id_fornecedor']) ? $data['id_fornecedor'] : null;
            $movimentacao->id_user_origem = !empty($data['id_usuario_origem']) ? $data['id_usuario_origem'] : null;
            $movimentacao->id_local_origem = !empty($data['id_local_origem']) ? $data['id_local_origem'] : null;
            $movimentacao->id_user_destino = !empty($data['id_usuario_destino']) ? $data['id_usuario_destino'] : null;
            $movimentacao->id_local_destino = !empty($data['id_local_destino']) ? $data['id_local_destino'] : null;
            $movimentacao->obs = !empty($data['observacao']) ? $data['observacao'] : null;
            $movimentacao->status = $status;

            $antes = null;
            $acao = "C";
        }

        $movimentacao->beginTransaction();

        if (!$movimentacao->save()) {
            $movimentacao->fail();
            $movimentacao->rollback();
            $return['message'] = $movimentacao->fail()->getMessage();
            $return['status'] = false;
            return $return;
        }

        $log = new Log();
        $log->registrarLog($acao, $movimentacao->getEntity(), $movimentacao->id, $antes, $movimentacao->data());

        //SE ESTOU CRIANDO UM REGISTRO DE FECHAMENTO IMEDIATO (O QUE QUER DIZER QUE NÃO ESTOU CONFIRMANDO UM REGISTRO JÁ ABERTO)        
        if ($status == "F" && !$confirmacao) {
            if ($isSaida) {

                $estoqueSaida = $this->atualizarEstoque($movimentacao, "S");
                if (!$estoqueSaida['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $estoqueSaida['mensagem'];
                    $return['status'] = false;
                    return $return;
                }

                $kardexSaida = $this->registrarKardex($movimentacao, "S");
                if (!$kardexSaida['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $kardexSaida['mensagem'];
                    $return['status'] = false;
                    return $return;
                }
            } elseif ($isEntrada) {
                $estoqueEntrada = $this->atualizarEstoque($movimentacao, "E");
                if (!$estoqueEntrada['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $estoqueEntrada['mensagem'];
                    $return['status'] = false;
                    return $return;
                }

                $kardexEntrada = $this->registrarKardex($movimentacao, "E");
                if (!$kardexEntrada['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $kardexEntrada['mensagem'];
                    $return['status'] = false;
                    return $return;
                }
            } else {

                $estoqueSaida = $this->atualizarEstoque($movimentacao, "S");
                $estoqueEntrada = $this->atualizarEstoque($movimentacao, "E");
                if (!$estoqueSaida['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $estoqueSaida['mensagem'];
                    $return['status'] = false;
                    return $return;
                }
                if (!$estoqueEntrada['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $estoqueEntrada['mensagem'];
                    $return['status'] = false;
                    return $return;
                }

                $kardexSaida = $this->registrarKardex($movimentacao, "S");
                $kardexEntrada = $this->registrarKardex($movimentacao, "E");
                if (!$kardexSaida['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $kardexSaida['mensagem'];
                    $return['status'] = false;
                    return $return;
                }
                if (!$kardexEntrada['status']) {
                    $movimentacao->rollback();
                    $return['message'] = $kardexEntrada['mensagem'];
                    $return['status'] = false;
                    return $return;
                }
            }
            $return['solicitacao'] = false;
        } else if ($status == "F" && $confirmacao) {
            $estoqueEntrada = $this->atualizarEstoque($movimentacao, "E");
            if (!$estoqueEntrada['status']) {
                $movimentacao->rollback();
                $return['message'] = $estoqueEntrada['mensagem'];
                $return['status'] = false;
                return $return;
            }

            $kardexEntrada = $this->registrarKardex($movimentacao, "E");
            if (!$kardexEntrada['status']) {
                $movimentacao->rollback();
                $return['message'] = $kardexEntrada['mensagem'];
                $return['status'] = false;
                return $return;
            }

            $return['solicitacao'] = true;
        } else {

            $estoqueSaida = $this->atualizarEstoque($movimentacao, "S");
            if (!$estoqueSaida['status']) {
                $movimentacao->rollback();
                $return['message'] = $estoqueSaida['mensagem'];
                $return['status'] = false;
                return $return;
            }

            $kardexSaida = $this->registrarKardex($movimentacao, "S");
            if (!$kardexSaida['status']) {
                $movimentacao->rollback();
                $return['message'] = $kardexSaida['mensagem'];
                $return['status'] = false;
                return $return;
            }

            $return['solicitacao'] = true;
        }

        $movimentacao->commit();

        $return['status'] = true;
        return $return;
    }

    /**
     * Essa função será chamada para registrar uma solicitação de movimentação (status "A") ou para registrar uma movimentação imediata (status "F")
     * ou para confirmar uma movimentação pendente (solicitação) quando o parâmetro de confirmação estiver marcado
     * @param mixed $data
     * @return void
     */
    public function solicitarMov($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        //se id_mov estiver setado, é porque está confirmando uma movimentação pendente (solicitação)
        //se não estiver setado, é porque está criando uma nova movimentação (solicitação)
        if (isset($data['id_mov']) && !empty($data['id_mov'])) {
            $salvar = $this->submitForm($data, "F");
        } else {
            $verifica = $this->verificaForm($data);
            if (!$verifica['status']) {
                $json['message'] = $this->message->warning($verifica['message'])->render();
                echo json_encode($json);
                return;
            }

            //Se for somente entrada ou somente saída, a movimentação será finalizada imediatamente (status "F")
            $isEntrada = isset($data['entrada']);
            $isSaida = isset($data['saida']);

            if ($isEntrada || $isSaida) {
                $salvar = $this->submitForm($data, "F");
            } else {
                $salvar = $this->submitForm($data, "A");
            }
        }



        if (!$salvar['status']) {
            $json['message'] = $this->message->error($salvar['message'])->render();
            echo json_encode($json);
            return;
        }

        if ($salvar['solicitacao']) {
            if (isset($data['id_mov']) && !empty($data['id_mov'])) {
                $this->message->success("Movimentação confirmada com sucesso!")->flash();
            } else {
                $this->message->success("Solicitação de movimentação registrada com sucesso!")->flash();
            }
        } else {
            $this->message->success("Movimentação registrada com sucesso!")->flash();
        }
        echo json_encode([
            "reload" => true
        ]);
    }

    public function cancelarMov($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (!isset($data['id']) || !ll_intValida($data['id'])) {
            $json['message'] = $this->message->error("Movimentação não encontrada.")->render();
            echo json_encode($json);
            return;
        }

        $movimentacao = (new EqpMov())->findById($data['id']);
        $antes = clone $movimentacao->data();
        $acao = "U";

        if (!$movimentacao) {
            $json['message'] = $this->message->error("Movimentação não encontrada.")->render();
            echo json_encode($json);
            return;
        }

        $movimentacao->beginTransaction();
        // Cancelar movimentação
        $movimentacao->status = 'C';
        if (!$movimentacao->save()) {
            $json['message'] = $this->message->error("Erro ao cancelar a movimentação: " . $movimentacao->fail()->getMessage())->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $movimentacao->getEntity(), $movimentacao->id, $antes, $movimentacao->data());

        $estoqueEntrada = $this->atualizarEstoque($movimentacao, "C");
        if (!$estoqueEntrada['status']) {
            $movimentacao->rollback();
            $return['message'] = $estoqueEntrada['mensagem'];
            $return['status'] = false;
            return $return;
        }

        $kardexEntrada = $this->registrarKardex($movimentacao, "C");
        if (!$kardexEntrada['status']) {
            $movimentacao->rollback();
            $return['message'] = $kardexEntrada['mensagem'];
            $return['status'] = false;
            return $return;
        }

        $movimentacao->commit();

        $this->message->success("Movimentação cancelada com sucesso!")->flash();
        echo json_encode([
            "reload" => true
        ]);
    }

    /**
     * ESSA FUNÇÃO SÓ SERÁ CHAMADA PARA REGISTRAR UMA MOVIMENTAÇÃO IMEDIATA (ENTRADA OU SAÍDA) QUANDO O PARAMETRO DE CONFIRMAÇÃO ESTIVER DESMARCADO
     * OU CONFIRMAR UMA MOVIMENTAÇÃO PENDENTE (SOLICITAÇÃO) QUANDO O PARAMETRO DE CONFIRMAÇÃO ESTIVER MARCADO     * 
     * @param mixed $data
     * @return void
     */
    public function salvarMov($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        //verificaForm vai validar os campos obrigatórios e quanto a questão de entrada e saida
        //"somente entrada" pode estar marcado, ou "somente saída" pode estar marcado
        //pro caso de entrada e saída instantânea (tranferência) ambos estarão desmarcados        
        $verifica = $this->verificaForm($data);
        if (!$verifica['status']) {
            $json['message'] = $this->message->warning($verifica['message'])->render();
            echo json_encode($json);
            return;
        }

        //***COLOCAR F NO SEGUNDO PARAMETRO PRA FINALIZAR A MOVIMENTAÇÃO INSTANTANEAMENTE */
        //deve sempre ser F, pois essa função é chamada para registrar uma movimentação imediata (entrada ou saída)
        //ou para confirmar uma movimentação pendente (solicitação) quando o parâmetro de confirmação estiver marcado
        $salvar = $this->submitForm($data, "F");

        if (!$salvar['status']) {
            $json['message'] = $this->message->error($salvar['message'])->render();
            echo json_encode($json);
            return;
        }

        $this->message->success("Movimentação registrada com sucesso!")->flash();
        echo json_encode([
            "reload" => true
        ]);
    }

    /**
     * @param \Source\Models\EqpMov $mov
     * @param string $status = "E" (Entrada), "S" (Saída), "C" (Cancelamento)
     * @return array{mensagem: string, status: bool}
     */
    private function atualizarEstoque(EqpMov $mov, $status)
    {
        $id_emp2 = $mov->id_emp2;
        $id_user = $mov->id_users;
        $id_equipamento = $mov->id_equipamento;
        $qtde = $mov->qtde;

        // SE FOR SAÍDA OU TRANSFERÊNCIA, SUBTRAI DO LOCAL DE ORIGEM
        if ($mov->id_local_origem && $status == "S") {
            $estoqueOrigem = (new EqpEstoque())->find(
                "id_equipamento = :eq AND id_local = :loc",
                "eq={$id_equipamento}&loc={$mov->id_local_origem}"
            )->fetch();

            $antes = clone $estoqueOrigem->data();
            $acao = "U";

            if (!$estoqueOrigem) {
                return [
                    'status' => false,
                    'mensagem' => 'Não existe estoque cadastrado para este equipamento no local de origem.'
                ];
            }

            if ($estoqueOrigem->qtde < $qtde) {
                return [
                    'status' => false,
                    'mensagem' => "Estoque insuficiente no local de origem. Disponível: {$estoqueOrigem->qtde}, Solicitado: {$qtde}."
                ];
            }

            $estoqueOrigem->qtde -= $qtde;

            if (!$estoqueOrigem->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao atualizar estoque do local de origem.'
                ];
            }

            $log = new Log();
            $log->registrarLog($acao, $estoqueOrigem->getEntity(), $estoqueOrigem->id, $antes, $estoqueOrigem->data());
        }

        // SE FOR ENTRADA OU TRANSFERÊNCIA, SOMA NO LOCAL DE DESTINO
        if ($mov->id_local_destino && $status == "E") {
            $estoqueDestino = (new EqpEstoque())->find(
                "id_equipamento = :eq AND id_local = :loc",
                "eq={$id_equipamento}&loc={$mov->id_local_destino}"
            )->fetch();

            if ($estoqueDestino) {
                $antes = clone $estoqueDestino->data();
                $acao = "U";
                $estoqueDestino->qtde += $qtde;
                if (!$estoqueDestino->save()) {
                    return [
                        'status' => false,
                        'mensagem' => 'Erro ao atualizar estoque do local de destino.'
                    ];
                }
                $log = new Log();
                $log->registrarLog($acao, $estoqueDestino->getEntity(), $estoqueDestino->id, $antes, $estoqueDestino->data());
            } else {
                // Cria novo registro apenas para entrada
                $novo = new EqpEstoque();
                $novo->id_emp2 = $id_emp2;
                $novo->id_users = $id_user;
                $novo->id_equipamento = $id_equipamento;
                $novo->id_local = $mov->id_local_destino;
                $novo->qtde = $qtde;
                if (!$novo->save()) {
                    return [
                        'status' => false,
                        'mensagem' => 'Erro ao criar estoque no local de destino.'
                    ];
                }

                $log = new Log();
                $log->registrarLog("C", $novo->getEntity(), $novo->id, null, $novo->data());
            }
        }

        // SE FOR CANCELAMENTO, RETORNA PRO LOCAL DE ORIGEM
        if ($status == "C") {
            $estoqueOrigem = (new EqpEstoque())->find(
                "id_equipamento = :eq AND id_local = :loc",
                "eq={$id_equipamento}&loc={$mov->id_local_origem}"
            )->fetch();

            $antes = clone $estoqueOrigem->data();
            $acao = "U";

            $estoqueOrigem->qtde += $qtde;

            if (!$estoqueOrigem->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao atualizar estoque do local de origem.'
                ];
            }

            $log = new Log();
            $log->registrarLog($acao, $estoqueOrigem->getEntity(), $estoqueOrigem->id, $antes, $estoqueOrigem->data());
        }

        return [
            'status' => true,
            'mensagem' => 'Estoque atualizado com sucesso.'
        ];
    }

    /**     
     * @param \Source\Models\EqpMov $mov
     * @param string $status = "S" (Saída), "E" (Entrada), "C" (Cancelamento)
     * @return array{mensagem: string, status: bool}
     */
    private function registrarKardex(EqpMov $mov, $status)
    {
        $id_empresa = $mov->id_emp2;
        $id_user = $mov->id_users;
        $id_equip = $mov->id_equipamento;
        $qtde = (float) $mov->qtde;
        $data = date('Y-m-d H:i:s');

        // Buscar último saldo total do equipamento (independente do local)
        $ultimoSaldoTotal = $this->getUltimoSaldoTotal($id_equip);

        // Registrar saída, se houver local de origem
        if ($mov->id_local_origem && $status == "S") {
            $saida = $qtde;
            $entrada = 0;
            $novoSaldoTotal = $ultimoSaldoTotal - $saida;

            $kardexOrigem = new EqpKardex();
            $kardexOrigem->id_emp2 = $id_empresa;
            $kardexOrigem->id_mov = $mov->id;
            $kardexOrigem->data_hora = $data;
            $kardexOrigem->id_usuario = !empty($mov->id_user_origem) ? $mov->id_user_origem : null;
            $kardexOrigem->id_equipamento = $id_equip;
            $kardexOrigem->id_local = $mov->id_local_origem;
            $kardexOrigem->entrada = $entrada;
            $kardexOrigem->saida = $saida;
            $kardexOrigem->saldo = $novoSaldoTotal; // Saldo total do equipamento
            $kardexOrigem->id_users = $id_user;

            if (!$kardexOrigem->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao registrar saída no kardex do local de origem.' . $kardexOrigem->fail()->getMessage()
                ];
            }

            $log = new Log();
            $log->registrarLog("C", $kardexOrigem->getEntity(), $kardexOrigem->id, null, $kardexOrigem->data());

            // Atualizar o último saldo total para o próximo cálculo
            $ultimoSaldoTotal = $novoSaldoTotal;
        }

        // Registrar entrada, se houver local de destino
        if ($mov->id_local_destino && $status == "E") {
            $entrada = $qtde;
            $saida = 0;
            $novoSaldoTotal = $ultimoSaldoTotal + $entrada;

            $kardexDestino = new EqpKardex();
            $kardexDestino->id_emp2 = $id_empresa;
            $kardexDestino->id_mov = $mov->id;
            $kardexDestino->data_hora = $data;
            $kardexDestino->id_usuario = !empty($mov->id_user_destino) ? $mov->id_user_destino : null;
            $kardexDestino->id_equipamento = $id_equip;
            $kardexDestino->id_local = $mov->id_local_destino;
            $kardexDestino->entrada = $entrada;
            $kardexDestino->saida = $saida;
            $kardexDestino->saldo = $novoSaldoTotal; // Saldo total do equipamento
            $kardexDestino->id_users = $id_user;

            if (!$kardexDestino->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao registrar entrada no kardex do local de destino.'
                ];
            }

            $log = new Log();
            $log->registrarLog("C", $kardexDestino->getEntity(), $kardexDestino->id, null, $kardexDestino->data());
        }

        // Registrar estorno em caso de cancelamento
        if ($mov->id_local_destino && $status == "C") {
            $entrada = $qtde;
            $saida = 0;
            $novoSaldoTotal = $ultimoSaldoTotal + $entrada;

            $kardexRetorno = new EqpKardex();
            $kardexRetorno->id_emp2 = $id_empresa;
            $kardexRetorno->id_mov = $mov->id;
            $kardexRetorno->data_hora = $data;
            $kardexRetorno->id_usuario = !empty($mov->id_user_origem) ? $mov->id_user_origem : null;
            $kardexRetorno->id_equipamento = $id_equip;
            $kardexRetorno->id_local = $mov->id_local_origem;
            $kardexRetorno->entrada = $entrada;
            $kardexRetorno->saida = $saida;
            $kardexRetorno->saldo = $novoSaldoTotal; // Saldo total do equipamento
            $kardexRetorno->id_users = $id_user;

            if (!$kardexRetorno->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao registrar entrada no kardex do local de destino.'
                ];
            }

            $log = new Log();
            $log->registrarLog("C", $kardexRetorno->getEntity(), $kardexRetorno->id, null, $kardexRetorno->data());
        }

        // Atualizar quantidade total na tabela equipamentos (deve ser igual ao último saldo do kardex)
        $equipamento = (new Equipamentos())->findById($id_equip);
        $antes = clone $equipamento->data();
        $acao = "U";

        if (!$equipamento) {
            return [
                'status' => false,
                'mensagem' => 'Equipamento não encontrado para atualização da quantidade total.'
            ];
        }

        $equipamento->qtde = $this->getUltimoSaldoTotal($id_equip);
        if (!$equipamento->save()) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao atualizar quantidade total do equipamento.'
            ];
        }

        $log = new Log();
        $log->registrarLog($acao, $equipamento->getEntity(), $equipamento->id, $antes, $equipamento->data());

        return [
            'status' => true,
            'mensagem' => 'Kardex registrado com sucesso.'
        ];
    }

    private function getUltimoSaldoTotal(int $idEquip): float
    {
        $ultimoKardex = (new EqpKardex())->find(
            "id_equipamento = :eq",
            "eq={$idEquip}",
            "saldo"
        )->order("id DESC")->limit(1)->fetch();

        return $ultimoKardex ? (float)$ultimoKardex->saldo : 0;
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
