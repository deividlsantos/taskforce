<?php

namespace Source\Controllers;

use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Obras;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Servico;
use Source\Models\Status;

class RelatoriosServicosController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user) {
            $this->message->error("Para acessar é preciso logar-se")->flash();
            redirect("");
        }
    }

    public function index(): void
    {
        $servicos = (new Servico())->find()->fetch(true);
        $status = (new Status())->find(null, null, "*", false)->fetch(true);
        $empresa = (new Emp2())->findById($this->user->id_emp2);
        $clientes = (new Ent())->find("tipo = :tipo", "tipo=1")->fetch(true);

        $front = [
            "titulo" => "Relatório de Serviços - Task Force",
            "user" => $this->user,
            "secTit" => "Relatório de Serviços"
        ];

        echo $this->view->render("tcsistemas.os/relatorios/servicos-rel", [
            "front" => $front,
            "servicos" => $servicos,
            "status" => $status,
            "clientes" => $clientes,
            "emp" => $empresa
        ]);
    }

    private function consultaRelatorio($data)
    {
        $cliente = $data['cliente'];
        $servico = $data['servico'];
        $status = $data['status'];
        $datai = $data['datai'] ?? null;
        $dataf = $data['dataf'] ?? null;
        $statusConcluido = $data['chk-concluidas'] ?? false;
        $statusCancelado = $data['chk-canceladas'] ?? false;
        $order1 = $data['order1'];
        $sort1 = $data['sort1'];
        $order2 = $data['order2'];
        $sort2 = $data['sort2'];
        $order3 = $data['order3'];
        $sort3 = $data['sort3'];

        $empresa_id = $this->user->id_emp2;

        // Verifico se as datas estão preenchidas corretamente
        if (($datai && !$dataf) || (!$datai && $dataf)) {
            $json['message'] = "Informe a data inicial e a data final, ou nenhuma data para buscar todo o período!";
            $json['error'] = true;
            return $json;
        }

        if ($datai && $dataf && strtotime($datai) > strtotime($dataf)) {
            $json['message'] = "A data inicial não pode ser maior que a data final!";
            $json['error'] = true;
            return $json;
        }

        // Formato as datas, se informadas
        $datas = false;
        if ($datai && $dataf) {
            $datai = date("Y-m-d", strtotime($datai));
            $dataf = date("Y-m-d", strtotime($dataf));
            $datas = true;
            $queryDate = "dataexec BETWEEN :datai AND :dataf";
            $paramsDate = "datai={$datai}&dataf={$dataf}";
        }

        // Montagem da consulta SQL
        $query = [];
        $params = [];

        $tabelaEnt = (new Ent())->getEntity();
        $tabelaServico = (new Servico())->getEntity();
        $tabelaOs1 = (new Os1())->getEntity();
        $tabelaSegmentos = (new Obras())->getEntity();

        $query[] = "{$tabelaOs1}.id_cli = :id_cli";
        $params[] = "id_cli={$cliente}";

        if ($servico != "todos") {
            $query[] = "id_servico = :id_servico";
            $params[] = "id_servico={$servico}";
        }

        if ($status != "todos") {
            $query[] = "os2.status = :status";
            $params[] = "status={$status}";
        } else {
            $statusConditions = [];
            if (!$statusConcluido) {
                $statusConditions[] = "'C'";
            }
            if (!$statusCancelado) {
                $statusConditions[] = "'D'";
            }
            if (!empty($statusConditions)) {
                $query[] = "os2.status NOT IN (" . implode(',', $statusConditions) . ")";
            }
        }

        $periodo = "";
        if ($datas) {
            $query[] = $queryDate;
            $params[] = $paramsDate;
            $periodo = " de " . date_fmt($datai, "d/m/Y") . " até " . date_fmt($dataf, "d/m/Y");
        }

        // Adiciona a condição obrigatória para id_emp2
        $query[] = "os2.id_emp2 = :id_emp2";
        $params[] = "id_emp2={$empresa_id}";

        // Combinação final da consulta
        $finalQuery = implode(' AND ', $query);
        $finalParams = implode('&', $params);

        // instância da tabela os2
        $os2 = new Os2();

        $os2->join($tabelaEnt, "{$tabelaEnt}.id = os2.id_colaborador", "LEFT")
            ->join($tabelaServico, "{$tabelaServico}.id = os2.id_servico", "LEFT")
            ->join($tabelaOs1, "{$tabelaOs1}.id = os2.id_os1", "LEFT")
            ->join($tabelaSegmentos, "{$tabelaSegmentos}.id = {$tabelaOs1}.id_obras", "LEFT");

        // Adiciona ordenação dinâmica
        $orderBy = [];
        if ($order1 && $sort1) {
            $orderBy[] = $this->mapOrderColumn($order1) . " {$sort1}";
        }
        if ($order2 && $sort2) {
            $orderBy[] = $this->mapOrderColumn($order2) . " {$sort2}";
        }
        if ($order3 && $sort3) {
            $orderBy[] = $this->mapOrderColumn($order3) . " {$sort3}";
        }

        $total = 0;
        if (isset($data['limit']) && (int) $data['limit'] > 0) {
            $contagem = $os2->find($finalQuery, $finalParams, "COUNT(*) as total", false)->fetch(true);
            $total = $contagem[0]->data()->total;
            $limit = isset($data['limit']) ? (int) $data['limit'] : 15;
            $page = isset($data['page']) ? (int) $data['page'] : 1;
            $offset = ($page - 1) * $limit;
            // Adiciona paginação
            $os2->limit($limit)->offset($offset);
        }

        if (!empty($orderBy)) {
            $os2->order(implode(", ", $orderBy));
        }

        // Executa a consulta
        $resultados = $os2->find(
            $finalQuery,
            $finalParams,
            "os2.*, COALESCE({$tabelaEnt}.nome, '---') as colaborador,
        COALESCE({$tabelaServico}.nome, '---') as servico,
        COALESCE({$tabelaOs1}.controle, '---') as os1controle,
        COALESCE({$tabelaSegmentos}.nome, '---') as segmento_nome,
        COALESCE({$tabelaSegmentos}.controle, '---') as segmento_controle,
        COALESCE({$tabelaServico}.medida, '---') as medida
        ",
            false
        )->fetch(true);

        // Traduz os status no resultado
        if ($resultados) {
            foreach ($resultados as $item) {
                $item->status = $this->retornaStatus($item->status);
            }
        }

        return [
            'resultados' => $resultados,
            'total' => $total,
            'periodo' => $periodo
        ];
    }

    /**
     * Mapeia a coluna de ordenação para o nome correto no banco de dados.
     * @param mixed $column
     * @return string
     */
    private function mapOrderColumn($column)
    {
        $tabelaEnt = (new Ent())->getEntity();
        $tabelaServico = (new Servico())->getEntity();
        $tabelaOs1 = (new Os1())->getEntity();
        $tabelaSegmentos = (new Obras())->getEntity();

        $map = [
            'servico' => "{$tabelaServico}.nome",
            'id_os1' => "os2.id_os1",
            'ctrl_os1' => "{$tabelaOs1}.controle",
            'dataexec' => "os2.dataexec",
            'segmento' => "{$tabelaSegmentos}.nome",
            'ctrl_segmento' => "{$tabelaSegmentos}.controle",
            'colaborador' => "{$tabelaEnt}.nome",
            'id' => "os2.id",
            'status' => "FIELD(os2.status, 'A', 'I', 'P', 'C', 'D')",
            'vtotal' => "os2.vtotal"
        ];

        return $map[$column] ?? "os2.{$column}";
    }

    /**
     * @param [type] $status - A (AGUARDANDO INÍCIO), I (EM ANDAMENTO), P (PAUSADAS), C (CONCLUÍDAS), D (CANCELADAS)
     * @return void
     */
    private function retornaStatus($status)
    {
        switch ($status) {
            case 'A':
                return "AGUARDANDO INÍCIO";
            case 'I':
                return "EM ANDAMENTO";
            case 'P':
                return "PAUSADO";
            case 'C':
                return "CONCLUÍDO";
            case 'D':
                return "CANCELADO";
        }
    }

    public function retornaRelatorio($data)
    {
        if (empty($data['cliente'])) {
            $json['message'] = $this->message->warning("SELECIONE UM CLIENTE!")->render();
            echo json_encode($json);
            return;
        }

        $resultado = $this->consultaRelatorio($data);

        if (isset($resultado['error'])) {
            $json['message'] = $this->message->warning($resultado['message'])->render();
            echo json_encode($json);
            return;
        }

        $resultadoData = [];
        if (!empty($resultado['resultados'])) {
            $resultadoData = objectsToArray($resultado['resultados']);
        }

        $filtros = $data;
        $filtros['page'] = 0;
        $filtros['limit'] = 0;
        $filtros['url_pdf'] = url("servicosrel/pdfservicos");

        $filtrosPaginacao = $data;

        $json['total'] = $resultado['total'];
        $json['paginacao'] = $filtrosPaginacao;
        $json['servicosrel'] = true;
        $json['registros'] = $resultadoData;
        $json['filtros'] = $filtros;
        $json['periodo'] = $resultado['periodo'];

        echo json_encode($json);
    }

    public function pdfservicos($data)
    {
        $emp = (new Emp2())->findById($this->user->id_emp2);

        if (empty($data['dados'])) {
            $json['error'] = true;
            $json['message'] = $this->message->error("Sem resultados pro relatório!")->render();
            echo json_encode($json);
            return;
        }

        $cliente = (new Ent())->findById($data['dados']['cliente']);

        $resultado = $this->consultaRelatorio($data['dados']);

        //** CRIO UM ARRAY QUE VAI RECEBER TODOS OS COLABORADORES DISTINTOS */
        $servicos = [];

        if (!empty($resultado)) {
            foreach ($resultado['resultados'] as $os) {
                if (!in_array($os->data->id_servico, $servicos)) {
                    $servicos[] = $os->data->id_servico;
                }
            }
        }

        sort($servicos);

        // Remove valores nulos e não inteiros
        $servicos = array_map('intval', $servicos);
        $servicos = array_filter($servicos, function ($valor) {
            return $valor > 0;
        });

        // Verificação extra para evitar SQL com IN vazio
        if (empty($servicos)) {
            $json['error'] = true;
            $json['message'] = $this->message->error("Sem resultados pro relatório!")->render();
            echo json_encode($json);
            exit;
        }

        //** FAÇO UMA PESQUISA NO BANCO DIRETAMENTE NA TABELA DE COLABORADORES PRA TER ACESSO A TODAS SUAS INFORMAÇÕES */
        $srv = (new Servico())->find("id IN (" . implode(",", $servicos) . ")")->fetch(true);

        usort($srv, function ($a, $b) {
            return strcmp($a->nome, $b->nome);
        });

        $html = $this->view->render("tcsistemas.os/relatorios/servicos-pdf", [
            "emp" => $emp,
            "user" => $this->user,
            "dados" => $resultado['resultados'],
            "servico" => $srv,
            "periodo" => $resultado['periodo'],
            "cliente" => $cliente
        ]);

        $textoRodape = "Relatório gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //** COMO É UMA REQUISIÇÃO VIA POST, PRECISO SALVAR O PDF NUM LOCAL TEMPORÁRIO */
        $caminho = __DIR__ . "/../../storage/uploads/";

        //echo $html;
        //** E ASSIM, INFORMO O PARAMETRO 'S', E O CAMINHO */
        //$arquivo = ll_pdfGerar($html, "relatorio-servicos", "R", "S", $caminho, $textoRodape);

        //** SE O ARQUIVO FOI GERADO, RETORNO O CAMINHO DO ARQUIVO EM FORMATO JSON, E O MOTOR DATA-POST RENDERIZA O PDF */
        // if ($arquivo) {
        //     $json['file'] = url("storage/uploads/") . $arquivo;
        //     echo json_encode($json);
        // } else {
        //     $json['message'] = $this->message->error("Erro ao gerar o relatório!")->render();
        //     echo json_encode($json);
        // }

        // Retorna o HTML diretamente
        echo json_encode([
            'error' => false,
            'html' => $html
        ]);
    }

    public function pdfOs2b()
    {
        $html = $this->view->render("tcsistemas.os/relatorios/os2-list", []);

        $textoRodape = "Relatório gerado por {$this->user->nome} em " . date("d/m/Y H:i:s");

        //echo $html;
        ll_pdfGerar($html, "relatorio-medicao", "R", "P", "", $textoRodape);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
