<?php

namespace Source\Controllers;

use DateTime;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Obras;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Servico;
use Source\Models\Status;

class RelatoriosController extends Controller
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

        $front = [
            "titulo" => "Relatórios - Taskforce",
            "user" => $this->user,
            "secTit" => "Relatórios"

        ];

        echo $this->view->render("tcsistemas.os/relatorios/relatorio-index", [
            "front" => $front

        ]);
    }

    public function medicaoRel($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $dataInicio = null;
            $dataFim = null;

            if ($data['data-inicio'] != "" || $data['data-fim'] != "") {
                $dataInicio = date("Y-m-d", strtotime($data['data-inicio']));
                $dataFim = date("Y-m-d", strtotime($data['data-fim']));

                if ($dataInicio > $dataFim) {
                    $json['message'] = $this->message->error("Data de início não pode ser maior que a data final")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if (!$data['filtrar-por']) {
                $json['message'] = $this->message->error("Selecione um filtro para continuar")->render();
                echo json_encode($json);
                return;
            }

            $filtro = $data['filtrar-por'];

            $status = (new Status())->find(null, null, "*", false)->fetch(true);
            $statusArr = [];
            foreach ($status as $st) {
                $statusArr[$st->id] = $st->descricao;
            }
            $servicos = (new Servico())->find()->fetch(true);
            $clientes = (new Ent())->find("tipo = 1")->fetch(true);
            $clientesArr = [];
            foreach ($clientes as $cliente) {
                $clientesArr[$cliente->id] = mb_strimwidth($cliente->nome, 0, 50, "...");
            }
            $obras = (new Obras())->find()->fetch(true);
            $obrasArr = [];
            if (!empty($obras)) {
                foreach ($obras as $obra) {
                    $obrasArr[$obra->id] = mb_strimwidth($obra->nome, 0, 50, "...");
                }
            }

            if ($filtro == "os") {
                $json['status'] = "success";
                $json['result'] = $this->filtroOrdem($empresa, $servicos, $statusArr, $clientesArr, $obrasArr, $dataInicio, $dataFim);
                echo json_encode($json);
                return;
            } else if ($filtro == "obra") {
                $selectObra = $data['select-obra'] ?? null;
                $option = $data['opcao'] ?? null;
                $json['status'] = "success";
                $json['result'] = $this->filtroObra($empresa, $selectObra, $option, $dataInicio, $dataFim);
                echo json_encode($json);
                return;
            } else if ($filtro == "funcionario") {
                $selectFuncionario = $data['select-funcionario'] ?? null;
                $json['status'] = "success";
                $json['result'] = $this->filtroFuncionario($empresa, $selectFuncionario, $dataInicio, $dataFim);
                echo json_encode($json);
                return;
            } else if ($filtro == "servico") {
                $filtro = "id_servico";
            } else if ($filtro == "cliente") {
                $selectCliente = $data['select-cliente'] ?? null;
                $json['status'] = "success";
                $json['result'] = $this->filtroCliente($empresa, $selectCliente, $statusArr, $clientesArr, $obrasArr, $dataInicio, $dataFim);
                echo json_encode($json);
                return;
            }

            echo $this->view->render("tcsistemas.os/relatorios/medicao-list", []);
            return;
        }

        $obras = (new Obras())->find()->fetch(true);
        $clientes = (new Ent())->find("tipo = 1")->fetch(true);
        $funcionarios = (new Ent())->find("tipo = 3")->fetch(true);

        $front = [
            "titulo" => "Relatórios - Taskforce",
            "user" => $this->user,
            "secTit" => "Relatórios de Medições",
            "relatorio" => true
        ];

        echo $this->view->render("tcsistemas.os/relatorios/medicao-rel", [
            "front" => $front,
            "obras" => $obras,
            "clientes" => $clientes,
            "funcionarios" => $funcionarios,
            "empresa" => $empresa
        ]);
    }

    private function filtroOrdem($empresa, $servicos, $status, $clientes, $obras, $datai = null, $dataf = null): mixed
    {

        $periodo = "";
        if (!empty($datai) && !empty($dataf)) {
            $periodo = date_fmt($datai, "d/m/Y") . " a " . date_fmt($dataf, "d/m/Y");
            $dadosMedicoes = (new Os2_1())->find("dataf BETWEEN :datai AND :dataf", "datai={$datai}&dataf={$dataf}");
            //consulta para buscar pelo menos uma das datas dentro do intervalo
            //$dadosMedicoes = ((new Os2_1))->find("((datai BETWEEN :datai AND :dataf) OR (dataf BETWEEN :datai AND :dataf) OR (datai <= :datai AND dataf >= :dataf))", "datai={$datai}&dataf={$dataf}");
        } else {
            $dadosMedicoes = ((new Os2_1))->find();
        }

        if (empty($dadosMedicoes)) {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        if ($dadosMedicoes->count() > 0) {
            $medicoes = $dadosMedicoes->order("id_os1")->group("id_os1")->fetch(true);
        }

        if (empty($medicoes)) {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        $os1 = [];
        $dadosFormatados = [];

        $funcionario = (new Ent())->find("tipo = 3")->fetch(true);

        foreach ($medicoes as $medicao) {
            $os1[] = (new Os1())->findById($medicao->id_os1);
        }

        foreach ($os1 as $os) {
            $os2 = (new Os2())->findByIdOs($os->id);

            if (!empty($os2)) {
                $totalTarefas = 0;
                $realizadas = 0;
                foreach ($os2 as $tarefa) {
                    foreach ($servicos as $servico) {
                        if ($servico->id == $tarefa->id_servico && $servico->medicao == "1") {
                            $tarefa->nome = $servico->nome;
                            $tarefa->unidade = $servico->medida;
                            $tarefa->mede = true;
                            $totalTarefas++;
                            $realizadas += (new Os2_1())->find("id_os2 = :id_os2", "id_os2={$tarefa->id}")->count();

                            $medicoes = (new Os2_1())->find("id_os2 = :id_os2", "id_os2={$tarefa->id}")->fetch(true);

                            if (!empty($medicoes)) {
                                foreach ($medicoes as $medicao) {
                                    foreach ($funcionario as $func) {
                                        if ($func->id == $medicao->id_operador) {
                                            $medicao->responsavel = $func->nome;
                                        }
                                    }
                                }
                            }
                            $tarefa->medicoes = $medicoes;
                        }
                    }
                }
            }

            $dadosFormatados[] = [
                "id" => $os->id,
                "status" => $os->id_status,
                "cliente" => $os->id_cli,
                "obra" => $os->id_obras,
                "totalTarefas" => $totalTarefas,
                "realizadas" => $realizadas,
                "tarefas" => $os2
            ];
        }

        return $this->view->render("tcsistemas.os/relatorios/medicao-list", [
            "os1" => $dadosFormatados,
            "status" => $status,
            "clientes" => $clientes,
            "obras" => $obras,
            "empresa" => $empresa,
            "periodo" => $periodo
        ]);
    }

    private function filtroCliente($empresa, $cliente, $status, $clientes, $obras, $datai = null, $dataf = null)
    {

        if ($cliente != "0") {
            $ordensComMedicoes = [];
            $os1 = (new Os1())->find("id_cli = :id_cli", "id_cli={$cliente}")->fetch(true);
            $idsDasOrdesDoCliente = [];
            if (!empty($os1)) {
                foreach ($os1 as $os) {
                    $idsDasOrdesDoCliente[] = $os->id;
                }

                $verificaMedicoes = (new Os2_1())->find(
                    "id_os1 IN (" . implode(",", $idsDasOrdesDoCliente) . ")"
                )->fetch(true);

                if (!empty($verificaMedicoes)) {
                    $ordensComMedicoes = $idsDasOrdesDoCliente;
                }
            }

            if (empty($ordensComMedicoes)) {
                $return = "<p>Nenhum resultado encontrado.</p>";
                return $return;
            }
        }

        if (!empty($ordensComMedicoes)) {
            if (!empty($datai) && !empty($dataf)) {
                $dadosMedicoes = ((new Os2_1))->find(
                    "(dataf BETWEEN :datai AND :dataf) AND id_os1 IN (" . implode(",", $ordensComMedicoes) . ")",
                    "datai={$datai}&dataf={$dataf}"
                )->fetch(true);
            } else {
                $dadosMedicoes = ((new Os2_1))->find("id_os1 IN (" . implode(",", $ordensComMedicoes) . ")")->fetch(true);
            }
        } else if (!empty($datai) && !empty($dataf)) {
            $dadosMedicoes = ((new Os2_1))->find(
                "dataf BETWEEN :datai AND :dataf",
                "datai={$datai}&dataf={$dataf}"
            )->fetch(true);
        } else {
            $dadosMedicoes = ((new Os2_1))->find()->fetch(true);
        }

        if (empty($dadosMedicoes)) {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        if (!empty($dadosMedicoes)) {
            foreach ($dadosMedicoes as $medicao) {
                $os2 = (new Os2())->findById($medicao->id_os2);
                $os1 = (new Os1())->findById($medicao->id_os1);
                $cliente = (new Ent())->findById($os1->id_cli);
                $medicao->cliente = $cliente->nome;
                $medicao->obra = !empty($os1->id_obras) ? (new Obras())->findById($os1->id_obras)->nome : "";
                $medicao->tarefa = (new Servico())->findById($os2->id_servico)->nome;
                $medicao->medida = (new Servico())->findById($os2->id_servico)->medida;
                $medicao->inicio = date("d/m/Y", strtotime($medicao->datai));
                $medicao->fim = date("d/m/Y", strtotime($medicao->dataf));
                $medicao->responsavel = !empty($medicao->id_operador) ? (new Ent())->findById($medicao->id_operador)->nome : "";
            }
        } else {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        return $this->view->render("tcsistemas.os/relatorios/medicao-list2", [
            "dados" => $dadosMedicoes,
            "status" => $status,
            "clientes" => $clientes,
            "obras" => $obras,
            "empresa" => $empresa
        ]);
    }

    private function filtroObra($empresa, $obra, $option, $datai = null, $dataf = null)
    {
        $os1 = (new Os1())->find()->fetch(true);

        $ordensComObras = [];

        if ($obra == "0") {
            foreach ($os1 as $os) {
                if (!empty($os->id_obras)) {
                    $ordensComObras[] = $os->id;
                }
            }
        } else {
            foreach ($os1 as $os) {
                if ($os->id_obras == $obra) {
                    $ordensComObras[] = $os->id;
                }
            }
        }

        if (!empty($ordensComObras)) {
            if (!empty($datai) && !empty($dataf)) {
                $dadosMedicoes = ((new Os2_1))->find(
                    "dataf BETWEEN :datai AND :dataf AND id_os1 IN (" . implode(",", $ordensComObras) . ")",
                    "datai={$datai}&dataf={$dataf}"
                )->fetch(true);
            } else {
                $dadosMedicoes = ((new Os2_1))->find("id_os1 IN (" . implode(",", $ordensComObras) . ")")->fetch(true);
            }
        } else {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        $resultado = [];

        if (empty($dadosMedicoes)) {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }
        foreach ($dadosMedicoes as $medicao) {
            $os2 = (new Os2())->findById($medicao->id_os2);
            $os1 = (new Os1())->findById($medicao->id_os1);
            $obraInfo = (new Obras())->findById($os1->id_obras);
            $cliente = (new Ent())->findById($os1->id_cli);
            $servico = (new Servico())->findById($os2->id_servico);
            $responsavel = (new Ent())->findById($medicao->id_operador);

            $obraId = $os1->id_obras;
            $tarefaId = $os2->id_servico;

            // Organiza as obras
            if (!isset($resultado[$obraId])) {
                $resultado[$obraId] = [
                    "obra" => $obraInfo->nome,
                    "endereco" => $obraInfo->endereco,
                    "tarefas" => [],
                    "datai" => $datai,
                    "dataf" => $dataf
                ];
            }

            // Organiza as tarefas dentro da obra
            if (!isset($resultado[$obraId]["tarefas"][$tarefaId])) {
                $resultado[$obraId]["tarefas"][$tarefaId] = [
                    "tarefa" => $servico->nome,
                    "medida" => $servico->medida,
                    "medicoes" => []
                ];
            }

            // Adiciona a medição
            $resultado[$obraId]["tarefas"][$tarefaId]["medicoes"][] = [
                "os" => $os1->id,
                "cliente" => $cliente->nome,
                "responsavel" => $responsavel->nome,
                "data_inicio" => $medicao->datai,
                "data_fim" => $medicao->dataf,
                "qtde" => $medicao->qtde,
                "obs" => $medicao->obs
            ];
        }

        if ($option == "obra") {
            return $this->view->render("tcsistemas.os/relatorios/medicao-list4", [
                "dados" => $resultado,
                "empresa" => $empresa
            ]);
        } else if ($option == "pdf") {
            return $resultado;
        } else {
            return $this->view->render("tcsistemas.os/relatorios/medicao-list3", [
                "dados" => $resultado,
                "empresa" => $empresa
            ]);
        }
    }

    private function filtroFuncionario($empresa, $funcionario, $datai = null, $dataf = null, $tipo = null)
    {
        $query = [];
        $params = [];

        if ($funcionario != "0") {
            $query[] = "id_operador = :id_operador";
            $params["id_operador"] = $funcionario;
        }

        if (!empty($datai) && !empty($dataf)) {
            $query[] = "dataf BETWEEN :datai AND :dataf";
            $params["datai"] = $datai;
            $params["dataf"] = $dataf;
        }


        if (!empty($query)) {
            $params = http_build_query($params);
            $medicoes = (new Os2_1())->find(implode(" AND ", $query), $params)->fetch(true);
        } else {
            $medicoes = (new Os2_1())->find()->fetch(true);
        }

        if (empty($medicoes)) {
            $return = "<p>Nenhum resultado encontrado.</p>";
            return $return;
        }

        foreach ($medicoes as $medicao) {
            $os2 = (new Os2())->findById($medicao->id_os2);
            $os1 = (new Os1())->findById($medicao->id_os1);
            $obra = null;
            if (!empty($os1->id_obras)) {
                $obra = (new Obras())->findById($os1->id_obras);
            }

            $medicao->cliente = (new Ent())->findById($os1->id_cli)->nome;
            $medicao->obra = !empty($obra) ? $obra->nome : "";
            $medicao->tarefa = (new Servico())->findById($os2->id_servico)->nome;
            $medicao->medida = (new Servico())->findById($os2->id_servico)->medida;
            $medicao->unitario = (new Servico())->findById($os2->id_servico)->custo;
            $medicao->total = $medicao->qtde * $medicao->unitario;
            $medicao->inicio = date("d/m/Y", strtotime($medicao->datai));
            $medicao->fim = date("d/m/Y", strtotime($medicao->dataf));
            $medicao->responsavel = (new Ent())->findById($medicao->id_operador)->nome;
        }

        if ($tipo == "pdf") {
            return $medicoes;
        } else {
            return $this->view->render("tcsistemas.os/relatorios/medicao-list5", [
                "dados" => $medicoes,
                "filtro" => $funcionario,
                "datai" => $datai,
                "dataf" => $dataf,
                "empresa" => $empresa
            ]);
        }
    }


    public function pdfFuncionario($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);
        $funcionario = ll_decode($data['id']);

        $datai = $data['datai'] ?? null;
        $dataf = $data['dataf'] ?? null;

        $funcionarios = $this->filtroFuncionario($empresa, $funcionario, $datai, $dataf, "pdf");

        $operadores = [];

        foreach ($funcionarios as $item) {
            $operadores[] = $item->data->id_operador;
        }

        // Remover duplicatas
        $operadores = array_unique($operadores);

        // Ordenar os valores
        sort($operadores);

        $ent_fun = (new Ent())->find("id IN (" . implode(",", $operadores) . ")")->fetch(true);

        usort($funcionarios, function ($a, $b) {
            $dataA = DateTime::createFromFormat('d/m/Y', $a->data->inicio);
            $dataB = DateTime::createFromFormat('d/m/Y', $b->data->inicio);

            return $dataA <=> $dataB;
        });


        $html = $this->view->render("tcsistemas.os/relatorios/medicao-pdf5", ["dados" => $funcionarios, "emp" => $empresa, "func" => $ent_fun]);

        $textoRodape = "Relatório gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //echo $html;
        ll_pdfGerar($html, "relatorio-medicao", "R", "P", "", $textoRodape);
    }

    public function medicaoPdf($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);
        $logo = url("storage/uploads/" . $empresa->logo);
        $servicos = (new Servico())->find()->fetch(true);
        $os1 = (new Os1())->findById(ll_decode($data['id']));
        $obras = (new Obras())->find()->fetch(true);

        if (!empty($obras)) {
            foreach ($obras as $obra) {
                if ($obra->id == $os1->id_obras) {
                    $os1->obra = $obra->nome;
                }
            }
        }

        $os2 = (new Os2())->findByIdOs(ll_decode($data['id']));

        foreach ($os2 as $tarefa) {
            foreach ($servicos as $servico) {
                if ($servico->id == $tarefa->id_servico && $servico->medicao == "1") {
                    $tarefa->nome = $servico->nome;
                    $tarefa->unidade = $servico->medida;
                    $tarefa->mede = true;

                    $tarefa->inicio = strtotime($tarefa->dataexec) + $tarefa->horaexec;
                    $tarefa->fim = $tarefa->inicio + $tarefa->tempo;

                    $medicoes = (new Os2_1())->find("id_os2 = :id_os2", "id_os2={$tarefa->id}")->fetch(true);

                    $tarefa->valorMedido = 0;
                    $tarefa->valorPendente = $tarefa->qtde;

                    if (!empty($medicoes)) {
                        foreach ($medicoes as $medicao) {
                            $tarefa->valorMedido += $medicao->qtde;
                            $tarefa->valorPendente = $tarefa->qtde - $tarefa->valorMedido;
                        }
                        $tarefa->medicoes = $medicoes;
                    }
                }
            }
        }
        $html = $this->view->render("tcsistemas.os/relatorios/medicao-pdf", ["os1" => $os1, "os2" => $os2, "emp" => $empresa]);

        $textoRodape = "Relatório gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //echo $html;
        ll_pdfGerar($html, "relatorio-medicao", "R", "P", "", $textoRodape);
    }

    public function obraPdfMedicao($data): void
    {

        $data['tema'] = filter_input(INPUT_GET, 'tema', FILTER_SANITIZE_STRING);

        $tema = $data['tema'] ?? ""; // aqui estou recebendo o tema pra imprimir na capa do pdf

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa); //dados da empresa pra alimentar o cabeçalho do pdf
        $logo = url("storage/uploads/" . $empresa->logo); //logo da empresa pro pdf

        $obra = (new Obras())->findById(ll_decode($data['id'])); //cadastro da obra

        $temPeriodo = false;
        if (isset($data['datai']) && isset($data['dataf'])) {
            $os21 = (new Os2_1())->find("dataf BETWEEN :datai AND :dataf", "datai={$data['datai']}&dataf={$data['dataf']}")->fetch(true);
            $temPeriodo = true;
        } else {
            $os21 = (new Os2_1())->find(null, null, "id_os1")->group("id_os1")->fetch(true); //busco todas as medicões
        }

        $ids = array_map(function ($item) {
            return $item->id_os1;
        }, $os21); // pego os ids das ordens de serviço que contém medições feitas da obra específica        

        $os1 = (new Os1())->find("id in (" . implode(",", $ids) . ") and id_obras = :id_obras", "id_obras={$obra->id}")->fetch(true); //aqui estou pegando todas as ordens, independete da data       

        foreach ($os1 as $os) {
            $os2 = (new Os2())->findByIdOs($os->id);

            if (!empty($os2)) {

                foreach ($os2 as $tarefa) {
                    $tarefa->tarefa = (new Servico())->findById($tarefa->id_servico)->nome;
                    $tarefa->medida = (new Servico())->findById($tarefa->id_servico)->medida;
                    $tarefa->mede = (new Servico())->findById($tarefa->id_servico)->medicao;
                    $tarefa->inicio = strtotime($tarefa->dataexec) + $tarefa->horaexec;
                    $tarefa->fim = $tarefa->inicio + $tarefa->tempo;

                    if ($temPeriodo) {
                        $medicoes = (new Os2_1())->find("id_os2 = :id_os2 AND dataf BETWEEN :datai AND :dataf", "id_os2={$tarefa->id}&datai={$data['datai']}&dataf={$data['dataf']}")->fetch(true);
                    } else {
                        $medicoes = (new Os2_1())->find("id_os2 = :id_os2", "id_os2={$tarefa->id}")->fetch(true);
                    }

                    $tarefa->valorMedido = 0;
                    $tarefa->valorPendente = $tarefa->qtde;

                    if (!empty($medicoes)) {
                        foreach ($medicoes as $medicao) {
                            $tarefa->valorMedido += $medicao->qtde;
                            $tarefa->valorPendente = $tarefa->qtde - $tarefa->valorMedido;
                        }
                    }
                    $tarefa->medicoes = $medicoes;
                }
            }

            $os->tarefas = $os2;
        }

        $obra->cliente = (new Ent())->findById($obra->id_ent_cli)->nome;

        $html = $this->view->render("tcsistemas.os/relatorios/medicao-pdf4", [
            "obra" => $obra,
            "os1" => $os1,
            "emp" => $empresa,
            "logo" => $logo,
            "tema" => $tema
        ]);

        $textoRodape = "Relatório gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //echo $html;
        ll_pdfGerar($html, "relatorio-medicao", "R", "P", "", $textoRodape);
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
