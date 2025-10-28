<?php

namespace Source\Controllers;

use DateTime;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Obras;
use Source\Models\Oper;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Pag;
use Source\Models\PagSaldo;
use Source\Models\Rec;
use Source\Models\RecSaldo;
use Source\Models\Servico;
use Source\Models\Status;

class RelatoriosFinanceiroController extends Controller
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
            "titulo" => "Relatórios Financeiro - Taskforce",
            "user" => $this->user,
            "secTit" => "Relatórios Financeiro"
        ];

        echo $this->view->render("tcsistemas.financeiro/relatorios/relatorio-index", [
            "front" => $front
        ]);
    }

    /**
     * Mapeia a coluna de ordenação para o nome correto no banco de dados.
     * @param mixed $column
     * @return string
     */
    private function mapOrderColumn($column, $tabela)
    {
        $tabelaEnt = (new Ent())->getEntity();

        $tabela2 = $tabela;
        if ($tabela === 'pagsaldo') {
            $tabela2 = 'pag';
        } elseif ($tabela === 'recsaldo') {
            $tabela2 = 'rec';
        }

        $map = [
            'id_entf' => "{$tabelaEnt}.nome",
            'id_entc' => "{$tabelaEnt}.nome",
            'databaixa' => "{$tabela}.datapag",
            'dataven' => "{$tabela2}.dataven",
            'datacad' => "{$tabela2}.datacad",
            'titulo' => "{$tabela2}.titulo",
            'valor' => "{$tabela}.valor"
        ];

        return $map[$column] ?? "{$tabela}.{$column}";
    }

    private function consultaRelatorio($data)
    {
        $fperiodo = $data['filtro-periodo'];
        $status = $data['status'];
        $datai = $data['datai'] ?? null;
        $dataf = $data['dataf'] ?? null;
        $order1 = $data['order1'];
        $sort1 = $data['sort1'];
        $order2 = $data['order2'];
        $sort2 = $data['sort2'];
        $order3 = $data['order3'];
        $sort3 = $data['sort3'];

        if (isset($data['tabela']) && $data['tabela'] === 'pag') {
            if ($data['status'] == "aberto") {
                $tabela = 'pag';
                $instancia = new Pag();
            } else {
                $tabela = 'pagsaldo';
                $instancia = new PagSaldo();
            }
        } else {
            if ($data['status'] == "aberto") {
                $tabela = 'rec';
                $instancia = new Rec();
            } else {
                $tabela = 'recsaldo';
                $instancia = new RecSaldo();
            }
        }

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

        // Montagem da consulta SQL
        $query = [];
        $params = [];

        if ($data['status'] == 'baixado') {
            $colunaPeriodo = "datapag";
        } else {
            if ($fperiodo == "vencimento") {
                $colunaPeriodo = "dataven";
            } elseif ($fperiodo == "emissao") {
                $colunaPeriodo = "datacad";
            }

            $query[] = "{$tabela}.baixado = :baixado";
            $params[] = "baixado=N";
        }

        // Formato as datas, se informadas        
        if ($datai && $dataf) {
            $datai = date("Y-m-d", strtotime($datai));
            $dataf = date("Y-m-d", strtotime($dataf));
            $queryDate = "{$colunaPeriodo} BETWEEN :datai AND :dataf";
            $paramsDate = "datai={$datai}&dataf={$dataf}";
        }

        $tabelaEnt = (new Ent())->getEntity();

        $query[] = $queryDate;
        $params[] = $paramsDate;
        $periodo = " de " . date_fmt($datai, "d/m/Y") . " até " . date_fmt($dataf, "d/m/Y");


        // Adiciona a condição obrigatória para id_emp2
        $query[] = "{$tabela}.id_emp2 = :id_emp2";
        $params[] = "id_emp2={$empresa_id}";

        // Combinação final da consulta
        $finalQuery = implode(' AND ', $query);
        $finalParams = implode('&', $params);

        $entidade = "id_entc";
        $colunasAdicionais = "";
        if ($tabela === 'pag') {
            $entidade = "id_entf";
        } else if ($tabela === 'pagsaldo' || $tabela === 'recsaldo') {
            $entidade = "id_ent";
            if ($tabela === 'pagsaldo') {
                $instancia->join('pag', "pag.id = {$tabela}.id_pag", "LEFT");
                $colunasAdicionais = ",
                pag.titulo as titulo,
                pag.id_oper as id_oper,
                pag.datacad as datacad,
                pag.dataven as dataven,
                pag.databaixa as baixa,
                {$tabela}.datapag as databaixa";
            } else {
                $instancia->join('rec', "rec.id = {$tabela}.id_rec", "LEFT");
                $colunasAdicionais = ",
                rec.titulo as titulo,
                rec.id_oper as id_oper,
                rec.datacad as datacad,
                rec.dataven as dataven,
                {$tabela}.datapag as databaixa";
            }
        }

        $instancia->join($tabelaEnt, "{$tabelaEnt}.id = {$tabela}.{$entidade}", "LEFT");

        // Adiciona ordenação dinâmica
        $orderBy = [];
        if ($order1 && $sort1) {
            $orderBy[] = $this->mapOrderColumn($order1, $tabela) . " {$sort1}";
        }
        if ($order2 && $sort2) {
            $orderBy[] = $this->mapOrderColumn($order2, $tabela) . " {$sort2}";
        }
        if ($order3 && $sort3) {
            $orderBy[] = $this->mapOrderColumn($order3, $tabela) . " {$sort3}";
        }

        $total = 0;
        if (isset($data['limit']) && (int) $data['limit'] > 0) {
            $contagem = $instancia->find($finalQuery, $finalParams, "COUNT(*) as total", false)->fetch(true);
            $total = $contagem[0]->data()->total;
            $limit = isset($data['limit']) ? (int) $data['limit'] : 15;
            $page = isset($data['page']) ? (int) $data['page'] : 1;
            $offset = ($page - 1) * $limit;
            // Adiciona paginação
            $instancia->limit($limit)->offset($offset);
        }

        if (!empty($orderBy)) {
            $instancia->order(implode(", ", $orderBy));
        }

        // Executa a consulta
        $resultados = $instancia->find(
            $finalQuery,
            $finalParams,
            "{$tabela}.*, COALESCE({$tabelaEnt}.nome, '---') as entidade_nome
            {$colunasAdicionais}
        ",
            false
        )->fetch(true);

        if (!empty($resultados)) {
            foreach ($resultados as $item) {
                $item->tabela = $tabela;
                // Formata as datas
                $item->datacad = date_fmt($item->datacad, "d/m/Y");
                $item->dataven = date_fmt($item->dataven, "d/m/Y");
                $item->databaixa = $item->databaixa ? date_fmt($item->databaixa, "d/m/Y") : '';
            }
        }

        return [
            'resultados' => $resultados,
            'total' => $total,
            'periodo' => $periodo
        ];
    }

    public function pagar(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $front = [
            "titulo" => "Relatórios Contas a Pagar - Taskforce",
            "user" => $this->user,
            "secTit" => "Relatórios Contas a Pagar"
        ];

        echo $this->view->render("tcsistemas.financeiro/relatorios/financeiro-pagar", [
            "front" => $front
        ]);
    }

    public function receber(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $front = [
            "titulo" => "Relatórios Contas a Receber - Taskforce",
            "user" => $this->user,
            "secTit" => "Relatórios Contas a Receber"
        ];

        echo $this->view->render("tcsistemas.financeiro/relatorios/financeiro-receber", [
            "front" => $front
        ]);
    }

    public function retornaRelatorio($data)
    {
        $status = $data['status'] ?? null;
        $periodo = $data['filtro-periodo'] ?? null;

        // Lista de valores válidos
        $statusValidos  = ['baixado', 'aberto'];
        $periodoValidos = ['emissao', 'baixa', 'vencimento'];

        // 1) Verifica se os valores existem e são válidos
        if (!in_array($status, $statusValidos, true)) {
            $json['message'] = $this->message->warning("Valor do campo 'STATUS' inválido!")->render();
            echo json_encode($json);
            return;
        }

        if (!in_array($periodo, $periodoValidos, true)) {
            $json['message'] = $this->message->warning("Valor do campo 'PERÍODO POR' inválido!")->render();
            echo json_encode($json);
            return;
        }

        // 2) Regras de consistência
        if ($status !== 'baixado' && $periodo === 'baixa') {
            $json['message'] = $this->message->warning("A busca do período pela 'BAIXA' só pode ser usado quando o status for 'BAIXADO'!")->render();
            echo json_encode($json);
            return;
        }


        if (!isset($data['datai']) || !isset($data['dataf']) || empty($data['datai']) || empty($data['dataf'])) {
            $json['message'] = $this->message->warning("Informe a data inicial e a data final!")->render();
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
        $filtros['url_pdf'] = url("financeirorel/pdf");

        $filtrosPaginacao = $data;
        $filtrosPaginacao['form'] = $data['tabela'] === 'pag' ? 'pagrel' : 'recrel';

        $json['total'] = $resultado['total'];
        $json['paginacao'] = $filtrosPaginacao;
        $json['financeirorel'] = true;
        $json['registros'] = $resultadoData;
        $json['filtros'] = $filtros;
        $json['periodo'] = $resultado['periodo'];

        echo json_encode($json);
    }

    public function pdf($data)
    {
        $emp = (new Emp2())->findById($this->user->id_emp2);

        if (empty($data['dados'])) {
            $json['error'] = true;
            $json['message'] = $this->message->error("Sem resultados pro relatório!")->render();
            echo json_encode($json);
            return;
        }

        $resultado = $this->consultaRelatorio($data['dados']);

        foreach ($resultado['resultados'] as $item) {
            $item->operacao = (new Oper())->findById($item->id_oper)->descricao;
        }


        $html = $this->view->render("tcsistemas.financeiro/relatorios/relatorio-pdf", [
            "emp" => $emp,
            "user" => $this->user,
            "dados" => $resultado['resultados'],
            "periodo" => $resultado['periodo']
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



    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
