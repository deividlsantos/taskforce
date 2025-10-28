<?php

namespace Source\Controllers;

use DateTime;
use Exception;
use InvalidArgumentException;
use Source\Models\ChkGrupo;
use Source\Models\ChkItem;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Equipamentos;
use Source\Models\Log;
use Source\Models\Materiais;
use Source\Models\Obras;
use Source\Models\Obs;
use Source\Models\Oper;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Os2_2;
use Source\Models\Os2_2_1;
use Source\Models\Os3;
use Source\Models\Os5;
use Source\Models\Plconta;
use Source\Models\Rec;
use Source\Models\Recorrencias;
use Source\Models\RecorrenciasCliServ;
use Source\Models\RecSaldo;
use Source\Models\Servico;
use Source\Models\Status;
use Source\Models\Tipo;

class OsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->os != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    /**
     * TELA PAI
     * titulo - titulo da aba
     * tituloPai - titulo da página maior
     * secTit - titulo da página menor
     * @return void
     */
    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);

        $empresasDoGrupo = null;
        if ($empresa->id_emp1 != 1) {
            $empresasDoGrupo = (new Emp2())->find("id_emp1 = :id_emp1", "id_emp1={$empresa->id_emp1}", "*", false)->fetch(true);
        }

        $page = 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        // Query complexa para ordenação por status e data de execução
        $ordens = (new Os1())->find(
            "os1.id_status IN (2,3,4,8)",
            null,
            "os1.*, 
                CASE 
                    WHEN os1.id_status = 3 THEN 1
                    WHEN os1.id_status = 4 THEN 2  
                    WHEN os1.id_status = 2 THEN 3
                    WHEN os1.id_status = 8 THEN 4
                    ELSE 5
                END as status_order,
                COALESCE(
                    (SELECT MIN(os2.dataexec) FROM os2 WHERE os2.id_os1 = os1.id), 
                    os1.datacad
                ) as data_execucao,
                COALESCE(
                    (SELECT os2.horaexec 
                    FROM os2 
                    WHERE os2.id_os1 = os1.id 
                    AND os2.dataexec = (SELECT MIN(os2_inner.dataexec) FROM os2 os2_inner WHERE os2_inner.id_os1 = os1.id)
                    ORDER BY os2.horaexec ASC 
                    LIMIT 1), 
                    0
                ) as hora_execucao"
        )->order("status_order ASC, data_execucao ASC, os1.id ASC")
            ->limit($limit)
            ->offset($offset)
            ->fetch(true);

        if (!empty($ordens)) {
            foreach ($ordens as $vlr) {
                if ($vlr->concluir == 'S') {
                    $tarefas = (new Os2())->findByIdOs($vlr->id);
                    if (!empty($tarefas)) {
                        foreach ($tarefas as $tarefa) {
                            $statusIncompleto = false;
                            foreach ($tarefas as $tarefa) {
                                if ($tarefa->status != 'C' && $tarefa->status != 'D') {
                                    $statusIncompleto = true;
                                    break;
                                }
                            }

                            if ($statusIncompleto) {
                                $vlr->concluir = 'N';
                                $vlr->save();
                            }
                        }
                    }
                }
            }
        }

        // Contar total de registros para paginação futura
        $totalRegistros = (new Os1())->find("os1.id_status IN (2,3,4,8)")->count();
        $totalPaginas = ceil($totalRegistros / $limit);

        $tipo = (new Tipo())->find()->order('id')->fetch(true);

        $status = (new Status())->find(
            "id_emp2 = :id_emp2",
            "id_emp2=1",
            "*",
            false
        )->fetch(true);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $servico = (new Servico())->find()->fetch(true);
        $operador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=3&status=A"
        )->fetch(true);
        $produto = (new Materiais())->find()->fetch(true);
        $segmentos = (new Obras())->find()->fetch(true);

        $front = [
            "titulo" => "Ordens de Serviço - Taskforce",
            "user" => $this->user,
            "secTit" => "Ordens de Serviço"
        ];

        echo $this->view->render("tcsistemas.os/ordens/ordensList", [
            "front" => $front,
            "ordens" => $ordens,
            "status" => $status,
            "cliente" => $cliente,
            "servico" => $servico,
            "operador" => $operador,
            "produto" => $produto,
            "segmentos" => $segmentos,
            "tipo" => $tipo,
            "user" => $this->user,
            "empresa" => $empresa,
            "empresasDoGrupo" => $empresasDoGrupo,
            "paginacao" => [
                "paginaAtual" => $page,
                "totalPaginas" => $totalPaginas,
                "registrosPorPagina" => $limit,
                "totalRegistros" => $totalRegistros
            ]
        ]);
    }

    public function carregarPagina($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        // Verificar se é requisição POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }


        try {
            $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT) ?: 1;
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT) ?: 50;
            $offset = ($page - 1) * $limit;

            $filtros = $data;

            // Remover dados de paginação dos filtros
            unset($filtros['page'], $filtros['limit']);

            //Contruir query com filtros
            $queryData = $this->construirQueryComFiltros($filtros);

            // Buscar ordens com paginação
            $ordens = (new Os1())->find(
                $queryData['where'],
                $queryData['params'],
                "os1.*,
                CASE 
                    WHEN os1.id_status = 3 THEN 1
                    WHEN os1.id_status = 4 THEN 2  
                    WHEN os1.id_status = 2 THEN 3
                    WHEN os1.id_status = 8 THEN 4
                    ELSE 5
                END as status_order,
                COALESCE(
                    (SELECT MIN(os2.dataexec) FROM os2 WHERE os2.id_os1 = os1.id), 
                    os1.datacad
                ) as data_execucao,
                COALESCE(
                    (SELECT os2.horaexec 
                    FROM os2 
                    WHERE os2.id_os1 = os1.id 
                    AND os2.dataexec = (SELECT MIN(os2_inner.dataexec) FROM os2 os2_inner WHERE os2_inner.id_os1 = os1.id)
                    ORDER BY os2.horaexec ASC 
                    LIMIT 1), 
                    0
                ) as hora_execucao"
            )
                ->order($queryData['order'])
                ->limit($limit)
                ->offset($offset)
                ->fetch(true);

            // Buscar dados complementares (mesmo código da função index)
            $status = (new Status())->find("id_emp2 = :id_emp2", "id_emp2=1", "*", false)->fetch(true);
            $cliente = (new Ent())->find("tipo = :tipo AND status = :status", "tipo=1&status=A")->fetch(true);
            $tipo = (new Tipo())->find()->order('id')->fetch(true);

            // Contar total com filtros
            $totalRegistros = (new Os1())->find($queryData['where'], $queryData['params'])->count();
            $totalPaginas = ceil($totalRegistros / $limit);

            // Gerar HTML da tabela
            $htmlTabela = $this->gerarHtmlTabela($ordens, $status, $cliente, $tipo);

            // Gerar HTML da paginação
            $paginacao = [
                'paginaAtual' => $page,
                'totalPaginas' => $totalPaginas,
                'totalRegistros' => $totalRegistros,
                'registrosPorPagina' => $limit
            ];
            $htmlPaginacao = $this->gerarHtmlPaginacao($paginacao);

            echo json_encode([
                'success' => true,
                'data' => [
                    'ordens' => $htmlTabela,
                    'paginacao' => $htmlPaginacao
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function construirQueryComFiltros($filtros): array
    {
        $where = ["os1.id_status IN (2,3,4,8)"];
        $params = [];
        $order = "status_order ASC, data_execucao ASC, hora_execucao ASC, os1.id ASC";

        // STATUS (checkboxes múltiplos)
        if (!empty($filtros['os-status']) && is_array($filtros['os-status'])) {
            $statusIds = array_map('intval', $filtros['os-status']);
            $where[0] = "os1.id_status IN (" . implode(',', $statusIds) . ")";
        }

        // TIPO
        if (!empty($filtros['os-tipo']) && $filtros['os-tipo'] !== 'selecione') {
            $where[] = "EXISTS (SELECT 1 FROM tipo WHERE tipo.id = os1.id_tipo AND tipo.descricao = :tipo)";
            $params['tipo'] = $filtros['os-tipo'];
        }

        // CLIENTE (quando buscar-por = cliente)
        if (!empty($filtros['os-cli']) && $filtros['os-cli'] !== 'todos') {
            $where[] = "os1.id_cli = :cliente_id";
            $params['cliente_id'] = (int)$filtros['os-cli'];
        }

        // TAREFA/SERVIÇO (quando buscar-por = tarefa)
        if (!empty($filtros['os-tarefa']) && $filtros['os-tarefa'] !== 'todas') {
            $where[] = "EXISTS (SELECT 1 FROM os2 WHERE os2.id_os1 = os1.id AND os2.id_servico = :servico_id)";
            $params['servico_id'] = (int)$filtros['os-tarefa'];
        }

        // OPERADOR (quando buscar-por = operador)
        if (!empty($filtros['os-operador']) && $filtros['os-operador'] !== 'todos') {
            $where[] = "EXISTS (SELECT 1 FROM os2 WHERE os2.id_os1 = os1.id AND os2.id_colaborador = :operador_id)";
            $params['operador_id'] = (int)$filtros['os-operador'];
        }

        // SEGMENTO (quando buscar-por = segmento)
        if (!empty($filtros['os-segmento']) && $filtros['os-segmento'] !== 'todos') {
            // Assumindo que existe uma relação entre OS e segmento
            $where[] = "os1.id_obras = :segmento_id"; // Ajuste conforme sua estrutura
            $params['segmento_id'] = (int)$filtros['os-segmento'];
        }

        // BUSCA GERAL (quando buscar-por = todos)
        if (!empty($filtros['os-busca-geral'])) {
            $busca = '%' . $filtros['os-busca-geral'] . '%';
            $where[] = "(
            os1.id LIKE :busca_geral 
            OR os1.controle LIKE :busca_geral2
            OR EXISTS (
                SELECT 1 FROM ent 
                WHERE ent.id = os1.id_cli 
                AND ent.nome LIKE :busca_geral3
            )
        )";
            $params['busca_geral'] = $busca;
            $params['busca_geral2'] = $busca;
            $params['busca_geral3'] = $busca;
        }

        // PERÍODO
        if (!empty($filtros['data-inicio'])) {
            $where[] = "DATE(COALESCE(
            (SELECT MIN(os2.dataexec) FROM os2 WHERE os2.id_os1 = os1.id), 
            os1.datacad
        )) >= :data_inicio";
            $params['data_inicio'] = $filtros['data-inicio'];
        }

        if (!empty($filtros['data-fim'])) {
            $where[] = "DATE(COALESCE(
            (SELECT MIN(os2.dataexec) FROM os2 WHERE os2.id_os1 = os1.id), 
            os1.datacad
        )) <= :data_fim";
            $params['data_fim'] = $filtros['data-fim'];
        }

        // ORDENAÇÃO
        if (!empty($filtros['ordenar-por'])) {
            $direcao = (!empty($filtros['sort1']) && $filtros['sort1'] === 'desc') ? 'DESC' : 'ASC';

            switch ($filtros['ordenar-por']) {
                case 'os':
                    $order = "os1.id {$direcao}";
                    break;
                case 'controle':
                    $order = "
                        LENGTH(CAST(os1.controle AS UNSIGNED)) {$direcao},
                        CAST(os1.controle AS UNSIGNED) {$direcao}, 
                        os1.controle {$direcao}
                    ";
                    break;
                case 'tipo':
                    $order = "(SELECT tipo.descricao FROM tipo WHERE tipo.id = os1.id_tipo) {$direcao}";
                    break;
                case 'execucao':
                    $order = "data_execucao {$direcao}, hora_execucao {$direcao}";
                    break;
                case 'cliente':
                    $order = "(SELECT ent.nome FROM ent WHERE ent.id = os1.id_cli) {$direcao}";
                    break;
                default:
                    // Mantém ordenação padrão
                    $order = "status_order ASC, data_execucao ASC, hora_execucao ASC, os1.id ASC";
            }
        }

        return [
            'where' => implode(' AND ', $where),
            'params' => http_build_query($params),
            'order' => $order
        ];
    }

    private function gerarHtmlTabela($ordens, $status, $cliente, $tipo): string
    {
        // Capturar o output do template
        ob_start();

        // Renderizar o template da listagem
        echo $this->view->render("tcsistemas.os/ordens/ordensListListagem", [
            "ordens" => $ordens,
            "status" => $status,
            "tipo" => $tipo,
            "cliente" => $cliente,
            "user" => $this->user
        ]);

        $html = ob_get_clean();
        return $html;
    }

    private function gerarHtmlPaginacao($paginacao): string
    {
        // Capturar o output do template
        ob_start();

        // Renderizar o template da paginação
        echo $this->view->render("tcsistemas.os/ordens/ordensListPaginacao", [
            "paginacao" => $paginacao
        ]);

        $html = ob_get_clean();
        return $html;
    }

    private function verificaRecInterno(int $id_os1): array
    {
        $rec = (new Rec())->find("id_os1 = :id_os1", "id_os1={$id_os1}")->fetch(true);

        if (empty($rec)) {
            return ['flagrec' => false, 'documento' => null];
        }

        $ids = array_map(fn($r) => (int) $r->id, $rec);
        $idsList = implode(',', $ids);

        $recSaldo = (new RecSaldo())->find("id_rec IN ($idsList)")->fetch();

        if ($recSaldo) {
            return ['flagrec' => true, 'documento' => $rec[0]->documento];
        }

        return ['flagrec' => false, 'documento' => null];
    }

    // função que responde pro frontend via AJAX
    public function verificaRec($data): void
    {
        $id_os1 = (int) ($data['id'] ?? 0);
        echo json_encode($this->verificaRecInterno($id_os1));
    }

    // função de estorno
    public function estornaOS($data): void
    {
        $id_os1 = (int) ($data['id'] ?? 0);
        $verifica = $this->verificaRecInterno($id_os1);

        if ($verifica['flagrec']) {
            echo json_encode([
                'success' => false,
                'message' => 'Não é possível estornar. Existem receitas baixadas vinculadas a esta OS. Documento: ' . $verifica['documento']
            ]);
            return;
        }

        $os1 = (new Os1())->findById($id_os1);
        $os1->beginTransaction();
        $antes = clone $os1->data();

        $rec = (new Rec())->find("id_os1 = :id_os1", "id_os1={$id_os1}")->fetch(true);

        if (!empty($rec)) {
            foreach ($rec as $r) {
                $recAntes = clone $r->data();
                $r->destroy();
                $log = new Log();
                $log->registrarLog("D", $r->getEntity(), $r->id, $recAntes, null);
            }
        }

        $os1->id_status = 3; // status estornado
        $os1->estornado = 'S';
        $os1->id_users = $this->user->id;

        if (!$os1->save()) {
            $os1->rollback();
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao estornar a OS. Contate o desenvolvedor.'
            ]);
            return;
        }

        $log = new Log();
        $log->registrarLog("U", $os1->getEntity(), $os1->id, $antes, $os1->data());

        $os1->commit();

        $this->message->success("Ordem de Serviço estornada! Novo status 'EM EXECUÇÃO'.")->flash();
        $json['success'] = true;

        echo json_encode($json);
    }

    public function verificaOs($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (isset($data['preosIdCli'])) {
            $idCli = $data['preosIdCli'];
            $ordens = (new Os1())->find(
                "id_cli = :id_cli",
                "id_cli={$idCli}",
                "id"
            )->order('id DESC')->fetch(true);

            $ordensData = [];
            if (!empty($ordens)) {
                $ordensData = objectsToArray($ordens);
                $json['success'] = true;
                $json['ordens'] = $ordensData;
            } else {
                $json['success'] = false;
            }

            echo json_encode($json);
            return;
        }

        if (isset($data['osId'])) {
            $id = (int) $data['osId'];
            $ordens = (new Os1())->find(
                "id = :id",
                "id={$id}"
            )->fetch(true);


            $ordensData = [];
            if (!empty($ordens)) {
                $ordensData = objectsToArray($ordens);
                $json['success'] = true;
                $json['ordens'] = $ordensData;
            } else {
                $json['success'] = false;
            }

            echo json_encode($json);
            return;
        }

        $id = ll_decode($data['id']);

        $os2 = (new Os2())->findByIdOs($id);
        $os3 = (new Os3())->findByIdOs($id);

        $servicos = false;
        $materiais = false;

        if (!empty($os2)) {
            $servicos = true;
        }

        if (!empty($os3)) {
            $materiais = true;
        }

        $json['servicos'] = $servicos;
        $json['materiais'] = $materiais;

        echo json_encode($json);
    }

    public function retornaItens($data): void
    {
        $id = $data['id'];

        $os2 = (new Os2())->findByIdOs($id);
        $os3 = (new Os3())->findByIdOs($id);

        $servicos = [];
        $materiais = [];

        if (!empty($os2)) {
            foreach ($os2 as $o) {
                $o->servico = (new Servico())->findById($o->id_servico)->nome;
                $o->operador = $o->id_colaborador ? (new Ent())->findById($o->id_colaborador)->nome : "N/A";
            }
            $servicos = objectsToArray($os2);
        }

        if (!empty($os3)) {
            foreach ($os3 as $o) {
                $o->material = (new Materiais())->findById($o->id_materiais)->descricao;
            }
            $materiais = objectsToArray($os3);
        }

        if (empty($os2) && empty($os3)) {
            $json['success'] = false;
            echo json_encode($json);
            return;
        }

        $json['success'] = true;
        $json['servicos'] = $servicos;
        $json['materiais'] = $materiais;

        echo json_encode($json);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);
        $label = str_to_single($empresa->labelFiliais);

        $ordens = "";
        $materiaisPorTarefas = "";
        $secTit = "Cadastrar";

        if (isset($data['id_ordens'])) {
            $id = ll_decode($data['id_ordens']);
            $ordens = (new Os1())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $tipo = (new Tipo())->find()->order('id')->fetch(true);

        $operador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=3&status=A"
        )->fetch(true);

        $status = (new Status())->find(
            "id_emp2 = :id_emp2",
            "id_emp2=1",
            "*",
            false
        )->fetch(true);

        $portador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=4&status=A"
        )->fetch(true);

        $plconta = (new Plconta())->find(
            "ativo = :ativo",
            "ativo=1"
        )->fetch(true);

        $operacao = (new Oper())->find()->fetch(true);

        $servico = (new Servico())->find()->fetch(true);
        $material = (new Materiais())->find()->fetch(true);
        $obras = (new Obras())->find()->fetch(true);
        $obs = (new Obs())->find()->order("descricao")->fetch(true);
        $equipamentos = (new Equipamentos())->find()->fetch(true);

        $recorrenciasPadrao = (new Recorrencias())->find("padrao = :padrao", "padrao=1", "*", false)->fetch(true);
        $recorrenciasEmpresa = (new Recorrencias())->find("id_emp2 = :id_emp2 AND padrao = :padrao", "id_emp2={$id_empresa}&padrao=0")->fetch(true);
        $recorrencias = array_merge($recorrenciasPadrao ?? [], $recorrenciasEmpresa ?? []);

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Ordem de Serviço"
        ];

        echo $this->view->render("tcsistemas.os/ordens/ordensCad", [
            "front" => $front,
            "ordens" => $ordens,
            "cliente" => $cliente,
            "tipo" => $tipo,
            "operador" => $operador,
            "status" => $status,
            "servico" => $servico,
            "material" => $material,
            "obras" => $obras,
            "obs" => $obs,
            "label" => $label,
            "recorrencias" => $recorrencias,
            "portador" => $portador,
            "plconta" => $plconta,
            "operacao" => $operacao,
            "empresa" => $empresa,
            "equipamentos" => $equipamentos,
            "user" => $this->user
        ]);
    }

    private function registrarTarefas($data, $id_empresa, $os1)
    {
        $id_user = $this->user->id;

        if (empty($data) || !is_array($data) || !$id_empresa || !$os1 || !$os1->id) {
            throw new InvalidArgumentException("Parâmetros inválidos para registrarTarefas");
        }

        $tarefasExistentes = (new Os2())->findByIdOs($os1->id);
        $tarefasProcessadas = [];
        $ajustado = false;
        $mensagemAjuste = null;

        if (isset($data["OS2_servico_1"])) {
            $s = 1;
            while (isset($data["OS2_servico_$s"])) {
                $name = 'OS2';

                $id = $data["{$name}_id_$s"];
                $os2 = ll_intValida($id) ? (new Os2())->findById($id) : new Os2();
                if (ll_intValida($id)) {
                    $antes = clone $os2->data();
                    $acao = "U";
                } else {
                    $antes = null;
                    $acao = "C";
                }
                $pularCodigo = false;

                $aditivo = "N";
                if ($name == 'add') {
                    $aditivo = "S";
                }

                $servicoID = $data["{$name}_servico_$s"];

                $servico = (new Servico())->findById($servicoID); //procuro o serviço dessa tarefa

                if ($servico->recorrente == '1') {
                    // Validação da recorrência e data fixa
                    $recorrencia = $data["{$name}_recorrencia_$s"] ?? null;
                    $datafixa = $data["{$name}_datafixa_$s"] ?? null;

                    if (($recorrencia > 2) && empty($datafixa)) {
                        $json['message'] = $this->message->warning("Preencha um dia válido para o campo 'Dia Recorrência'.")->render();
                        echo json_encode($json);
                        return false;
                    } else if ($recorrencia > 2 && $datafixa > 30) {
                        $json['message'] = $this->message->warning("Preencha um dia válido para o campo 'Dia Recorrência'. (1 - 30)")->render();
                        echo json_encode($json);
                        return false;
                    }
                } else {
                    $recorrencia = null;
                    $datafixa = null;
                }

                $os2->id_emp2 = $id_empresa;
                $os2->id_os1 = $os1->id;
                $os2->id_servico = $data["{$name}_servico_$s"];
                if ($os1->id_status == 8 && empty($data["{$name}_operador_$s"])) { //se for um orçamento e operador estiver vazio, então não atribui operador
                    $os2->id_colaborador = null;
                } else {
                    $os2->id_colaborador = $data["{$name}_operador_$s"];
                }
                $os2->qtde = ($data["{$name}_qtd_servico_$s"]) ? moedaSql($data["{$name}_qtd_servico_$s"]) : 1;
                $os2->tempo = intval($data["{$name}_tempo_$s"]) * 60;
                $os2->vunit = moedaSql($data["{$name}_vunit_servico_$s"] ?? '0.00');
                $os2->vtotal = moedaSql($data["{$name}_vtotal_servico_$s"] ?? '0.00');
                $os2->id_recorrencia = $recorrencia ?? null;
                $os2->dia_recorrencia = !empty($datafixa) ? $datafixa : null;
                $os2->obs = $data["{$name}_obs_$s"] ?? null;
                $os2->datalegal = !empty($data["{$name}_datalegal_$s"]) ? $data["{$name}_datalegal_$s"] : null;
                $os2->aditivo = $aditivo;
                $os2->id_users = $id_user;

                if ($servico->recorrente == '1') {
                    if ($servico->id_recorrencia != $os2->id_recorrencia || $servico->dia != $os2->dia_recorrencia) {
                        // Verifica se já existe um registro para este cliente e serviço
                        $recorrenciaExistente = (new RecorrenciasCliServ())->find("id_cli = :id_cli AND id_servico = :id_servico", "id_cli={$os1->id_cli}&id_servico={$os2->id_servico}")->fetch();

                        if ($recorrenciaExistente) {
                            $antes = clone $recorrenciaExistente->data();
                            $recorrenciaExistente->id_recorrencia = $os2->id_recorrencia;
                            $recorrenciaExistente->dia = $datafixa; // Atualiza o dia da recorrência
                            $recorrenciaExistente->id_users = $id_user;

                            if (!$recorrenciaExistente->save()) {
                                error_log($recorrenciaExistente->fail());
                                $json['message'] = $this->message->warning("Erro ao atualizar a recorrência")->render();
                                echo json_encode($json);
                                return false;
                            }

                            $log = new Log();
                            $log->registrarLog("U", $recorrenciaExistente->getEntity(), $recorrenciaExistente->id, $antes, $recorrenciaExistente->data());
                        } else {
                            $recorrenciaCliServ = new RecorrenciasCliServ();
                            $recorrenciaCliServ->id_emp2 = $id_empresa;
                            $recorrenciaCliServ->id_cli = $os1->id_cli;
                            $recorrenciaCliServ->id_servico = $os2->id_servico;
                            $recorrenciaCliServ->id_recorrencia = $os2->id_recorrencia;
                            $recorrenciaCliServ->dia = $recorrencia; // Define o dia da recorrência
                            $recorrenciaCliServ->id_users = $id_user;

                            if (!$recorrenciaCliServ->save()) {
                                error_log($recorrenciaCliServ->fail());
                                $json['message'] = $this->message->warning("Erro ao salvar a recorrência")->render();
                                echo json_encode($json);
                                return false;
                            }

                            $log = new Log();
                            $log->registrarLog("C", $recorrenciaCliServ->getEntity(), $recorrenciaCliServ->id, null, $recorrenciaCliServ->data());
                        }
                    }
                }

                $horaPreferida = !empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : null; //essa é a hora do formulário            

                $dataDoFormulario = strtotime($data["{$name}_dataexec_$s"]);
                $dataHoraFormulario = $dataDoFormulario + $horaPreferida;

                // se verdadeiro significa que existe uma tarefa existente, e por sua vez dataexec_original estará preenchido
                if (ll_intValida($id)) {
                    $dataOriginal = $data["{$name}_dataexec_original_$s"];
                    if ($dataOriginal == $dataHoraFormulario) {
                        $pularCodigo = true;
                    }
                }

                //se $pularCodigo for verdadeiro, significa que a tarefa não foi alterada, então pula para a próxima
                if ($pularCodigo) {
                    $tarefasProcessadas[] = $os2->id;
                    $os2->save();
                    $s++;
                    continue; //pula para a próxima tarefa, ou sai da função caso seja a última tarefa
                } else { //se entrar nesse laço, significa que a tarefa foi alterada
                    // if (ll_intValida($id)) {
                    //     $hoje = time();
                    //     if ($dataHoraFormulario < $hoje) {
                    //         $json['message'] = $this->message->warning("Não é possível alterar uma tarefa para uma data/hora anterior a atual!")->render();
                    //         echo json_encode($json);
                    //         return false;
                    //     }
                    // }

                    if ($os1->id_status == 8) {
                        if (empty($data["{$name}_dataexec_$s"])) {
                            $os2->dataexec = date("Y-m-d");
                            $os2->horaexec = time() - strtotime("today");; // Hora atual em segundos dentro de um dia de 24 horas
                            $os2->horafim = $os2->horaexec + $os2->tempo;
                        }
                    } else {
                        $os2->dataexec = !empty($data["{$name}_dataexec_$s"]) ? $data["{$name}_dataexec_$s"] : date("Y-m-d");
                        $os2->horaexec = $horaPreferida ?? (!empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : time() - strtotime("today"));
                        $os2->horafim = $horaPreferida + $os2->tempo;
                    }
                }

                if (!$os2->save()) {
                    error_log($os2->fail());
                    //var_dump($os2->fail());
                    $json['message'] = $this->message->warning("Erro ao salvar a tarefa(1)")->render();
                    echo json_encode($json);
                    return false;
                }

                $log = new Log();
                $log->registrarLog($acao, $os2->getEntity(), $os2->id, $antes, $os2->data());

                $tarefasProcessadas[] = $os2->id;
                $s++;
            }
        }

        if (isset($data["add_servico_1"])) {
            if (!empty($data["add_servico_1"])) {
                if ($data["OS1_status"] != 8) {
                    $hasOper = false;
                    $hasDateExec = true;
                    $s = 1;
                    while (isset($data["add_operador_$s"])) {
                        if (!empty($data["add_operador_$s"])) {
                            $hasOper = true;
                        }
                        // if (empty($data["add_dataexec_$s"])) {
                        //     $hasDateExec = false;
                        // }
                        if (empty($data["add_servico_$s"])) {
                            $hasTask = false;
                        }
                        $s++;
                    }

                    if (!$hasOper) {
                        $json['message'] = $this->message->warning("Selecione um operador pra tarefa aditiva!")->render();
                        echo json_encode($json);
                        return;
                    }

                    if (!$hasDateExec) {
                        $json['message'] = $this->message->warning("Preencha a data de execução para todas as tarefas aditivas!")->render();
                        echo json_encode($json);
                        return;
                    }
                }

                $s = 1;
                while (isset($data["add_servico_$s"])) {
                    $name = 'add';

                    $id = $data["{$name}_id_$s"];
                    $os2 = ll_intValida($id) ? (new Os2())->findById($id) : new Os2();
                    if (ll_intValida($id)) {
                        $antes = clone $os2->data();
                        $acao = "U";
                    } else {
                        $antes = null;
                        $acao = "C";
                    }
                    $pularCodigo = false;

                    $aditivo = "N";
                    if ($name == 'add') {
                        $aditivo = "S";
                    }

                    $servicoID = $data["{$name}_servico_$s"];

                    $servico = (new Servico())->findById($servicoID); //procuro o serviço dessa tarefa

                    if ($servico->recorrente == '1') {
                        // Validação da recorrência e data fixa
                        $recorrencia = $data["{$name}_recorrencia_$s"] ?? null;
                        $datafixa = $data["{$name}_datafixa_$s"] ?? null;

                        if (($recorrencia > 2) && empty($datafixa)) {
                            $json['message'] = $this->message->warning("Preencha um dia válido para o campo 'Dia Recorrência'.")->render();
                            echo json_encode($json);
                            return false;
                        } else if ($recorrencia > 2 && $datafixa > 30) {
                            $json['message'] = $this->message->warning("Preencha um dia válido para o campo 'Dia Recorrência'. (1 - 30)")->render();
                            echo json_encode($json);
                            return false;
                        }
                    } else {
                        $recorrencia = null;
                        $datafixa = null;
                    }

                    $os2->id_emp2 = $id_empresa;
                    $os2->id_os1 = $os1->id;
                    $os2->id_servico = $data["{$name}_servico_$s"];
                    if ($os1->id_status == 8 && empty($data["{$name}_operador_$s"])) { //se for um orçamento e operador estiver vazio, então não atribui operador
                        $os2->id_colaborador = null;
                    } else {
                        $os2->id_colaborador = $data["{$name}_operador_$s"];
                    }
                    $os2->qtde = ($data["{$name}_qtd_servico_$s"]) ? moedaSql($data["{$name}_qtd_servico_$s"]) : 1;
                    $os2->tempo = intval($data["{$name}_tempo_$s"]) * 60;
                    $os2->vunit = moedaSql($data["{$name}_vunit_servico_$s"] ?? '0.00');
                    $os2->vtotal = moedaSql($data["{$name}_vtotal_servico_$s"] ?? '0.00');
                    $os2->id_recorrencia = $recorrencia ?? null;
                    $os2->dia_recorrencia = !empty($datafixa) ? $datafixa : null;
                    $os2->obs = $data["{$name}_obs_$s"] ?? null;
                    $os2->datalegal = !empty($data["{$name}_datalegal_$s"]) ? $data["{$name}_datalegal_$s"] : null;
                    $os2->aditivo = $aditivo;
                    $os2->id_users = $id_user;

                    if ($servico->recorrente == '1') {
                        if ($servico->id_recorrencia != $os2->id_recorrencia || $servico->dia != $os2->dia_recorrencia) {
                            // Verifica se já existe um registro para este cliente e serviço
                            $recorrenciaExistente = (new RecorrenciasCliServ())->find("id_cli = :id_cli AND id_servico = :id_servico", "id_cli={$os1->id_cli}&id_servico={$os2->id_servico}")->fetch();

                            if ($recorrenciaExistente) {
                                $antes = clone $recorrenciaExistente->data();
                                $recorrenciaExistente->id_recorrencia = $recorrencia;
                                $recorrenciaExistente->dia = $datafixa; // Atualiza o dia da recorrência
                                $recorrenciaExistente->id_users = $id_user;

                                if (!$recorrenciaExistente->save()) {
                                    error_log($recorrenciaExistente->fail());
                                    $json['message'] = $this->message->warning("Erro ao atualizar a recorrência")->render();
                                    echo json_encode($json);
                                    return false;
                                }

                                $log = new Log();
                                $log->registrarLog("U", $recorrenciaExistente->getEntity(), $recorrenciaExistente->id, $antes, $recorrenciaExistente->data());
                            } else {
                                $recorrenciaCliServ = new RecorrenciasCliServ();
                                $recorrenciaCliServ->id_emp2 = $id_empresa;
                                $recorrenciaCliServ->id_cli = $os1->id_cli;
                                $recorrenciaCliServ->id_servico = $os2->id_servico;
                                $recorrenciaCliServ->id_recorrencia = $recorrencia;
                                $recorrenciaCliServ->dia = $datafixa; // Define o dia da recorrência
                                $recorrenciaCliServ->id_users = $id_user;

                                if (!$recorrenciaCliServ->save()) {
                                    error_log($recorrenciaCliServ->fail());
                                    $json['message'] = $this->message->warning("Erro ao salvar a recorrência")->render();
                                    echo json_encode($json);
                                    return false;
                                }

                                $log = new Log();
                                $log->registrarLog("C", $recorrenciaCliServ->getEntity(), $recorrenciaCliServ->id, null, $recorrenciaCliServ->data());
                            }
                        }
                    }

                    $horaPreferida = !empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : null; //essa é a hora do formulário            

                    $dataDoFormulario = strtotime($data["{$name}_dataexec_$s"]);
                    $dataHoraFormulario = $dataDoFormulario + $horaPreferida;

                    // se verdadeiro significa que existe uma tarefa existente, e por sua vez dataexec_original estará preenchido
                    if (ll_intValida($id)) {
                        $dataOriginal = $data["{$name}_dataexec_original_$s"];
                        if ($dataOriginal == $dataHoraFormulario) {
                            $pularCodigo = true;
                        }
                    }

                    //se $pularCodigo for verdadeiro, significa que a tarefa não foi alterada, então pula para a próxima
                    if ($pularCodigo) {
                        $tarefasProcessadas[] = $os2->id;
                        $os2->save();
                        $s++;
                        continue; //pula para a próxima tarefa, ou sai da função caso seja a última tarefa
                    } else { //se entrar nesse laço, significa que a tarefa foi alterada
                        // if (ll_intValida($id)) {
                        //     $hoje = time();
                        //     if ($dataHoraFormulario < $hoje) {
                        //         $json['message'] = $this->message->warning("Não é possível alterar uma tarefa para uma data/hora anterior a atual!")->render();
                        //         echo json_encode($json);
                        //         return false;
                        //     }
                        // }

                        if ($os1->id_status == 8) {
                            if (empty($data["{$name}_dataexec_$s"])) {
                                $os2->dataexec = date("Y-m-d");
                                $os2->horaexec = time() - strtotime("today"); // Hora atual em segundos dentro de um dia de 24 horas
                                $os2->horafim = $os2->horaexec + $os2->tempo;
                            }
                        } else {
                            $os2->dataexec = !empty($data["{$name}_dataexec_$s"]) ? $data["{$name}_dataexec_$s"] : date("Y-m-d");
                            $os2->horaexec = $horaPreferida ?? (!empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : time() - strtotime("today"));
                            $os2->horafim = $horaPreferida + $os2->tempo;
                        }
                    }

                    if (!$os2->save()) {
                        error_log($os2->fail());
                        //var_dump($os2->fail());
                        $json['message'] = $this->message->warning("Erro ao salvar a tarefa(2)")->render();
                        echo json_encode($json);
                        return false;
                    }

                    $log = new Log();
                    $log->registrarLog($acao, $os2->getEntity(), $os2->id, $antes, $os2->data());

                    $tarefasProcessadas[] = $os2->id;
                    $s++;
                }
            }
        }

        //caso alguma tarefa que estava no banco não esteja mais no formulário, então ela deve ser excluída
        if ($tarefasExistentes) {
            foreach ($tarefasExistentes as $tarefaExistente) {
                if (!in_array($tarefaExistente->id, $tarefasProcessadas)) {
                    $antes = clone $tarefaExistente->data();
                    if (!$tarefaExistente->destroy()) {
                        error_log("Erro ao excluir tarefa ID: {$tarefaExistente->id}");
                    }
                    $log = new Log();
                    $log->registrarLog("D", $tarefaExistente->getEntity(), $tarefaExistente->id, $antes, null);
                }
            }
        }

        return true;
    }

    private function atualizaTarefaOperDesk($data, $os1, $id_empresa, $user, $name)
    {
        $flagOs2 = false;
        $flagAdd = true;
        if ($name == 'OS2') {
            $s = 1;
            while (isset($data["{$name}_servico_$s"])) {
                $id = $data["{$name}_id_$s"];

                $os2 = (new Os2())->findById($id);
                $antes = clone $os2->data();
                $acao = "U";

                $horaPreferida = !empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : null; //essa é a hora do formulário

                $dataDoFormulario = strtotime($data["{$name}_dataexec_$s"]);
                $dataHoraFormulario = $dataDoFormulario + $horaPreferida;

                $dataOriginal = $data["{$name}_dataexec_original_$s"];
                $pularCodigo = false;
                if ($dataOriginal == $dataHoraFormulario) {
                    $pularCodigo = true;
                }


                //se $pularCodigo for verdadeiro, significa que a tarefa não foi alterada, então pula para a próxima
                if ($pularCodigo) {
                    $os2->save();
                    $s++;
                    continue; //pula para a próxima tarefa, ou sai da função caso seja a última tarefa
                } else {
                    $os2->dataexec = !empty($data["{$name}_dataexec_$s"]) ? $data["{$name}_dataexec_$s"] : date("Y-m-d");
                    $os2->horaexec = $horaPreferida ?? (!empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : time() - strtotime("today"));
                    $os2->horafim = $horaPreferida + $os2->tempo;
                }

                if (!$os2->save()) {
                    error_log($os2->fail());
                    //var_dump($os2->fail());
                    $json['message'] = $this->message->warning("Erro ao salvar a tarefa(3)")->render();
                    echo json_encode($json);
                    return false;
                }

                $log = new Log();
                $log->registrarLog($acao, $os2->getEntity(), $os2->id, $antes, $os2->data());
                $s++;
            }

            $flagOs2 = true;
            return $flagOs2;
        } else if ($name == 'add') {
            $s = 1;
            if (!empty($data["add_servico_1"])) {

                $flagAdd = false;

                $s = 1;
                while (isset($data["add_operador_$s"])) {

                    if (empty($data["add_operador_$s"])) {
                        $json['message'] = "Preencha o campo 'Operador' na tarefa aditiva #{$s}!";
                        return $json;
                    }

                    if (empty($data["add_servico_$s"])) {
                        $json['message'] = "Preencha o campo 'Serviço' na tarefa aditiva #{$s}!";
                        return $json;
                    }

                    $s++;
                }

                $s = 1;
                while (isset($data["add_servico_$s"])) {
                    $id = $data["{$name}_id_$s"];
                    $os2 = ll_intValida($id) ? (new Os2())->findById($id) : new Os2();
                    if (ll_intValida($id)) {
                        $antes = clone $os2->data();
                        $acao = "U";
                    } else {
                        $antes = null;
                        $acao = "C";
                    }
                    $pularCodigo = false;

                    $aditivo = "N";
                    if ($name == 'add') {
                        $aditivo = "S";
                    }

                    $servicoID = $data["{$name}_servico_$s"];

                    $servico = (new Servico())->findById($servicoID); //procuro o serviço dessa tarefa

                    if ($servico->recorrente == '1') {
                        // Validação da recorrência e data fixa
                        $recorrencia = $data["{$name}_recorrencia_$s"] ?? null;
                        $datafixa = $data["{$name}_datafixa_$s"] ?? null;

                        if (($recorrencia > 2) && empty($datafixa)) {
                            $json['message'] = "Preencha um dia válido para o campo 'Dia Recorrência'.";
                            return $json;
                        } else if ($recorrencia > 2 && $datafixa > 30) {
                            $json['message'] = "Preencha um dia válido para o campo 'Dia Recorrência'. (1 - 30)";
                            return $json;
                        }
                    } else {
                        $recorrencia = null;
                        $datafixa = null;
                    }

                    $os2->id_emp2 = $id_empresa;
                    $os2->id_os1 = $os1->id;
                    $os2->id_servico = $data["{$name}_servico_$s"];
                    $os2->id_colaborador = $data["{$name}_operador_$s"];
                    $os2->qtde = ($data["{$name}_qtd_servico_$s"]) ? moedaSql($data["{$name}_qtd_servico_$s"]) : 1;
                    $os2->tempo = intval($data["{$name}_tempo_$s"]) * 60;
                    $os2->vunit = moedaSql($data["{$name}_vunit_servico_$s"] ?? '0.00');
                    $os2->vtotal = moedaSql($data["{$name}_vtotal_servico_$s"] ?? '0.00');
                    $os2->id_recorrencia = $recorrencia ?? null;
                    $os2->dia_recorrencia = !empty($datafixa) ? $datafixa : null;
                    $os2->obs = $data["{$name}_obs_$s"] ?? null;
                    $os2->datalegal = !empty($data["{$name}_datalegal_$s"]) ? $data["{$name}_datalegal_$s"] : null;
                    $os2->aditivo = $aditivo;
                    $os2->id_users = $user->id;

                    if ($servico->recorrente == '1') {
                        if ($servico->id_recorrencia != $os2->id_recorrencia || $servico->dia != $os2->dia_recorrencia) {
                            // Verifica se já existe um registro para este cliente e serviço
                            $recorrenciaExistente = (new RecorrenciasCliServ())->find("id_cli = :id_cli AND id_servico = :id_servico", "id_cli={$os1->id_cli}&id_servico={$os2->id_servico}")->fetch();

                            if ($recorrenciaExistente) {
                                $antes = clone $recorrenciaExistente->data();
                                $recorrenciaExistente->id_recorrencia = $recorrencia;
                                $recorrenciaExistente->dia = $datafixa; // Atualiza o dia da recorrência
                                $recorrenciaExistente->id_users = $user->id;

                                if (!$recorrenciaExistente->save()) {
                                    error_log($recorrenciaExistente->fail());
                                    $json['message'] = "Erro ao atualizar a recorrência";
                                    return $json;
                                }

                                $log = new Log();
                                $log->registrarLog("U", $recorrenciaExistente->getEntity(), $recorrenciaExistente->id, $antes, $recorrenciaExistente->data());
                            } else {
                                $recorrenciaCliServ = new RecorrenciasCliServ();
                                $recorrenciaCliServ->id_emp2 = $id_empresa;
                                $recorrenciaCliServ->id_cli = $os1->id_cli;
                                $recorrenciaCliServ->id_servico = $os2->id_servico;
                                $recorrenciaCliServ->id_recorrencia = $recorrencia;
                                $recorrenciaCliServ->dia = $datafixa; // Define o dia da recorrência
                                $recorrenciaCliServ->id_users = $user->id;

                                if (!$recorrenciaCliServ->save()) {
                                    error_log($recorrenciaCliServ->fail());
                                    $json['message'] = "Erro ao salvar a recorrência";
                                    return $json;
                                }

                                $log = new Log();
                                $log->registrarLog("C", $recorrenciaCliServ->getEntity(), $recorrenciaCliServ->id, null, $recorrenciaCliServ->data());
                            }
                        }
                    }

                    $horaPreferida = !empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : null; //essa é a hora do formulário            

                    $dataDoFormulario = strtotime($data["{$name}_dataexec_$s"]);
                    $dataHoraFormulario = $dataDoFormulario + $horaPreferida;

                    // se verdadeiro significa que existe uma tarefa existente, e por sua vez dataexec_original estará preenchido
                    if (ll_intValida($id)) {
                        $dataOriginal = $data["{$name}_dataexec_original_$s"];
                        if ($dataOriginal == $dataHoraFormulario) {
                            $pularCodigo = true;
                        }
                    }

                    //se $pularCodigo for verdadeiro, significa que a tarefa não foi alterada, então pula para a próxima
                    if ($pularCodigo) {
                        $tarefasProcessadas[] = $os2->id;
                        $os2->save();
                        $s++;
                        continue; //pula para a próxima tarefa, ou sai da função caso seja a última tarefa
                    } else {
                        $os2->dataexec = !empty($data["{$name}_dataexec_$s"]) ? $data["{$name}_dataexec_$s"] : date("Y-m-d");
                        $os2->horaexec = $horaPreferida ?? (!empty($data["{$name}_horaexec_$s"]) ? timeToSeconds($data["{$name}_horaexec_$s"]) : time() - strtotime("today"));
                        $os2->horafim = $horaPreferida + $os2->tempo;
                    }

                    if (!$os2->save()) {
                        error_log($os2->fail());
                        //var_dump($os2->fail());
                        $json['message'] = "Erro ao salvar a tarefa(4)";
                        return $json;
                    }

                    $log = new Log();
                    $log->registrarLog($acao, $os2->getEntity(), $os2->id, $antes, $os2->data());

                    $s++;
                }

                $ordem = (new Os1())->findById($os1->id);
                $antes = clone $ordem->data();
                $acao = "U";

                $tarefas = (new Os2())->find("id_os1 = :id_os1", "id_os1={$ordem->id}", "SUM(vtotal) as total_vtotal")->fetch();
                $materiais = (new Os3())->find("id_os1 = :id_os1", "id_os1={$ordem->id}", "SUM(vtotal) as total_vtotal")->fetch();

                $ordem->vtotal = $tarefas->total_vtotal + $materiais->total_vtotal;
                $ordem->vtotal = moedaSql($ordem->vtotal);
                if (!$ordem->save()) {
                    error_log($ordem->fail());
                    $json['message'] = "Erro ao atualizar o valor total da ordem de serviço";
                    return $json;
                }
                $log = new Log();
                $log->registrarLog($acao, $ordem->getEntity(), $ordem->id, $antes, $ordem->data());
                $flagAdd = true;
            }

            return $flagAdd;
        }
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (isset($data['modalos'])) {
            $id_ordens = $data['id_os1'];
        } else {
            $id_ordens = ll_decode($data['id_os1']);
        }

        if (empty($data['OS1_cliente'])) {
            $json['message'] = $this->message->warning("Selecione um cliente!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['OS1_controle'])) {
            if (!str_verify($data['OS1_controle'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CONTROLE'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['OS1_obs'])) {
            if (!str_verify($data['OS1_obs'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'OBS'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data["OS3_material_1"])) {
            if (empty($data["OS3_qtd_material_1"])) {
                $json['message'] = $this->message->warning("Preencha a quantidade do produto/material 1!")->render();
                echo json_encode($json);
                return;
            }
        }

        // if (empty($data["OS2_servico_1"])) {
        //     $json['message'] = $this->message->warning("Selecione uma tarefa!")->render();
        //     echo json_encode($json);
        //     return;
        // }

        if ($data["OS1_status"] != 8) {
            $hasOper = false;
            $hasDateExec = true;
            $s = 1;
            while (isset($data["OS2_operador_$s"])) {
                if (!empty($data["OS2_operador_$s"])) {
                    $hasOper = true;
                }
                // if (empty($data["OS2_dataexec_$s"])) {
                //     $hasDateExec = false;
                // }                
                $s++;
            }

            if (!$hasOper) {
                $json['message'] = $this->message->warning("Selecione um operador!")->render();
                echo json_encode($json);
                return;
            }

            // if (!$hasDateExec) {
            //     $json['message'] = $this->message->warning("Preencha a data de execução para todas as tarefas!")->render();
            //     echo json_encode($json);
            //     return;
            // }
        }

        $s = 1;

        while (isset($data["OS2_servico_$s"])) {
            // Validação de data
            if (!empty($data["OS2_dataexec_$s"])) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data["OS2_dataexec_$s"])) {
                    $json['message'] = $this->message->warning("Formato de data inválido para a tarefa #{$s}! Use o formato DD/MM/YYYY.")->render();
                    echo json_encode($json);
                    return;
                }

                $d = DateTime::createFromFormat('Y-m-d', $data["OS2_dataexec_$s"]);
                if (!$d || $d->format('Y-m-d') !== $data["OS2_dataexec_$s"]) {
                    $json['message'] = $this->message->warning("Data inválida para a tarefa #{$s}!")->render();
                    echo json_encode($json);
                    return;
                }

                // Criar objeto da data atual sem horário (para evitar problemas de comparação)
                $hoje = new DateTime();
                $hoje->setTime(0, 0, 0); // Zera o horário para garantir que só a data seja comparada

                // if ($d < $hoje) {
                //     $json['message'] = $this->message->warning("A data para a tarefa #{$s} não pode ser anterior à data atual!")->render();
                //     echo json_encode($json);
                //     return;
                // }
            }

            // Validação de hora
            if (!empty($data["OS2_horaexec_$s"])) {
                if (!preg_match('/^\d{2}:\d{2}$/', $data["OS2_horaexec_$s"])) {
                    $json['message'] = $this->message->warning("Formato de hora inválido para a tarefa #{$s}! Use o formato HH:MM.")->render();
                    echo json_encode($json);
                    return;
                }

                $t = DateTime::createFromFormat('H:i', $data["OS2_horaexec_$s"]);
                if (!$t || $t->format('H:i') !== $data["OS2_horaexec_$s"]) {
                    $json['message'] = $this->message->warning("Hora inválida para a tarefa #{$s}!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            $s++;
        }

        if (ll_intValida($id_ordens)) {
            $os1 = (new Os1())->findById($id_ordens);
            $antes = clone $os1->data();
            $acao = "U";
        } else {
            $os1 = new Os1();
            $antes = null;
            $acao = "C";
        }

        if ($this->user->tipo == 3) {

            if (!empty($data["OS2_servico_1"])) {
                $OS2 = $this->atualizaTarefaOperDesk($data, $os1, $id_empresa, $this->user, "OS2");
            }

            if (!empty($data["add_servico_1"])) {
                $add = $this->atualizaTarefaOperDesk($data, $os1, $id_empresa, $this->user, "add");
                if ($add['message']) {
                    $json["message"] = $this->message->warning($add['message'])->render();
                    echo json_encode($json);
                    return;
                }
            } else {
                $add = true;
            }

            if ($OS2 && $add) {
                $this->message->success("Tarefas atualizadas com sucesso!")->flash();
                $json['reload'] = true;
                echo json_encode($json);
                return;
            } else {
                $json['message'] = $this->message->warning("Erro[OS799]. Verifique as informações")->render();
                echo json_encode($json);
                return;
            }
        }

        $os1->id_emp2 = $id_empresa;
        $os1->controle = !empty($data["OS1_controle"]) ? $data["OS1_controle"] : null;
        $os1->id_status = $data["OS1_status"];
        if (in_array($data["OS1_status"], [5, 7])) {
            $os1->concluir = "N";
        }
        if (isset($data['OS1_tipo'])) {
            $os1->id_tipo = $data["OS1_tipo"];
        } else {
            $os1->id_tipo = (new Tipo())->find()->fetch()->id;
        }
        $os1->id_cli = $data["OS1_cliente"];
        $os1->datacad = date("Y-m-d");
        $os1->obs = $data["OS1_obs"];
        $os1->vtotal = $data["OS1_vtotal"] != "" ? moedaSql($data["OS1_vtotal"]) : 0;
        $os1->id_obras = $data["OS1_obra"] != "" ? $data["OS1_obra"] : null;
        if ($data["OS1_status"] == 5 || $data["OS1_status"] == 7) {
            $os1->estornado = null;
        }
        $os1->id_users = $id_user;

        $os1->beginTransaction();

        if (!$os1->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            $os1->rollback();
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $os1->getEntity(), $os1->id, $antes, $os1->data());

        if ($os1->id_status == 8 && empty($data["OS2_servico_1"])) {
            // Pula a variável $resultado
        } else {
            $resultado = $this->registrarTarefas($data, $id_empresa, $os1);

            if (!$resultado) {
                $os1->rollback();
                return;
            }
        }

        if (ll_intValida($id_ordens)) {
            $delOs3 = (new Os3())->find("id_os1 = :id_os1 AND (id_os2 IS NULL OR id_os2 = '')", "id_os1={$id_ordens}")->fetch(true);
            if (!empty($delOs3)) {
                // var_dump($delOs3);
                // exit;
                foreach ($delOs3 as $os) {
                    $antes = clone $os->data();
                    $os->destroy();
                    $log = new Log();
                    $log->registrarLog("D", $os->getEntity(), $os->id, $antes, null);
                }
            }

            $medicoes = new Os2_1();

            $os2 = (new OS2())->findByIdOs($id_ordens);

            if ($os1->id_status == 8 && !empty($os2)) {
                foreach ($os2 as $tarefa) {
                    $medicoesLista = $medicoes->findByOs2($tarefa->id);

                    if (!empty($medicoesLista)) {
                        $tarefa->medicao = $medicoesLista;
                    } else {
                        $tarefa->medicao = []; // Garante que a chave exista mesmo se não houver medições
                    }

                    //* TIREI POIS AGORA OS MATERIAIS DAS TAREFAS SÂO LANÇADOS DE MANEIRA DIFERENTE **/
                    // $m = 1;
                    // if (!empty($data["OS3_material_{$tarefa->id}_$m"])) {
                    //     while (isset($data["OS3_material_{$tarefa->id}_$m"])) {
                    //         $os3 = new Os3();
                    //         $os3->id_emp2 = $id_empresa;
                    //         $os3->id_os1 = $os1->id;
                    //         $os3->id_os2 = $tarefa->id;
                    //         $os3->id_materiais = $data["OS3_material_{$tarefa->id}_$m"];
                    //         $os3->id_users = $id_user;

                    //         if (empty($data["OS3_qtd_material_{$tarefa->id}_$m"])) {
                    //             $json['message'] = $this->message->warning("Preencha a quantidade do material {$m} da tarefa #{$tarefa->id}!")->render();
                    //             echo json_encode($json);
                    //             $os1->rollback();
                    //             return;
                    //         } else {
                    //             $os3->qtde = $data["OS3_qtd_material_{$tarefa->id}_$m"];
                    //         }

                    //         if (empty($data["OS3_valor_material_{$tarefa->id}_$m"])) {
                    //             $json['message'] = $this->message->warning("Preencha o Valor Unitário do material {$m} da tarefa #{$tarefa->id}!")->render();
                    //             echo json_encode($json);
                    //             $os1->rollback();
                    //             return;
                    //         } else {
                    //             $os3->vunit = moedaSql($data["OS3_valor_material_{$tarefa->id}_$m"]);
                    //         }

                    //         $os3->vtotal = moedaSql($data["OS3_vtotal_material_{$tarefa->id}_$m"]);

                    //         if (!$os3->save()) {
                    //             $json['message'] = $this->message->warning("Erro ao salvar Material {$m} da tarefa #{$tarefa->id}!")->render();
                    //             echo json_encode($json);
                    //             $os1->rollback();
                    //             return;
                    //         }

                    //         $log = new Log();
                    //         $log->registrarLog("C", $os3->getEntity(), $os3->id, null, $os3->data());

                    //         $m++;
                    //     }
                    // }
                }
            }
        }


        if (!empty($data["OS3_material_1"])) {
            $m = 1;
            while (isset($data["OS3_material_$m"])) {

                $os3 = new Os3();
                $os3->id_emp2 = $id_empresa;
                $os3->id_os1 = $os1->id;
                $os3->id_users = $id_user;

                if (empty($data["OS3_material_$m"])) {
                    $json['message'] = $this->message->warning("Selecione o produto/material da linha {$m}!")->render();
                    echo json_encode($json);
                    $os1->rollback();
                    return;
                } else {
                    $os3->id_materiais = $data["OS3_material_$m"];
                }

                if (empty($data["OS3_qtd_material_$m"])) {
                    $json['message'] = $this->message->warning("Preencha a quantidade do produto/material {$m}!")->render();
                    echo json_encode($json);
                    $os1->rollback();
                    return;
                } else {
                    $quantidade = $data["OS3_qtd_material_$m"];
                    $os3->qtde = float_br_to_us($quantidade);
                }

                if (empty($data["OS3_valor_material_$m"])) {
                    $json['message'] = $this->message->warning("Preencha o Valor Unitário do produto/material {$m}!")->render();
                    echo json_encode($json);
                    $os1->rollback();
                    return;
                } else {
                    $os3->vunit = moedaSql($data["OS3_valor_material_$m"]);
                }

                $os3->vtotal = moedaSql($data["OS3_vtotal_material_$m"]);

                if (!$os3->save()) {
                    $fail = $os3->fail();
                    //$json['message'] = $this->message->warning($fail)->render();
                    $json['message'] = $this->message->warning("Erro ao salvar Produto/Material {$m}!")->render();
                    echo json_encode($json);
                    $os1->rollback();
                    return;
                }

                $log = new Log();
                $log->registrarLog("C", $os3->getEntity(), $os3->id, null, $os3->data());
                $m++;
            }
        }

        $os1->commit();

        $mensagem = null;


        if (ll_intValida($id_ordens)) {
            $msg = "REGISTRO ALTERADO COM SUCESSO! " . $mensagem;
            if (isset($data['modalos']) && $data['modalos'] == "osnova") {
                $json["message"] = $this->message->success($msg)->render();
                $json["ordem"] = true;
            } else {
                $this->message->success($msg)->flash();
                $json['redirect'] = url('ordens/form/' . ll_encode($os1->id));
            }
        } else {
            $msg = "CADASTRADO COM SUCESSO! " . $mensagem;
            if (isset($data['modalos']) && $data['modalos'] == "osnova") {
                $json["message"] = $this->message->success($msg)->render();
                $json["ordem"] = true;
            } else {
                $this->message->success($msg)->flash();
                $json['redirect'] = url('ordens/form/' . ll_encode($os1->id));
            }
        }

        echo json_encode($json);
    }

    public function gerarRecorrencias($data)
    {
        $user = $this->user;
        $id_emp2 = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_emp2);
        $tarefas = (new Os2())->findByIdOs($data['id']);

        if ($tarefas) {
            $recorrenciaCriada = false; // Flag para verificar se alguma recorrência foi criada

            foreach ($tarefas as $tarefa) {
                if ($empresa->equipamentoObrigatorio == 'X') {
                    $equipamentos = (new Os2_2())->find("id_os2 = :id_os2", "id_os2={$tarefa->id}")->fetch();

                    if (empty($equipamentos)) {
                        $json['status'] = "error";
                        $json['message'] = $this->message->warning("Não é possível concluir a OS. A tarefa #{$tarefa->id} não possui equipamentos vinculados!")->render();
                        echo json_encode($json);
                        return;
                    }
                }

                //** CONCLUI AS TAREFAS E SALVA O LOG */
                if ($tarefa->status != "C" || $tarefa->status != "D") {
                    $antes = clone $tarefa->data();
                    $acao = "U";
                    $tarefa->status = "C";
                    $tarefa->save();
                    $log = new Log();
                    $log->registrarLog($acao, $tarefa->getEntity(), $tarefa->id, $antes, $tarefa->data());
                }

                $estornado = (new Os1())->findById($data['id'])->estornado;

                if ($estornado != 'S' && !empty($tarefa->id_recorrencia)) {
                    $recorrencia = (new Recorrencias())->findById($tarefa->id_recorrencia);

                    if ($recorrencia) {
                        if ($recorrencia->id < 3) {
                            $novaDataExec = date('Y-m-d', strtotime("+{$recorrencia->intervalo} days", strtotime($tarefa->dataexec)));
                        } else {
                            $novaData = strtotime("+{$recorrencia->intervalo} days", strtotime($tarefa->dataexec));
                            $novoMes = date('m', $novaData);
                            $novoAno = date('Y', $novaData);
                            $diaRecorrencia = $tarefa->dia_recorrencia;

                            // Ajusta o dia para o mais próximo do dia_recorrencia
                            $dataAtual = new DateTime(date('Y-m-d', $novaData));
                            $dataMesAtual = new DateTime("{$novoAno}-{$novoMes}-{$diaRecorrencia}");
                            $dataMesSeguinte = (new DateTime("{$novoAno}-{$novoMes}-01"))->modify('+1 month')->setDate($novoAno, $novoMes + 1, $diaRecorrencia);

                            // Verifica qual data está mais próxima
                            $diffAtual = abs($dataAtual->getTimestamp() - $dataMesAtual->getTimestamp());
                            $diffSeguinte = abs($dataAtual->getTimestamp() - $dataMesSeguinte->getTimestamp());

                            if ($diffAtual <= $diffSeguinte) { // Prioriza o mês atual em caso de empate
                                $novaDataExec = $dataMesAtual->format('Y-m-d');
                            } else {
                                $novaDataExec = $dataMesSeguinte->format('Y-m-d');
                            }
                        }

                        $novaOs1 = new Os1();
                        $os1Original = (new Os1())->findById($data['id']);
                        $novaOs1->id_emp2 = $os1Original->id_emp2;
                        $novaOs1->id_status = 2;
                        $novaOs1->id_cli = $os1Original->id_cli;
                        $novaOs1->id_tipo = $os1Original->id_tipo;
                        $novaOs1->id_obras = $os1Original->id_obras;
                        $novaOs1->datacad = date("Y-m-d");
                        $novaOs1->obs = $os1Original->obs;
                        $novaOs1->os1_origem = $os1Original->id;
                        $novaOs1->id_users = $this->user->id;

                        $novaOs1->beginTransaction();

                        if (!$novaOs1->save()) {
                            error_log($novaOs1->fail());
                            $json['message'] = $this->message->warning("Erro ao criar nova OS para recorrência")->render();
                            echo json_encode($json);
                            $novaOs1->rollback();
                            return;
                        }

                        $novaTarefa = new Os2();
                        $novaTarefa->id_emp2 = $tarefa->id_emp2;
                        $novaTarefa->id_os1 = $novaOs1->id;
                        $novaTarefa->id_servico = $tarefa->id_servico;
                        $novaTarefa->id_colaborador = $tarefa->id_colaborador;
                        $novaTarefa->qtde = $tarefa->qtde;
                        $novaTarefa->vunit = $tarefa->vunit;
                        $novaTarefa->vtotal = $tarefa->qtde * $tarefa->vunit;
                        $novaTarefa->tempo = $tarefa->tempo;
                        $novaTarefa->horafim = $tarefa->horafim;
                        $novaTarefa->status = "A";
                        $novaTarefa->aditivo = "N";
                        $novaTarefa->id_users = $this->user->id;
                        $novaTarefa->id_recorrencia = $tarefa->id_recorrencia;
                        $novaTarefa->dia_recorrencia = $tarefa->dia_recorrencia;
                        $novaTarefa->dataexec = date_fmt($novaDataExec, "Y-m-d");

                        $servico = (new Servico())->findById($tarefa->id_servico);
                        if (!empty($servico->recor_datalegal) && $servico->recor_datalegal != "livre") {
                            if (!empty($tarefa->datalegal)) {
                                $dataGerada = new DateTime(calculaDataRecorrente($servico->datalegal, $servico->recor_datalegal));
                                $dataTarefa = new DateTime($tarefa->datalegal);
                                if ($dataGerada == $tarefa->datalegal || $dataGerada < $dataTarefa) {
                                    $novaTarefa->datalegal = calculaDataRecorrente($dataGerada, $servico->recor_datalegal, true);
                                } else {
                                    $novaTarefa->datalegal = calculaDataRecorrente($servico->datalegal, $servico->recor_datalegal);
                                }
                            } else {
                                $novaTarefa->datalegal = calculaDataRecorrente($servico->datalegal, $servico->recor_datalegal);
                            }
                        } else {
                            $novaTarefa->datalegal = null;
                        }

                        if (!$novaTarefa->save()) {
                            error_log($novaTarefa->fail());
                            $json['message'] = $this->message->warning("Erro ao criar nova tarefa para recorrência")->render();
                            echo json_encode($json);
                            $novaOs1->rollback();
                            return;
                        }
                        $novaOs1->commit();

                        $novaOs1->vtotal = $novaTarefa->vtotal;
                        $novaOs1->save();

                        $log = new Log();
                        $log->registrarLog("C", $novaOs1->getEntity(), $novaOs1->id, null, $novaOs1->data());
                        $log->registrarLog("C", $novaTarefa->getEntity(), $novaTarefa->id, null, $novaTarefa->data());

                        $recorrenciaCriada = true; // Marca que uma recorrência foi criada
                    }
                }
            }

            // Verifica se alguma recorrência foi criada
            $os1Original = (new Os1())->findById($data['id']);
            $json['os1'] = $os1Original ? $os1Original->data() : null;
            $json['status'] = "success";
            $json['message'] = $recorrenciaCriada ? "OS concluída! Recorrência criada!" : "OS concluída! Nenhuma recorrência criada!";
            echo json_encode($json);
            return;
        }
    }

    public function pdf($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $servicos = false;
        if (isset($_GET['servicos']) && $_GET['servicos'] == '1') {
            $servicos = true;
        }

        $materiais = false;
        if (isset($_GET['produtos']) && $_GET['produtos'] == '1') {
            $materiais = true;
        }

        if (isset($data['emp']) && ll_intValida($data['emp']) && $data['emp'] != 0) {
            $empresa = (new Emp2())->findById($data['emp']);
        } else {
            $empresa = (new Emp2())->findById($id_empresa);
        }

        $id = ll_decode($data['id']);

        $os1 = (new Os1())->findById($id);

        if ($servicos) {
            $os2 = (new Os2())->find(
                "id_os1 = :id_os1",
                "id_os1={$os1->id}",
                "id_servico, vunit, SUM(qtde) as qtde, SUM(vtotal) as vtotal"
            )->group("id_servico, vunit")->fetch(true);
        } else {
            $os2 = [];
        }

        if ($materiais) {
            $os3 = (new Os3())->find(
                "id_os1 = :id_os1",
                "id_os1={$os1->id}",
                "id_materiais, vunit, SUM(qtde) as qtde, SUM(vtotal) as vtotal"
            )->group("id_materiais, vunit")->fetch(true);
        } else {
            $os3 = [];
        }


        $html = $this->view->render("tcsistemas.os/ordens/ordensPdf", [
            "os1" => $os1,
            "os2" => $os2,
            "emp" => $empresa,
            "os3" => $os3
        ]);

        $textoRodape = "Gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //echo $html;
        ll_pdfGerar($html, "ordem-de-servico", "R", "P", "", $textoRodape);
    }

    public function statusCancelar($data)
    {
        $id_user = $this->user->id;
        $id_ordens = $data['os'];
        $status = $data['status'];

        if (ll_intValida($id_ordens)) {
            $os1 = (new Os1())->findById($id_ordens);
            $os1->id_users = $id_user;

            $os2 = (new OS2())->findByIdOs($id_ordens);

            $cancelar = true;

            foreach ($os2 as $os) {
                if ($os->id_status == "I" || $os->id_status == "P" || $os->id_status == "C") {
                    $cancelar = false;
                    break;
                }
            }

            if ($cancelar) {
                $os1->id_status = 7;
                foreach ($os2 as $os) {
                    $antes = clone $os->data();
                    $os->status = "D";
                    $os->id_users = $id_user;
                    $os->save();
                    $log = new Log();
                    $log->registrarLog("U", $os->getEntity(), $os->id, $antes, $os->data());
                }
                if (!$os1->save()) {
                    $json['message'] = $this->message->error("ERRO AO TENTAR CANCELAR!")->render();
                    echo json_encode($json);
                }
                $logOs1 = new Log();
                $logOs1->registrarLog("U", $os1->getEntity(), $os1->id, $os1->data(), $os1->data());

                $this->message->warning("REGISTRO CANCELADO COM SUCESSO")->flash();
                if ($data['agendaModal'] == "true") {
                    $json["reload"] = true;
                } else {
                    $json["redirect"] = url('ordens');
                }
                echo json_encode($json);
            } else {
                $json['message'] = $this->message->warning("Não é possível cancelar uma OS com tarefas em andamento!")->render();
                echo json_encode($json);
            }
        }
    }

    public function verificaMateriais($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $os3 = (new Os3())->find(
            "id_os2 = :id_os2",
            "id_os2={$data['id_tarefa']}"
        )->fetch(true);

        $tarefa = (new Os2())->findById($data['id_tarefa']);

        $os3Data = [];
        $somaVtotal = 0;

        if (!empty($os3)) {
            foreach ($os3 as $os) {
                $os->delete = url('ordens/excluir_material');
                $os->crypt_id = ll_encode($os->id);
                $os->edit = url("ordens/materiais");
                $os->descricao = (new Materiais())->findById($os->id_materiais)->descricao;
                $os->unidade = (new Materiais())->findById($os->id_materiais)->unidade;
                $somaVtotal += $os->vtotal; // Soma o valor total
            }

            $os3Data = objectsToArray($os3);
            $json['status'] = "success";
            $json['servico'] = (new Servico())->findById($tarefa->id_servico)->nome;
            $json['tarefaId'] = $data['id_tarefa'];
            $json['materiais'] = $os3Data;
            $json['soma_vtotal'] = $somaVtotal; // Adiciona a soma ao JSON            
        } else {
            $json['servico'] = (new Servico())->findById($tarefa->id_servico)->nome;
            $json['status'] = "error";
        }
        echo json_encode($json);
        return;
    }

    public function materiais($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $id = isset($data['id']) ? ll_decode($data['id']) : "";

        if (!ll_intValida($id)) {
            if (empty($data['id_material'])) {
                $json['message'] = $this->message->warning("Selecione um produto/material!")->render();
                echo json_encode($json);
                return;
            }
        }

        // NOVA VALIDAÇÃO: Verificar se material já existe para esta tarefa
        $materialExistente = (new Os3())->find(
            "id_os2 = :id_os2 AND id_materiais = :id_materiais",
            "id_os2={$data['id_tarefa']}&id_materiais={$data['id_material']}"
        )->fetch();

        if ($materialExistente) {
            $material = (new Materiais())->findById($data['id_material']);
            $json['message'] = $this->message->warning("O produto/material '{$material->descricao}' já foi lançado para esta tarefa. Edite o lançamento existente!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['qtde'])) {
            if (!float_verify($data['qtde'], 3) && !ll_intValida($data['qtde'])) {
                $json['message'] = $this->message->warning("Quantidade inválida!")->render();
                echo json_encode($json);
                return;
            }
            $quantidade = float_br_to_us($data['qtde']);
        } else {
            $json['message'] = $this->message->warning("Preencha a quantidade!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['vunit'])) {
            if (!float_verify($data['vunit'], 2) && !ll_intValida($data['vunit'])) {
                $json['message'] = $this->message->warning("Valor unitário inválido!")->render();
                echo json_encode($json);
                return;
            }
            $valor = float_br_to_us($data['vunit']);
        } else {
            $json['message'] = $this->message->warning("Preencha o valor unitário!")->render();
            echo json_encode($json);
            return;
        }

        $valorTotal = $quantidade * $valor;

        if (ll_intValida($id)) {
            $os3 = (new Os3())->findById($id);
            $antes = clone $os3->data();
            $acao = "U";
        } else {
            $os3 = new Os3();
            $acao = "C";
            $antes = null;
        }

        $os2 = (new Os2())->findById($data['id_tarefa']);

        $os3->id_emp2 = $id_empresa;
        $os3->id_os1 = $os2->id_os1;
        $os3->id_os2 = $data['id_tarefa'];
        $os3->id_materiais = isset($data['id_material']) ? $data['id_material'] : $os3->id_materiais;
        $os3->qtde = $quantidade;
        $os3->vunit = $valor;
        $os3->vtotal = round($valorTotal, 2);
        $os3->id_users = $id_user;

        $os1 = (new Os1())->findById($os2->id_os1);
        $antes = clone $os1->data();
        $os1->vtotal = $os1->vtotal + $valorTotal;
        $os1->beginTransaction();
        if (!$os1->save()) {
            $json['message'] = $this->message->warning("Erro ao salvar produto/material!")->render();
            echo json_encode($json);
            $os1->rollback();
            return;
        }
        $logOs1 = new Log();
        $logOs1->registrarLog("U", $os1->getEntity(), $os1->id, $antes, $os1->data());
        $antes = clone $os3->data();

        if (!$os3->save()) {
            $fail = $os3->fail();
            $json['message'] = $this->message->warning("Erro ao salvar produto/material!")->render();
            echo json_encode($json);
            $os1->rollback();
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $os3->getEntity(), $os3->id, $antes, $os3->data());

        $os1->commit();

        $material = (new Materiais())->findById($os3->id_materiais);

        $os3->delete = url('ordens/excluir_material');
        $os3->crypt_id = ll_encode($os3->id);
        $os3->edit = url("ordens/materiais");
        $os3->descricao = $material->descricao;
        $os3->unidade = $material->unidade;
        $os3Data = objectsToArray($os3);

        $json['status'] = "success";
        if (ll_intValida($id)) {
            $json['message'] = $this->message->success("Produto/Material alterado com sucesso!")->render();
        } else {
            $json['message'] = $this->message->success("Produto/Material adicionado com sucesso!")->render();
        }

        $json['id'] = $os3->id;
        $json['tarefaId'] = $os3->id_os2;
        $json['aditivo'] = $os2->aditivo;
        $json['material'] = $os3Data;

        $totalMateriais = (new Os3())->find(
            "id_os2 = :id_os2",
            "id_os2={$os3->id_os2}",
            "SUM(vtotal) as vtotal"
        )->fetch();

        $json['soma_vtotal'] = $totalMateriais->vtotal;

        echo json_encode($json);
        return;
    }

    public function excluirMaterial($data): void
    {
        $id_os3 = ll_decode($data['id']);

        if (ll_intValida($id_os3)) {
            $os3 = (new Os3())->findById($id_os3);
            $os2 = (new OS2())->findById($os3->id_os2);
            $antes = clone $os3->data();

            if (!$os3->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $os3->getEntity(), $os3->id, $antes, null);

            $json['status'] = "success";
            $json['tarefaId'] = $os2->id;
            $json['aditivo'] = $os2->aditivo;
            $json['message'] = $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->render();

            $totalMateriais = (new Os3())->find(
                "id_os2 = :id_os2",
                "id_os2={$os3->id_os2}",
                "SUM(vtotal) as vtotal"
            )->fetch();

            $json['soma_vtotal'] = !empty($totalMateriais) ? $totalMateriais->vtotal : 0;
            echo json_encode($json);
        }
    }


    public function verificaEquipamentos($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $tarefa = (new Os2())->findById($data['id_tarefa']);
        $equipamentos = (new Os2_2())->findByOs2($data['id_tarefa']);

        if (!empty($equipamentos)) {
            foreach ($equipamentos as $os) {
                $os->delete = url('ordens/excluir_equipamento');
                $os->crypt_id = ll_encode($os->id);
                $os->edit = url("ordens/equipamentos");
                $os->descricao = (new Equipamentos())->findById($os->id_equipamentos)->descricao;

                $checklist = (new Os2_2_1())->find(
                    "id_os2 = :id_os2 AND id_os2_2 = :id_os2_2",
                    "id_os2={$tarefa->id}&id_os2_2={$os->id}"
                )->fetch(true);

                $chkitens = (new Equipamentos())->findById($os->id_equipamentos)->id_chkitens;
                //var_dump($chkitens);

                $temchkitens = false;
                if (!empty($chkitens)) {
                    $temchkitens = true;
                }
                $os->temchkitens = $temchkitens;

                $temchk = false;
                if (!empty($checklist)) {
                    foreach ($checklist as $item) {
                        if ((!empty($item->status) && $item->status !== null) || (!empty($item->obs) && $item->obs !== null)) {
                            $temchk = true;
                            break;
                        }
                    }
                }
                $os->temchk = $temchk;
            }

            $equipamentosData = objectsToArray($equipamentos);
            $json['status'] = "success";
            $json['servico'] = (new Servico())->findById($tarefa->id_servico)->nome;
            $json['tarefaId'] = $data['id_tarefa'];
            $json['equipamentos'] = $equipamentosData;
        } else {
            $json['servico'] = (new Servico())->findById($tarefa->id_servico)->nome;
            $json['status'] = "error";
        }
        echo json_encode($json);
        return;
    }

    /**
     * Converte string de ids de checklist em array de inteiros válidos
     * @param string|null $idsString
     * @return array<int>
     */
    private function parseChecklistIds(?string $idsString): array
    {
        if (empty($idsString)) {
            return [];
        }

        $ids = array_map('trim', explode(',', $idsString));

        $validIds = array_filter($ids, function ($id) {
            return ll_intValida($id);
        });

        return array_map('intval', $validIds);
    }

    /**
     * Sincroniza os itens do checklist (os2_2_1) com o cadastro atual do equipamento
     * 
     * @param int $id_os2
     * @param int $id_os2_2
     * @param string|null $id_chkitensAtual
     * @return array<string, array> // retorna listas de itens novos criados e itens que sobraram
     */
    function syncChecklistItens(int $id_os2, int $id_os2_2, ?string $id_chkitensAtual): array
    {
        // Passo 1: Obter os ids do checklist atual (do cadastro do equipamento)
        $chkItensIdsAtual = $this->parseChecklistIds($id_chkitensAtual);

        // Passo 2: Obter os ids já lançados em os2_2_1
        $chkItensLancados = (new Os2_2_1())->find(
            "id_os2 = :id_os2 AND id_os2_2 = :id_os2_2",
            "id_os2={$id_os2}&id_os2_2={$id_os2_2}"
        )->fetch(true); // fetch(true) → retorna array de registros

        // Mapear os ids já lançados
        $chkItensLancadosIds = [];
        if ($chkItensLancados) {
            foreach ($chkItensLancados as $item) {
                $chkItensLancadosIds[] = (int) $item->id_chkitens;
            }
        }

        // Passo 3: Verificar itens que precisam ser criados
        $itensNovos = array_diff($chkItensIdsAtual, $chkItensLancadosIds);

        foreach ($itensNovos as $chkitemId) {
            // Cria novo registro em os2_2_1
            $os2_2_1 = new Os2_2_1();
            $os2_2_1->id_emp2 = $this->user->id_emp2; // Usando id_emp2 do usuário
            $os2_2_1->id_os2 = $id_os2;
            $os2_2_1->id_os2_2 = $id_os2_2;
            $os2_2_1->id_chkitens = $chkitemId;
            $os2_2_1->status = null;
            $os2_2_1->obs = '';
            $os2_2_1->id_users = $this->user->id; // Usando id do usuário logado

            if (!$os2_2_1->save()) {
                $fail = $os2_2_1->fail();
                $json['message'] = $this->message->warning("Erro ao salvar item de checklist: {$fail}")->render();
                echo json_encode($json);
                return [];
            }

            $log = new Log();
            $log->registrarLog("C", $os2_2_1->getEntity(), $os2_2_1->id, "Item criado via função syncChecklistItens()", $os2_2_1->data());
        }

        // Passo 4: Verificar itens que sobraram (estão na os2_2_1 mas não mais no cadastro)
        $itensSobrando = array_diff($chkItensLancadosIds, $chkItensIdsAtual);

        foreach ($itensSobrando as $chkitemIdSobrando) {
            // Buscar o registro completo em os2_2_1
            $itemSobrando = (new Os2_2_1())->find(
                "id_os2 = :id_os2 AND id_os2_2 = :id_os2_2 AND id_chkitens = :id_chkitens",
                "id_os2={$id_os2}&id_os2_2={$id_os2_2}&id_chkitens={$chkitemIdSobrando}"
            )->fetch();

            if ($itemSobrando) {
                $temStatus = !is_null($itemSobrando->status) && $itemSobrando->status != 0;
                $temObs = !empty(trim($itemSobrando->obs));

                if (!$temStatus && !$temObs) {
                    // OK para deletar (usuário ainda não preencheu nada)
                    $antes = clone $itemSobrando->data();
                    $itemSobrando->destroy();

                    // Log da exclusão
                    $log = new Log();
                    $log->registrarLog(
                        "D",
                        $itemSobrando->getEntity(),
                        $itemSobrando->id,
                        $antes,
                        "Item de checklist removido automaticamente na função syncChecklistItens() pois não havia interação do usuário."
                    );
                }
            }
        }

        // Retornar um array com o que foi feito
        return [
            'novos_criados' => $itensNovos,
            'itens_sobrando' => $itensSobrando, // você decide depois o que fazer (ex: perguntar pro usuário)
        ];
    }

    public function checklist($data)
    {
        $id_os2 = $_GET['id_os2'] ?? null;
        $id_os2_2 = $_GET['id_os2_2'] ?? null;

        if (!ll_intValida($id_os2) || !ll_intValida($id_os2_2)) {
            $json['message'] = $this->message->error("ID inválido!")->render();
            echo json_encode($json);
            return;
        }

        $os2_2 = (new Os2_2())->findById($id_os2_2);
        $chkAtuais = (new Equipamentos())->findById($os2_2->id_equipamentos)->id_chkitens;

        $diff = $this->syncChecklistItens($id_os2, $id_os2_2, $chkAtuais);

        $chkItens = (new Os2_2_1())->find(
            "id_os2 = :id_os2 AND id_os2_2 = :id_os2_2",
            "id_os2={$id_os2}&id_os2_2={$id_os2_2}"
        )->fetch(true);

        if (!empty($chkItens)) {
            foreach ($chkItens as $item) {
                $item->descricao = (new ChkItem())->findById($item->id_chkitens)->descricao;
                $item->grupo = (new ChkItem())->findById($item->id_chkitens)->id_chkgrupo;
                $item->descGrupo = (new ChkGrupo())->findById($item->grupo)->descricao;
            }
        }

        echo $this->view->render("tcsistemas.os/ordens/ordensChecklistContent", [
            "id_os2" => $id_os2,
            "id_os2_2" => $id_os2_2,
            "chkItens" => $chkItens,
            "novos_criados" => $diff['novos_criados'],
            "itens_sobrando" => $diff['itens_sobrando'],
            "obs" => trim($os2_2->chkobs)
        ]);
    }

    private function limparPdfsAntigos()
    {
        $arquivos = glob(CONF_FILES_PATH . "ordem-servico-*.pdf");
        $agora = time();

        foreach ($arquivos as $arquivo) {
            if (file_exists($arquivo) && ($agora - filemtime($arquivo)) > 3600) { // 1 hora
                unlink($arquivo);
            }
        }
    }

    public function checklistPdf($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $id_os2_2 = $data['idos2_2'];

        $os2_2 = (new Os2_2())->findById($id_os2_2);
        $equipamento = (new Equipamentos())->findById($os2_2->id_equipamentos);

        $chkItens = (new Os2_2_1())->find(
            "id_os2_2 = :id_os2_2",
            "id_os2_2={$id_os2_2}"
        )->fetch(true);

        if (!empty($chkItens)) {
            foreach ($chkItens as $item) {
                $item->descricao = (new ChkItem())->findById($item->id_chkitens)->descricao;
                $item->grupo = (new ChkItem())->findById($item->id_chkitens)->id_chkgrupo;
                $item->descGrupo = (new ChkGrupo())->findById($item->grupo)->descricao;
            }
        }

        $html = $this->view->render("tcsistemas.os/ordens/ordensPdfChk", [
            "emp" => $empresa,
            "chkItens" => $chkItens,
            "os2_2" => $os2_2,
            "equipamento" => $equipamento
        ]);

        $textoRodape = "Gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        $nomeUnico = "ordem-servico-{$id_user}-" . time() . "-" . uniqid();

        //echo json_encode($html);        
        $pdf = ll_pdfGerar($html, $nomeUnico, "R", "S", CONF_FILES_PATH, $textoRodape);

        if ($pdf) {

            $this->limparPdfsAntigos(); // Limpa PDFs antigos antes de salvar o novo

            echo json_encode([
                'success' => true,
                'pdf_url' => CONF_FILES_URL . $nomeUnico . '.pdf'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $this->message->error("Erro ao gerar PDF!")->render()
            ]);
        }
    }

    public function salvarChecklist($data)
    {
        $checklistUpdates = [];

        $temchk = false;
        $os2_2 = "";

        if (!empty($data['chkobs'])) {
            $temchk = true; // Marca que pelo menos um item tem observação preenchida

            if (!str_verify($data['chkobs'])) {
                $json['message'] = $this->message->warning("Caracteres inválidos para o campo 'Observações Gerais'!")->render();
                echo json_encode($json);
                return;
            }
        }

        foreach ($data as $key => $value) {
            if (strpos($key, 'checklist_') === 0) {
                // Extrair ID e campo (status ou obs)
                $parts = explode('_', $key);
                if (count($parts) >= 3) {
                    $id = $parts[1];
                    $field = $parts[2];

                    // Agrupar por ID
                    if (!isset($checklistUpdates[$id])) {
                        $checklistUpdates[$id] = [];
                    }

                    $checklistUpdates[$id][$field] = $value;
                }
            }
        }

        foreach ($checklistUpdates as $checklistId => $fields) {

            $status = null;
            if (isset($fields['status'])) {
                if (!empty($fields['status'])) {
                    $temchk = true; // Marca que pelo menos um item tem status preenchido
                }
                $status = $fields['status'];
            }

            $obs = null;
            if (isset($fields['obs'])) {
                if (!empty($fields['obs'])) {
                    $temchk = true; // Marca que pelo menos um item tem observação preenchida
                }
                $obs = $fields['obs'];
            }

            $os2_2_1 = (new Os2_2_1())->findById($checklistId);

            if (!$os2_2_1) {
                $json['message'] = $this->message->error("Item de checklist não encontrado!")->render();
                echo json_encode($json);
                return;
            }

            $antes = clone $os2_2_1->data();
            $os2_2_1->id_users = $this->user->id; // Atualiza o usuário que fez a alteração
            $os2_2_1->status = isset($status) ? $status : null; // Atualiza o status se fornecido
            $os2_2_1->obs = isset($obs) ? $obs : null; // Atualiza a observação se fornecida

            if (!$os2_2_1->save()) {
                $json['message'] = $this->message->error("Erro ao salvar checklist: {$os2_2_1->fail()}")->render();
                echo json_encode($json);
                return;
            }

            $log = new Log();
            $log->registrarLog("U", $os2_2_1->getEntity(), $os2_2_1->id, $antes, $os2_2_1->data());
            $os2_2 = $os2_2_1->id_os2_2; // Guarda o id do os2_2 para uso posterior
        }

        $eqpOs2_2 = (new Os2_2())->findById($os2_2);

        if (!empty($eqpOs2_2)) {
            $eqpOs2Antes = clone $eqpOs2_2->data();
            $eqpOs2_2->id_users = $this->user->id; // Atualiza o usuário que fez a alteração

            $eqpOs2_2->chkobs = !empty($data['chkobs']) ? trim($data['chkobs']) : null;

            if (!$eqpOs2_2->save()) {
                $json['message'] = $this->message->error("Erro ao salvar checklist: {$eqpOs2_2->fail()}")->render();
                echo json_encode($json);
                return;
            }

            $log = new Log();
            $log->registrarLog("U", $eqpOs2_2->getEntity(), $eqpOs2_2->id, $eqpOs2Antes, $eqpOs2_2->data());
        }


        $json['os2_2'] = $os2_2; // Retorna o id do os2_2 atualizado
        $json['temchk'] = $temchk; // Indica se pelo menos um item foi preenchido
        $json['chksave'] = true; // Indica que o checklist foi salvo
        $json['message'] = $this->message->success("Checklist salvo com sucesso!")->render();
        echo json_encode($json);
    }

    public function equipamentos($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $id_equipamento = isset($data['id_equipamento']) ? ll_decode($data['id_equipamento']) : ""; // id_equipamento só vem em novo cadastro
        $id = isset($data['id']) ? ll_decode($data['id']) : ""; // id só vem em edição

        if (!ll_intValida($id)) { // pra edição não preciso do id_equipamento, então se entrar nesse laço significa que é um novo cadastro, e verifico se o equipamento está preenchido
            if (empty($data['id_equipamento'])) {
                $json['message'] = $this->message->warning("Selecione um equipamento!")->render();
                echo json_encode($json);
                return;
            }
        }

        // NOVA VALIDAÇÃO: Verificar se equipamento já existe para esta tarefa
        $equipExistente = (new Os2_2())->find(
            "id_os2 = :id_os2 AND id_equipamentos = :id_equipamentos",
            "id_os2={$data['id_tarefa']}&id_equipamentos={$id_equipamento}"
        )->fetch();

        if ($equipExistente) {
            $equipamento = (new Equipamentos())->findById($id_equipamento);
            $json['message'] = $this->message->warning("O equipamento '{$equipamento->descricao}' já foi lançado para esta tarefa. Edite o lançamento existente!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['qtde'])) {
            if (ll_intValida($data['qtde'])) {
                $quantidade = (int) $data['qtde']; // usa cast direto
            } else {
                $json['message'] = $this->message->warning("Quantidade inválida!")->render();
                echo json_encode($json);
                return;
            }
        } else {
            $json['message'] = $this->message->warning("Preencha a quantidade!")->render();
            echo json_encode($json);
            return;
        }


        if (ll_intValida($id)) {
            $os2_2 = (new Os2_2())->findById($id);
            $antes = clone $os2_2->data();
            $acao = "U";
        } else {
            $os2_2 = new Os2_2();
            $acao = "C";
            $antes = null;
        }

        $os2 = (new Os2())->findById($data['id_tarefa']);

        $os2_2->id_emp2 = $id_empresa;
        $os2_2->id_os2 = $data['id_tarefa'];
        $os2_2->id_equipamentos = isset($data['id_equipamento']) ? ll_decode($data['id_equipamento']) : $os2_2->id_equipamentos;
        $os2_2->qtde = $quantidade;
        $os2_2->id_users = $id_user;

        $os2_2->beginTransaction();
        if (!$os2_2->save()) {
            //$fail = $os2_2->fail();
            //var_dump($fail);
            $json['message'] = $this->message->warning("Erro ao salvar equipamento!")->render();
            echo json_encode($json);
            $os2_2->rollback();
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $os2_2->getEntity(), $os2_2->id, $antes, $os2_2->data());

        $equipamentos = (new Equipamentos())->findById($os2_2->id_equipamentos);

        // CRIA OS ITENS DO CHECKLIST ASSOCIADOS AO EQUIPAMENTO APENAS NA HORA DO LANÇAMENTO
        if (!ll_intValida($id)) { //ou seja, se $id não for válido, significa que é um novo cadastro
            if (!empty($equipamentos->id_chkitens)) {
                // Verifica se o equipamento possui checklist associado
                $checklistIds = $this->parseChecklistIds($equipamentos->id_chkitens);

                if (!empty($checklistIds)) {
                    foreach ($checklistIds as $checklistId) {
                        // Criar registro de checklist para cada item
                        $os2_2_1 = new Os2_2_1();
                        $os2_2_1->id_emp2 = $id_empresa;
                        $os2_2_1->id_os2 = $os2->id;
                        $os2_2_1->id_os2_2 = $os2_2->id;
                        $os2_2_1->id_chkitens = $checklistId;
                        $os2_2_1->status = null; // Inicializa como null
                        $os2_2_1->obs = ''; // Inicializa como vazio
                        $os2_2_1->id_users = $id_user;

                        if (!$os2_2_1->save()) {
                            error_log("Erro ao criar checklist item: " . $os2_2_1->fail());
                            $json['message'] = $this->message->warning("Erro ao criar itens do checklist!")->render();
                            echo json_encode($json);
                            $os2_2->rollback();
                            return;
                        }

                        $log = new Log();
                        $log->registrarLog("C", $os2_2_1->getEntity(), $os2_2_1->id, null, $os2_2_1->data());
                    }
                }

                $os2_2->commit();
            } else {
                // Se não há checklist associado, apenas comita a transação
                $os2_2->commit();
            }
        } else {
            $os2_2->commit();
        }

        $chkitens = (new Equipamentos())->findById($os2_2->id_equipamentos)->id_chkitens;
        //var_dump($chkitens);

        $temchkitens = false;
        if (!empty($chkitens)) {
            $temchkitens = true;
        }
        $os2_2->temchkitens = $temchkitens;

        $os2_2->delete = url('ordens/excluir_equipamento');
        $os2_2->crypt_id = ll_encode($os2_2->id);
        $os2_2->edit = url("ordens/equipamentos");
        $os2_2->descricao = $equipamentos->descricao;
        $os2_2Data = objectsToArray($os2_2);

        $json['status'] = "success";
        if (ll_intValida($id)) {
            $json['message'] = $this->message->success("Equipamento alterado com sucesso!")->render();
        } else {
            $json['message'] = $this->message->success("Equipamento adicionado com sucesso!")->render();
        }

        $json['id'] = $os2_2->id;
        $json['tarefaId'] = $os2_2->id_os2;
        $json['aditivo'] = $os2->aditivo;
        $json['equipamentos'] = $os2_2Data;

        echo json_encode($json);
        return;
    }


    public function excluirEquipamento($data): void
    {
        $id_os2_2 = ll_decode($data['id']);

        if (ll_intValida($id_os2_2)) {
            $os2_2 = (new Os2_2())->findById($id_os2_2);
            $os2 = (new OS2())->findById($os2_2->id_os2);
            $antes = clone $os2_2->data();

            $checklist = (new Os2_2_1())->find(
                "id_os2_2 = :id_os2_2",
                "id_os2_2={$id_os2_2}"
            )->fetch(true);

            // Excluir os itens de checklist associados
            if ($checklist) {
                foreach ($checklist as $item) {
                    $antesItem = clone $item->data();
                    $acao = "D";
                    if (!$item->destroy()) {
                        error_log("Erro ao excluir item de checklist: " . $item->fail());
                    }
                    $logItem = new Log();
                    $logItem->registrarLog($acao, $item->getEntity(), $item->id, $antesItem, "excluído via exclusão do equipamento - função excluirEquipamento()");
                }
            }

            if (!$os2_2->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $os2_2->getEntity(), $os2_2->id, $antes, null);

            $json['status'] = "success";
            $json['tarefaId'] = $os2->id;
            $json['aditivo'] = $os2->aditivo;
            $json['message'] = $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->render();

            echo json_encode($json);
        }
    }

    public function verificaChecklist($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id = ll_decode($data['id']);

        $checklist = (new Os2_2_1())->find(
            "id_os2_2 = :id_os2_2",
            "id_os2_2={$id}"
        )->fetch(true);

        // Se não tem itens, retorna false direto
        if (!$checklist) {
            echo json_encode(false);
            return;
        }

        // Percorre cada item

        foreach ($checklist as $item) {
            // Acessa o objeto data
            $dataObj = $item->data();

            // Verifica se status e obs têm valor
            if (
                (!empty($dataObj->status) && trim($dataObj->status) !== '') &&
                (!empty($dataObj->obs) && trim($dataObj->obs) !== '')
            ) {
                // Se pelo menos um item tiver os dois preenchidos, retorna true
                echo json_encode(true);
                return;
            }
        }


        // Se chegou aqui, nenhum item tinha os dois preenchidos
        echo json_encode(false);
    }


    public function statusTarefa($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $empresa = (new Emp2())->findById($id_empresa);

        $id_os2 = $data['id_tarefa'];
        $status = $data['status'];

        $os2 = (new OS2())->findById($id_os2);
        $antes = clone $os2->data();
        $acao = "U";

        if (($status == 'I' || $status == 'P')) {
            if (empty($os2->id_colaborador)) {
                $json['message'] = $this->message->warning("Selecione um operador!")->render();
                echo json_encode($json);
                return;
            }
        }

        if ($empresa->bloqueia2tarefasPorOper == 'X') {
            if ($status == 'I') {
                $tarefaExistente = (new Os2())->find(
                    "id_colaborador = :id_colaborador AND status = :status AND id != :id",
                    "id_colaborador={$os2->id_colaborador}&status=I&id={$id_os2}"
                )->fetch();

                if ($tarefaExistente) {
                    $json['message'] = $this->message->warning("Já existe uma tarefa em andamento para este operador! OS {$tarefaExistente->id_os1}, tarefa #{$tarefaExistente->id}")->render();
                    echo json_encode($json);
                    return;
                }
            }
        }

        if ($status == 'I') {
            $os5Pausada = (new Os5())->find(
                "id_os1 = :id_os1 AND id_os2 = :id_os2 AND status = :status AND dhf IS NULL",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=P"
            )->fetch();

            if (!empty($os5Pausada)) {
                $antesOs5 = clone $os5Pausada->data();
                $acaoOs5 = "U";
                $os5Pausada->dhf = date("Y-m-d H:i:s");
                $tempo = strtotime($os5Pausada->dhf) - strtotime($os5Pausada->dhi);
                $os5Pausada->tempo = gmdate("H:i:s", $tempo);
                $os5Pausada->id_users = $id_user;
                $os5Pausada->save();
                $logOs5 = new Log();
                $logOs5->registrarLog($acaoOs5, $os5Pausada->getEntity(), $os5Pausada->id, $antesOs5, $os5Pausada->data());
            }

            $os5 = new Os5();
            $os5antes = null;
            $acaoOs5 = "C";
            $os5->id_emp2 = $id_empresa;
            $os5->id_os1 = $os2->id_os1;
            $os5->id_os2 = $os2->id;
            $os5->dhi = date("Y-m-d H:i:s");
            $os5->status = 'T';
            $os5->id_users = $id_user;
            if (!$os5->save()) {
                $json['message'] = $this->message->error("ERRO AO INICIAR TAREFA!")->render();
                echo json_encode($json);
                return;
            }
            $logOs5 = new Log();
            $logOs5->registrarLog($acaoOs5, $os5->getEntity(), $os5->id, $os5antes, $os5->data());
        } else if ($status == 'P') {
            $os5andamento = (new Os5())->find(
                "id_os1 = :id_os1 AND id_os2 = :id_os2 AND status = :status AND dhf IS NULL",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=T"
            )->fetch();

            if (!empty($os5andamento)) {
                $antesOs5 = clone $os5andamento->data();
                $acaoOs5 = "U";
                $os5andamento->dhf = date("Y-m-d H:i:s");
                $tempo = strtotime($os5andamento->dhf) - strtotime($os5andamento->dhi);
                $os5andamento->tempo = gmdate("H:i:s", $tempo);
                $os5andamento->id_users = $id_user;
                $os5andamento->save();
                $logOs5 = new Log();
                $logOs5->registrarLog($acaoOs5, $os5andamento->getEntity(), $os5andamento->id, $antesOs5, $os5andamento->data());
            } else {
                $json['message'] = $this->message->error("ERRO AO PAUSAR A TAREFA! INFORME O SUPORTE.(ERR1001)")->render();
                echo json_encode($json);
                return;
            }

            $os5 = new Os5();
            $os5antes = null;
            $acaoOs5 = "C";
            $os5->id_emp2 = $id_empresa;
            $os5->id_os1 = $os2->id_os1;
            $os5->id_os2 = $os2->id;
            $os5->dhi = date("Y-m-d H:i:s");
            $os5->status = 'P';
            $os5->id_users = $id_user;
            if (!$os5->save()) {
                $json['message'] = $this->message->error("ERRO AO PAUSAR TAREFA! INFORME O SUPORTE.(ERR1002)")->render();
                echo json_encode($json);
                return;
            }
            $logOs5 = new Log();
            $logOs5->registrarLog($acaoOs5, $os5->getEntity(), $os5->id, $os5antes, $os5->data());
        } else if ($status == 'C' || $status == 'D') {

            $os5andamento = (new Os5())->find(
                "id_os1 = :id_os1 AND id_os2 = :id_os2 AND status = :status AND dhf IS NULL",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=T"
            )->fetch();

            $os5pausada = (new Os5())->find(
                "id_os1 = :id_os1 AND id_os2 = :id_os2 AND status = :status AND dhf IS NULL",
                "id_os1={$os2->id_os1}&id_os2={$os2->id}&status=P"
            )->fetch();

            $os5atualizar = null;
            if (!empty($os5andamento)) {
                $os5atualizar = $os5andamento;
            } else if (!empty($os5pausada)) {
                $os5atualizar = $os5pausada;
            }

            if (!empty($os5atualizar)) {
                $antesOs5 = clone $os5atualizar->data();
                $acaoOs5 = "U";
                $os5atualizar->dhf = date("Y-m-d H:i:s");
                $tempo = strtotime($os5atualizar->dhf) - strtotime($os5atualizar->dhi);
                $os5atualizar->tempo = gmdate("H:i:s", $tempo);
                $os5atualizar->id_users = $id_user;
                $os5atualizar->save();
                $logOs5 = new Log();
                $logOs5->registrarLog($acaoOs5, $os5atualizar->getEntity(), $os5andamento->id, $antesOs5, $os5andamento->data());
            }
        }

        $os2->status = $status;
        $os2->id_users = $id_user;

        if (!$os2->save()) {
            $json['message'] = $this->message->error("ERRO AO ATUALIZAR STATUS!")->render();
            echo json_encode($json);
        }

        $os1 = (new Os1())->findById($os2->id_os1);
        if ($status == "I" && $os1->id_status == 2) {
            $os1Antes = clone $os1->data();
            $os1Acao = "U";

            $os1->id_status = 3;
            $os1->save();

            $logOs1 = new Log();
            $logOs1->registrarLog($os1Acao, $os1->getEntity(), $os1->id, $os1Antes, $os1->data());
        }

        if ($status == "C" || $status == "D") {
            $tarefas = (new Os2())->findByIdOs($os2->id_os1);

            $todasConcluidas = true;
            foreach ($tarefas as $tarefa) {
                if ($tarefa->status != "C" && $tarefa->status != "D") {
                    $todasConcluidas = false;
                    break;
                }
            }
            if ($todasConcluidas) {
                $os1Antes = clone $os1->data();
                $os1Acao = "U";

                $os1->concluir = 'S';
                $os1->save();

                $logOs1 = new Log();
                $logOs1->registrarLog($os1Acao, $os1->getEntity(), $os1->id, $os1Antes, $os1->data());
            }
        }

        $log = new Log();
        $log->registrarLog($acao, $os2->getEntity(), $os2->id, $antes, $os2->data());
        $this->message->success("STATUS ATUALIZADO COM SUCESSO")->flash();
        $json['reload'] = true;

        echo json_encode($json);
        return;
    }


    public function excluir($data): void
    {
        $id_ordens = ll_decode($data['id_ordens']);

        if (ll_intValida($id_ordens)) {
            $ordens = (new Os1())->findById($id_ordens);

            $tarefa = (new OS2())->findByIdOs($id_ordens);
            $material = (new Os3())->findByIdOs($id_ordens);
            $medicoes = (new Os2_1())->findByIdOs($id_ordens);
            $historico = (new Os5())->findByIdOs($id_ordens);


            if (!empty($historico)) {
                foreach ($historico as $os5) {
                    $os5->destroy();
                }
            }

            if (!empty($medicoes)) {
                foreach ($medicoes as $os4) {
                    $os4->destroy();
                }
            }

            if (!empty($tarefa)) {
                foreach ($tarefa as $os2) {
                    $os2->destroy();
                }
            }

            if (!empty($material)) {
                foreach ($material as $os3) {
                    $os3->destroy();
                }
            }

            $antes = clone $ordens->data();

            if (!$ordens->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $ordens->getEntity(), $ordens->id, $antes, null);

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
