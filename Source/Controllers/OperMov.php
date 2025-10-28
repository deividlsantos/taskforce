<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\EqpEstoque;
use Source\Models\EqpKardex;
use Source\Models\EqpLocal;
use Source\Models\EqpMov;
use Source\Models\Equipamentos;
use Source\Models\Users;

class OperMov extends OperController
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

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $solRecebidas = (new EqpMov())->find(
            "id_user_destino = :id_user_destino AND status = :status",
            "id_user_destino={$id_user}&status=A"
        )->fetch(true);
        $solEnviadas = (new EqpMov())->find(
            "id_user_origem = :id_user_origem AND status = :status",
            "id_user_origem={$id_user}&status=A"
        )->fetch(true);

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

            return $solicitacoes;
        };

        $equipamentos = (new Equipamentos())->find(
            "inventario = :inventario AND ativo = :ativo",
            "inventario=1&ativo=A"
        )->fetch(true);

        $usuarios = (new Users())->find()->fetch(true);
        $locais = (new EqpLocal())->find()->fetch(true);
        $estoque = (new EqpEstoque())->find()->fetch(true);


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
            "titulo" => "Mov. Ferramentas - Taskforce",
            "user" => $this->user,
            "nav" => "Mov. Ferramentas",
            "navback" => "oper_dash",
            "navlink" => "oper_mov"
        ];

        echo $this->view->render("mov/mov", [
            "front" => $front,
            "solRecebidas" => $formatar($solRecebidas),
            "solEnviadas" => $formatar($solEnviadas),
            "equipamentos" => $equipamentos,
            "usuarios" => $usuarios,
            "locais" => $locais,
            "estoque" => $estoque
        ]);
    }

    public function verificarEstoque($data)
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

    public function solicitarMov($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (isset($data['id_mov']) && !empty($data['id_mov'])) {
            $salvar = $this->submitForm($data, "F");
        } else {
            if (empty($data['id_equipamento'])) {
                $json['message'] = "Selecione a ferramenta.";
                $json['status'] = false;
                echo json_encode($json);
                return;
            }

            if (empty($data['qtde'])) {
                $json['message'] = "Informe a quantidade.";
                $json['status'] = false;
                echo json_encode($json);
                return;
            }

            if (empty($data['id_local_destino'])) {
                $json['message'] = "Informe o local de destino.";
                $json['status'] = false;
                echo json_encode($json);
                return;
            }

            $salvar = $this->submitForm($data, "A");
        }

        echo json_encode($salvar);
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
        
        echo json_encode([
            "reload" => true,
            "message" => "Movimentação cancelada com sucesso!"
        ]);
    }

    private function submitForm($data, $status = "F")
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

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
        } else {
            $movimentacao = new EqpMov();

            $movimentacao->id_emp2 = $id_empresa;
            $movimentacao->id_users = $id_user;
            $movimentacao->id_equipamento = $data['id_equipamento'];
            $movimentacao->qtde = $data['qtde'];
            $movimentacao->id_user_origem = $id_user;
            $movimentacao->id_local_origem = !empty($data['id_local_origem']) ? $data['id_local_origem'] : null;
            $movimentacao->id_user_destino = !empty($data['id_usuario_destino']) ? $data['id_usuario_destino'] : null;
            $movimentacao->id_local_destino = !empty($data['id_local_destino']) ? $data['id_local_destino'] : null;
            $movimentacao->obs = !empty($data['observacao']) ? $data['observacao'] : null;
            $movimentacao->status = $status;
        }

        $movimentacao->beginTransaction();

        if (!$movimentacao->save()) {
            $movimentacao->fail();
            $movimentacao->rollback();
            $return['message'] = $movimentacao->fail()->getMessage();
            $return['status'] = false;
            return $return;
        }

        if ($status == "A") {
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
            $return['message'] = "Solicitação registrada com sucesso!";
        } else {
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
            $return['message'] = "Movimentação confirmada com sucesso!";
        }

        $movimentacao->commit();

        $return['reload'] = true;
        return $return;
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
        }

        // SE FOR ENTRADA OU TRANSFERÊNCIA, SOMA NO LOCAL DE DESTINO
        if ($mov->id_local_destino && $status == "E") {
            $estoqueDestino = (new EqpEstoque())->find(
                "id_equipamento = :eq AND id_local = :loc",
                "eq={$id_equipamento}&loc={$mov->id_local_destino}"
            )->fetch();

            if ($estoqueDestino) {
                $estoqueDestino->qtde += $qtde;
                if (!$estoqueDestino->save()) {
                    return [
                        'status' => false,
                        'mensagem' => 'Erro ao atualizar estoque do local de destino.'
                    ];
                }
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
            }
        }

        // SE FOR CANCELAMENTO, RETORNA PRO LOCAL DE ORIGEM
        if ($status == "C") {
            $estoqueOrigem = (new EqpEstoque())->find(
                "id_equipamento = :eq AND id_local = :loc",
                "eq={$id_equipamento}&loc={$mov->id_local_origem}"
            )->fetch();

            $estoqueOrigem->qtde += $qtde;

            if (!$estoqueOrigem->save()) {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao atualizar estoque do local de origem.'
                ];
            }
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
        }

        // Atualizar quantidade total na tabela equipamentos (deve ser igual ao último saldo do kardex)
        $equipamento = (new Equipamentos())->findById($id_equip);
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
