<?php

namespace Source\Controllers;

use DateTime;
use DateTimeZone;
use Source\Boot\Session;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Materiais;
use Source\Models\Oper;
use Source\Models\Pag;
use Source\Models\Plconta;
use Source\Models\Rec;
use Source\Models\Servico;
use Source\Models\Users;

class Dash extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function dash(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);
        $empTit = !empty($emp->fantasia) ? $emp->fantasia : $emp->razao;

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

        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "secTit" => "Bem vindo(a), " . $this->user->nome . "!",
            "empTit" => $empTit
        ];

        echo $this->view->render("tcsistemas.financeiro/dash/telainicial", [
            "front" => $front,
            "cliente" => $cliente,
            "servico" => $servico,
            "operador" => $operador,
            "produto" => $produto
        ]);
    }

    public function cadastros(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => "Cadastros",
        ];

        echo $this->view->render("tcsistemas.financeiro/dash/cadastros", [
            "front" => $front,
            "empresa" => $emp
        ]);
    }

    public function dashFinanceiro(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);

        $empTit = mb_strimwidth(!empty($emp->fantasia) ? $emp->fantasia : $emp->razao, 0, 30, "...");

        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "secTit" => "Bem vindo(a), " . $this->user->nome . "!",
            "empTit" => $empTit
        ];

        $pag = new Pag();
        $rec = new Rec();

        $despesas = (new Pag())->find(
            null,
            null,
            "sum(valor) as totalpag,
         case when baixado = 'S' then databaixa else dataven end as data_mov"
        )->group("data_mov")->order("data_mov")->fetch(true);

        $totalDespesas = $pag->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalpag"
        )->fetch();

        $receitas = (new Rec())->find(
            null,
            null,
            "sum(valor) as totalrec, 
         case when baixado = 'S' then databaixa else dataven end as data_mov"
        )->group("data_mov")->order("data_mov")->fetch(true);

        $totalReceitas = $rec->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalrec"
        )->fetch();

        echo $this->view->render("tcsistemas.financeiro/dash/dash", [
            "front" => $front,
            "receitas" => $receitas,
            "despesas" => $despesas,
            "totalreceitas" => $totalReceitas,
            "totaldespesas" => $totalDespesas
        ]);
    }

    public function trocar_empresa($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa_id = ll_decode($data['empresa_select']);

        if (ll_intValida($empresa_id)) {
            $empresa = (new Emp2())->findById($empresa_id);
            $sessao = new Session();
            set_session($sessao, $empresa);
            $this->message->success("Troca de empresa realizada com sucesso!")->flash();
        }
        $json['redirect'] = url("dash");
        echo json_encode($json);
    }

    public function graficos($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        // Mês atual
        $mesReferencia = date('n'); // Pega o número do mês atual (1 a 12)
        $anoReferencia = date('Y');

        if ("POST" == $_SERVER['REQUEST_METHOD']) {
            $situacao = $data['situacao'];
            $query = "";
            $queryparam = "";
            if ($situacao != 'todas') {
                $query = " AND baixado = :baixado";
                $queryparam = "&baixado={$situacao}";
            }

            if ($situacao == 'N') {
                $campo = "dataven";
            } else if ($situacao == 'S') {
                $campo = "databaixa";
            }

            if (isset($data['tudo']) && $situacao == 'todas') {
                $totalDespesas = (new Pag())->find(
                    null,
                    null,
                    "sum(valor) as totalpag"
                )->fetch();
                $totalReceitas = (new Rec())->find(
                    null,
                    null,
                    "sum(valor) as totalrec"
                )->fetch();
            } else if (isset($data['tudo']) && $situacao != 'todas') {
                // Remove o "AND" inicial da condição (se houver)
                $query2 = preg_replace('/^\s*AND\s+/i', '', $query ?? '');

                // Garante que parâmetros e filtros estejam definidos corretamente
                $totalDespesas = (new Pag())->find(
                    $query2 = null,
                    $queryparam = null,
                    "sum(valor) as totalpag"
                )->fetch();

                $totalReceitas = (new Rec())->find(
                    $query2 = null,
                    $queryparam = null,
                    "sum(valor) as totalrec"
                )->fetch();

                // Fallback para evitar erro caso venha vazio
                if (empty($totalDespesas)) {
                    $totalDespesas = (object) ['data' => (object) ['totalpag' => 0]];
                }

                if (empty($totalReceitas)) {
                    $totalReceitas = (object) ['data' => (object) ['totalrec' => 0]];
                }

                // Aqui você pode chamar sua função de resposta, se necessário
                // echo json_encode([...]); return;
            }

            if (isset($data['semana']) && $situacao == 'todas') {
                $inicio = $data['datai'];
                $fim = $data['dataf'];

                if (empty($data["datai"]) || empty($data['dataf'])) {
                    $json["message"] = $this->message->warning("Preencha o período!")->render();
                    echo json_encode($json);
                    return;
                }

                $totalDespesas = (new Pag())->find(
                    "((baixado = 'N' AND dataven BETWEEN :datai AND :dataf) OR (baixado = 'S' AND databaixa BETWEEN :datai AND :dataf))",
                    "datai={$inicio}&dataf={$fim}",
                    "sum(valor) as totalpag"
                )->fetch();
                $totalReceitas = (new Rec())->find(
                    "((baixado = 'N' AND dataven BETWEEN :datai AND :dataf) OR (baixado = 'S' AND databaixa BETWEEN :datai AND :dataf))",
                    "datai={$inicio}&dataf={$fim}",
                    "sum(valor) as totalrec"
                )->fetch();
            } else if (isset($data['semana']) && $situacao != 'todas') {
                $inicio = $data['datai'];
                $fim = $data['dataf'];

                if (empty($data["datai"]) || empty($data['dataf'])) {
                    $json["message"] = $this->message->warning("Preencha o período!")->render();
                    echo json_encode($json);
                    return;
                }

                $totalDespesas = (new Pag())->find(
                    "{$campo} BETWEEN :datai AND :dataf {$query}",
                    "datai={$inicio}&dataf={$fim}{$queryparam}",
                    "sum(valor) as totalpag"
                )->fetch();
                $totalReceitas = (new Rec())->find(
                    "{$campo} BETWEEN :datai AND :dataf {$query}",
                    "datai={$inicio}&dataf={$fim}{$queryparam}",
                    "sum(valor) as totalrec"
                )->fetch();
            }

            if (isset($data['hoje']) && !empty($data['hoje']) && $situacao == 'todas') {
                $dia = $data['hoje'];
                $totalDespesas = (new Pag())->find(
                    "((baixado = 'N' AND dataven = :dia) OR (baixado = 'S' AND databaixa = :dia))",
                    "dia={$dia}",
                    "sum(valor) as totalpag"
                )->fetch();
                $totalReceitas = (new Rec())->find(
                    "((baixado = 'N' AND dataven = :dia) OR (baixado = 'S' AND databaixa = :dia))",
                    "dia={$dia}",
                    "sum(valor) as totalrec"
                )->fetch();
            } else if (isset($data['hoje']) && !empty($data['hoje']) && $situacao != 'todas') {
                $dia = $data['hoje'];
                $totalDespesas = (new Pag())->find(
                    "{$campo} = :dia {$query}",
                    "dia={$dia}{$queryparam}",
                    "sum(valor) as totalpag"
                )->fetch();
                $totalReceitas = (new Rec())->find(
                    "{$campo} = :dia {$query}",
                    "dia={$dia}{$queryparam}",
                    "sum(valor) as totalrec"
                )->fetch();
            }

            if (isset($data['mes']) && !empty($data['mes']) && $situacao != 'todas') {
                // Se o mês estiver preenchido e a situação não for "todas"
                list($mes, $ano) = explode('/', $data['mes']);

                // Soma das despesas do mês
                $totalDespesas = (new Pag())->find(
                    "MONTH({$campo}) = :mes AND YEAR({$campo}) = :ano {$query}",
                    "mes={$mes}&ano={$ano}{$queryparam}",
                    "SUM(valor) AS totalpag"
                )->fetch();

                // Soma das receitas do mês
                $totalReceitas = (new Rec())->find(
                    "MONTH({$campo}) = :mes AND YEAR({$campo}) = :ano {$query}",
                    "mes={$mes}&ano={$ano}{$queryparam}",
                    "SUM(valor) AS totalrec"
                )->fetch();

                // Remove "AND" inicial da query para evitar erro na segunda busca (gráfico)
                $query2 = preg_replace('/^\s*AND\s+/i', '', $query);

                // Evolução mensal - Despesas
                $totalDespesasMes = (new Pag())->find(
                    $query2 ?: null,
                    $queryparam ?: null,
                    "SUM(valor) AS totalpag, MONTH({$campo}) AS mes, YEAR({$campo}) AS ano"
                )->group("mes, ano")->fetch(true) ?: [];

                // Evolução mensal - Receitas
                $totalReceitasMes = (new Rec())->find(
                    $query2 ?: null,
                    $queryparam ?: null,
                    "SUM(valor) AS totalrec, MONTH({$campo}) AS mes, YEAR({$campo}) AS ano"
                )->group("mes, ano")->fetch(true) ?: [];

                // Valores padrão se estiverem vazios
                if (empty($totalDespesas)) {
                    $totalDespesas = (object)['data' => (object)['totalpag' => 0]];
                }
                if (empty($totalReceitas)) {
                    $totalReceitas = (object)['data' => (object)['totalrec' => 0]];
                }

                // Aqui, como são arrays de objetos, garantimos que estejam no formato esperado
                if (!is_array($totalDespesasMes)) {
                    $totalDespesasMes = [];
                }
                if (!is_array($totalReceitasMes)) {
                    $totalReceitasMes = [];
                }

                // Monta dados para gráfico
                $dados = $this->mesesGraficos($mes, $ano, $totalDespesas, $totalDespesasMes, $totalReceitas, $totalReceitasMes);

                echo json_encode($dados);
                return;
            } else if (isset($data['mes']) && !empty($data['mes']) && $situacao == 'todas') {
                list($mes, $ano) = explode('/', $data['mes']);

                // Soma total das despesas no mês informado
                $totalDespesas = (new Pag())->find(
                    "((baixado = 'N' AND MONTH(dataven) = :mes AND YEAR(dataven) = :ano) OR (baixado = 'S' AND MONTH(databaixa) = :mes AND YEAR(databaixa) = :ano))",
                    "mes={$mes}&ano={$ano}",
                    "SUM(valor) AS totalpag"
                )->fetch();

                // Soma total das receitas no mês informado
                $totalReceitas = (new Rec())->find(
                    "((baixado = 'N' AND MONTH(dataven) = :mes AND YEAR(dataven) = :ano) OR (baixado = 'S' AND MONTH(databaixa) = :mes AND YEAR(databaixa) = :ano))",
                    "mes={$mes}&ano={$ano}",
                    "SUM(valor) AS totalrec"
                )->fetch();

                // Define a data inicial como 1 mês antes do mês informado e a final como 5 meses após
                $dataInicial = new DateTime("{$ano}-{$mes}-01");
                $dataInicial->modify('-1 month');
                $dataFinal = clone $dataInicial;
                $dataFinal->modify('+5 months');

                $dataInicialFormatada = $dataInicial->format('Y-m-01');
                $dataFinalFormatada = $dataFinal->format('Y-m-t');

                // Evolução das despesas dos últimos 6 meses
                $totalDespesasMes = (new Pag())->find(
                    "((baixado = 'N' AND dataven BETWEEN :data_inicial AND :data_final) OR (baixado = 'S' AND databaixa BETWEEN :data_inicial AND :data_final))",
                    "data_inicial={$dataInicialFormatada}&data_final={$dataFinalFormatada}",
                    "SUM(valor) AS totalpag, MONTH(CASE WHEN baixado = 'N' THEN dataven ELSE databaixa END) AS mes, YEAR(CASE WHEN baixado = 'N' THEN dataven ELSE databaixa END) AS ano"
                )->group("mes, ano")->fetch(true) ?: [];

                // Evolução das receitas dos últimos 6 meses
                $totalReceitasMes = (new Rec())->find(
                    "((baixado = 'N' AND dataven BETWEEN :data_inicial AND :data_final) OR (baixado = 'S' AND databaixa BETWEEN :data_inicial AND :data_final))",
                    "data_inicial={$dataInicialFormatada}&data_final={$dataFinalFormatada}",
                    "SUM(valor) AS totalrec, MONTH(CASE WHEN baixado = 'N' THEN dataven ELSE databaixa END) AS mes, YEAR(CASE WHEN baixado = 'N' THEN dataven ELSE databaixa END) AS ano"
                )->group("mes, ano")->fetch(true) ?: [];

                // Garante objetos válidos se não houver dados
                if (empty($totalDespesas)) {
                    $totalDespesas = (object) ['data' => (object) ['totalpag' => 0]];
                }
                if (empty($totalReceitas)) {
                    $totalReceitas = (object) ['data' => (object) ['totalrec' => 0]];
                }

                if (!is_array($totalDespesasMes)) {
                    $totalDespesasMes = [];
                }
                if (!is_array($totalReceitasMes)) {
                    $totalReceitasMes = [];
                }

                $dados = $this->mesesGraficos($mes, $ano, $totalDespesas, $totalDespesasMes, $totalReceitas, $totalReceitasMes);

                echo json_encode($dados);
                return;
            }

            if (empty($totalDespesas)) {
                $totalDespesas = (object) ['data' => (object) ['totalpag' => 0]];
            }
            if (empty($totalReceitas)) {
                $totalReceitas = (object) ['data' => (object) ['totalrec' => 0]];
            }

            $dados = [
                'graficoDespesas' => [
                    'receitas' => $totalReceitas->data->totalrec,
                    'despesas' => $totalDespesas->data->totalpag
                ]
            ];

            echo json_encode($dados);
            return;
        }

        $totalDespesas = (new Pag())->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalpag"
        )->fetch();

        $totalReceitas = (new Rec())->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalrec"
        )->fetch();

        // Consultar receitas e despesas agrupadas por mês e ano sem limitar o ano
        $totalReceitasMes = (new Rec())->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalrec, MONTH(dataven) as mes, YEAR(dataven) as ano"
        )->group("mes, ano")->fetch(true);

        $totalDespesasMes = (new Pag())->find(
            "baixado = :baixado",
            "baixado=N",
            "sum(valor) as totalpag, MONTH(dataven) as mes, YEAR(dataven) as ano"
        )->group("mes, ano")->fetch(true);

        $dados = $this->mesesGraficos($mesReferencia, $anoReferencia, $totalDespesas, $totalDespesasMes, $totalReceitas, $totalReceitasMes);

        echo json_encode($dados);
    }

    private function mesesGraficos($mesReferencia, $anoReferencia, $totalDespesas, $totalDespesasMes, $totalReceitas, $totalReceitasMes)
    {
        $nomesMeses = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Set',
            10 => 'Out',
            11 => 'Nov',
            12 => 'Dez'
        ];

        // Criar a lista de meses (1 mês antes e 4 meses depois do atual)
        $meses = [];
        $anos = [];

        for ($i = -1; $i <= 4; $i++) {
            $mesCalculado = ($mesReferencia + $i - 1 + 12) % 12 + 1;

            // Calcule o ano baseado no mês calculado.
            $anoCalculado = $anoReferencia + floor(($mesReferencia + $i - 1) / 12);

            $meses[] = $mesCalculado;
            $anos[] = $anoCalculado;
        }

        // Organizar os dados de receitas e despesas por mês considerando os meses e anos calculados
        $evolucaoMensal = [
            'receitas' => [],
            'despesas' => []
        ];

        // Inicializar valores para cada mês calculado
        if (!empty($meses)) {
            foreach ($meses as $index => $mes) {
                $mesAnoKey = "{$anos[$index]}-$mes";
                $evolucaoMensal['receitas'][$mesAnoKey] = 0;
                $evolucaoMensal['despesas'][$mesAnoKey] = 0;
            }
        }

        // Preencher receitas e despesas para os meses e anos calculados
        if (!empty($totalReceitasMes)) {
            foreach ($totalReceitasMes as $recMes) {
                $mesAnoKey = "{$recMes->ano}-{$recMes->mes}";
                if (array_key_exists($mesAnoKey, $evolucaoMensal['receitas'])) {
                    $evolucaoMensal['receitas'][$mesAnoKey] = $recMes->totalrec;
                }
            }
        }
        if (!empty($totalDespesasMes)) {
            foreach ($totalDespesasMes as $pagMes) {
                $mesAnoKey = "{$pagMes->ano}-{$pagMes->mes}";
                if (array_key_exists($mesAnoKey, $evolucaoMensal['despesas'])) {
                    $evolucaoMensal['despesas'][$mesAnoKey] = $pagMes->totalpag;
                }
            }
        }

        // Dados para o gráfico
        $dados = [
            'graficoDespesas' => [
                'receitas' => $totalReceitas->data->totalrec,
                'despesas' => $totalDespesas->data->totalpag
            ],
            'graficoEvolucao' => [
                'despesas' => array_values($evolucaoMensal['despesas']),
                'entradas' => array_values($evolucaoMensal['receitas']),
                'meses' => array_map(function ($mes, $ano) use ($nomesMeses) {
                    return $nomesMeses[$mes] . ' ' . $ano;
                }, $meses, $anos) // Converte os números dos meses para nomes e adiciona o ano
            ]
        ];

        return $dados;
    }

    public function profile($data): void
    {
        if ("POST" == $_SERVER['REQUEST_METHOD']) {
            if (!str_verify($data['nome'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'USUÁRIO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            $id = ll_decode($data['id_users']);
            $user = (new Users)->findById($id);

            $user->nome = $data['nome'];

            if (!empty($data["senha"])) {
                if (empty($data["senha_re"]) || $data["senha"] != $data["senha_re"]) {
                    $json["message"] = $this->message->warning("Para alterar sua senha, informe e repita a nova senha!")->render();
                    echo json_encode($json);
                    return;
                }

                $user->senha = $data["senha"];
            }

            if (!$user->save) {
                $json['message'] = $this->message->warning($user->fail()->getMessage())->render();
                echo json_encode($json);
                return;
            } else {
                $this->message->success("Atualizado com sucesso!")->flash();
                $json['reload'] = true;
                echo json_encode($json);
                return;
            }
        }

        $empresas = null;
        $usuario = null;
        if ($this->user->tipo == 5) {
            $usuario = $this->user;
            $usuario->empresa = (new Emp2())->findById($this->user->id_emp2)->razao;
            $empresas = (new Emp2())->find(
                null,
                null,
                "id, razao",
                false
            )->fetch(true);
        }

        $front = [
            "titulo" => "Configurações de usuário - Taskforce",
            "user" => $this->user,
            "secTit" => "Configurações de usuário"
        ];

        echo $this->view->render("tcsistemas.financeiro/profile/userForm", [
            "front" => $front,
            "usuario" => $usuario,
            "empresas" => $empresas
        ]);
    }

    public function swapEmpDev($data)
    {
        $id_user = $this->user->id;

        if ($this->user->tipo != 5) {
            $json['message'] = $this->message->error("Você não tem permissão para trocar de empresa.")->render();
            echo json_encode($json);
            return;
        }

        $empresa_id = $data['emp_dev'];
        $usuario = (new Users())->findById($id_user);
        $usuario->last_emp = $empresa_id;
        $usuario->save();

        $empresa = (new Emp2())->findById($empresa_id);

        $sessao = new Session();
        set_session($sessao, $empresa);
        $this->message->success("Troca de empresa realizada com sucesso!")->flash();

        $json['redirect'] = url("dash");
        echo json_encode($json);
    }

    public function emp2($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            if (!filter_var($data['razao'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'EMPRESA / RAZÃO SOCIAL'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if ($data['fantasia'] != "") {
                if (!filter_var($data['fantasia'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FANTASIA'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if (!filter_var($data['endereco'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'ENDEREÇO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (!filter_var($data['numero'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NÚMERO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if ($data['bairro'] != "") {
                if (!filter_var($data['bairro'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'BAIRRO'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if (!filter_var($data['cidade'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CIDADE'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (!filter_var($data['uf'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'UF'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (!filter_var($data['cep'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CEP'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if ($data['fone1'] != "") {
                if (!filter_var($data['fone1'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FONE 1'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if ($data['fone2'] != "") {
                if (!filter_var($data['fone2'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FONE 2'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if ($data['email'] != "") {
                if (!is_email($data['email'])) {
                    $json['message'] = $this->message->warning("Digite um email válido!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            if ($data['emp_label'] != "") {
                if (!filter_var($data['emp_label'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FANTASIA'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            } else {
                $json['message'] = $this->message->warning("Preencha o nome do segmento.")->render();
                echo json_encode($json);
                return;
            }

            if (isset($data['emp_os_financeiro_auto']) && $data['emp_os_financeiro_auto'] == 'on') {
                if (empty($data['finconfig-plconta'])) {
                    $json['message'] = $this->message->warning("Selecione as opções obrigatórias para receitas da OS.")->render();
                    $json['piscar'] = "#toggle_fin_config_modal";
                    echo json_encode($json);
                    return;
                }

                if (empty($data['finconfig-operacao'])) {
                    $json['message'] = $this->message->warning("Selecione as opções obrigatórias para receitas da OS.")->render();
                    $json['piscar'] = "#toggle_fin_config_modal";
                    echo json_encode($json);
                    return;
                }
            }

            $id = ll_decode($data['id_emp']);

            $emp = (new Emp2)->findById($id);

            $emp->razao = $data['razao'];
            $emp->fantasia = $data['fantasia'];
            $emp->cnpj = $data['cnpj'];
            $emp->endereco = $data['endereco'];
            $emp->numero = $data['numero'];
            $emp->bairro = $data['bairro'];
            $emp->cidade = $data['cidade'];
            $emp->uf = $data['uf'];
            $emp->cep = $data['cep'];
            $emp->fone1 = $data['fone1'];
            $emp->fone2 = $data['fone2'];
            $emp->email = $data['email'];
            $emp->labelFiliais = $data['emp_label'];
            $emp->iconeLabel = $data['emp_icone'];
            $emp->mostraValorPdf = !empty($data['emp_mostraValorPdf']) ? 'X' : '';
            $emp->servicosComMedicoes = !empty($data['emp_servicosComMedicoes']) ? 'X' : '';
            $emp->tarefasAditivas = !empty($data['emp_tarefasAditivas']) ? 'X' : '';
            $emp->os_financeiro_auto = !empty($data['emp_os_financeiro_auto']) ? 'X' : '';
            $emp->mostraDataLegal = !empty($data['emp_mostraDataLegal']) ? 'X' : '';
            $emp->bloqueia2tarefasPorOper = !empty($data['emp_bloqueia2tarefasPorOper']) ? 'X' : '';
            $emp->equipamentoObrigatorio = !empty($data['emp_equipamentoObrigatorio']) ? 'X' : '';
            $emp->servicosComEquipamentos = !empty($data['emp_servicosComEquipamentos']) ? 'X' : '';
            $emp->confirmaMovimentacaoEstoque = !empty($data['emp_confirmaMovimentacaoEstoque']) ? 'X' : '';
            if (!empty($data['emp_os_financeiro_auto'])) {
                $emp->plconta_padrao = $data['finconfig-plconta'] ?? null;
                $emp->oper_padrao = $data['finconfig-operacao'] ?? null;
            } else {
                $emp->plconta_padrao = null;
                $emp->oper_padrao = null;
            }
            $emp->id_users = $id_user;
            $antes = clone $emp->data();

            if (!empty($_FILES["emp_photo"])) {
                $file = $_FILES['emp_photo'];
                $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileError = $file['error'];
                $fileType = $file['type'];

                $allowed = ['jpg', 'jpeg', 'png', 'bmp'];

                if (in_array($fileExt, $allowed)) {
                    if ($fileError === 0) {
                        if ($fileSize <= 41943040) {
                            $fileNameNew = $id_empresa . '_logo_' . time() . '.' . $fileExt;
                            $fileDestination = CONF_FILES_PATH . $fileNameNew;

                            if (!empty($emp->logo)) {
                                $old_image = CONF_FILES_PATH . $emp->logo;
                            }

                            if (!file_exists(CONF_FILES_PATH)) {
                                mkdir(CONF_FILES_PATH, 0777, true);
                            }

                            if (move_uploaded_file($fileTmp, $fileDestination)) {
                                if (!empty($old_image) && file_exists($old_image)) {
                                    unlink($old_image);
                                }
                                $emp->logo = $fileNameNew;
                            } else {
                                $json['message'] = $this->message->warning("Erro ao salvar a logo.")->render();
                                echo json_encode($json);
                                return;
                            }
                        } else {
                            $json['message'] = $this->message->warning("O arquivo é muito grande. Tamanho máximo permitido: 40MB.")->render();
                            echo json_encode($json);
                            return;
                        }
                    } else {
                        $json['message'] = $this->message->warning("Erro ao fazer upload da logo.")->render();
                        echo json_encode($json);
                        return;
                    }
                } else {
                    $json['message'] = $this->message->warning("Tipo de arquivo não permitido.")->render();
                    echo json_encode($json);
                    return;
                }
            }

            $depois = $emp->data();

            if (!$emp->save()) {
                $json['message'] = $this->message->warning("Não foi possível atualizar. Por favor verifique os dados.")->render();
                echo json_encode($json);
                return;
            }

            $log = new Log();
            $log->registrarLog("U", $emp->getEntity(), $emp->id, $antes, $depois);

            $this->message->success("Dados da empresa atualizados!")->flash();
            $json['reload'] = true;
            echo json_encode($json);
            return;
        }

        $emp = (new Emp2())->findById($id_empresa);

        $plconta = (new Plconta())->find("tipo = :tipo", "tipo=R")->fetch(true);
        $operacoes = (new Oper())->find()->fetch(true);

        $front = [
            "titulo" => "Configurações da empresa - Taskforce",
            "user" => $this->user,
            "secTit" => "Dados da empresa"
        ];

        echo $this->view->render("tcsistemas.financeiro/dash/empForm", [
            "front" => $front,
            "emp2" => $emp,
            "plconta" => $plconta,
            "operacoes" => $operacoes
        ]);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
