<?php

namespace Source\Controllers;

use Source\Models\Baixas;
use Source\Models\BaixasItens;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Pag;
use Source\Models\PagSaldo;
use Source\Models\Rec;
use Source\Models\RecSaldo;

class BaixarController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $front = [
            "titulo" => "Baixas - Taskforce",
            "user" => $this->user,
            "secTit" => "Baixas"
        ];

        $baixar = null;
        $cliente = null;
        $fornecedor = null;
        $portador = null;

        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $fornecedor = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=2&status=A"
        )->fetch(true);

        $portador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=4&status=A"
        )->fetch(true);


        if (!empty($data)) {

            $tipo = $data['tabelaDados']['0']['tipo'];
            $ids = array_column($data['tabelaDados'], 'id');

            $idsSql = implode(', ', array_map('intval', $ids));

            if ($tipo == 'despesa') {
                $baixar = (new Pag())->find("id IN ({$idsSql})", null, "*", false)->fetch(true);
                foreach ($baixar as $b) {
                    $b->tipo = "despesa";
                }
            } elseif ($tipo == 'receita') {
                $baixar = (new Rec())->find("id IN ({$idsSql})", null, "*", false)->fetch(true);
                foreach ($baixar as $b) {
                    $b->tipo = "receita";
                }
            }
        }

        echo $this->view->render("tcsistemas.financeiro/baixar/baixar", [
            "front" => $front,
            "baixar" => $baixar,
            "cliente" => $cliente,
            "fornecedor" => $fornecedor,
            "portador" => $portador
        ]);
    }

    private function registraBaixas($baixasGeradas)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $baixaLote = new Baixas();

        $dataBaixa = $baixasGeradas['baixas'][0]->datapag;

        if ($baixasGeradas['tipo'] == 'receita') {
            $baixaLote->tipo = 'R';
            $indicebaixa = 'id_recsaldo';
            $tabPaiBaixaGerada = 'id_rec';
        } elseif ($baixasGeradas['tipo'] == 'despesa') {
            $baixaLote->tipo = 'D';
            $indicebaixa = 'id_pagsaldo';
            $tabPaiBaixaGerada = 'id_pag';
        }

        $baixaLote->id_emp2 = $id_empresa;
        $baixaLote->id_users = $id_user;
        $baixaLote->databaixa = $dataBaixa;

        if (!$baixaLote->save) {
            $return['message'] = "Erro ao registrar baixa em lote!";
            $return['status'] = "error";
            return $return;
        }

        foreach ($baixasGeradas['baixas'] as $b) {
            $baixaItem = new BaixasItens();
            $baixaItem->id_emp2 = $id_empresa;
            $baixaItem->id_users = $id_user;
            $baixaItem->id_baixas = $baixaLote->id;
            $baixaItem->id_pagrec = $b->$tabPaiBaixaGerada;
            $baixaItem->id_ent = $b->id_ent;
            $baixaItem->{$indicebaixa} = $b->id;
            $baixaItem->valor = $b->valor;
            $baixaItem->vdesc = $b->vdesc;
            $baixaItem->voutros = $b->voutros;
            $baixaItem->vpago = $b->vpago;
            $baixaItem->saldo = $b->saldo;

            if (!$baixaItem->save) {
                $return['message'] = "Erro ao registrar baixa em lote!";
                $return['status'] = "error";
                return $return;
            }

            $logItem = new Log();
            $logItem->registrarLog("C", $baixaItem->getEntity(), $baixaItem->id, null, $baixaItem->data());
        }

        $logBaixa = new Log();
        $logBaixa->registrarLog("C", $baixaLote->getEntity(), $baixaLote->id, null, $baixaLote->data());

        $return['baixaid'] = $baixaLote->id;
        $return['status'] = "success";
        return $return;
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $dataBaixa = date_fmt($data['dataBx'], "d/m/Y");

        $baixasGeradas = [];
        $transacao = new Emp2();
        $transacao->beginTransaction();
        foreach ($data as $key => $value) {
            if (strpos($key, 'idBx_') !== false) {

                $id = str_replace('idBx_', '', $key);

                $tipo = isset($data["tipoBx_$id"]) ? $data["tipoBx_$id"] : "";
                $vbaixa = isset($data["vbaixaBx_$id"]) ? moedaSql($data["vbaixaBx_$id"]) : 0;
                if ($vbaixa == 0) {
                    $json['message'] = $this->message->warning("Valor da baixa não pode estar zerado!")->render();
                    echo json_encode($json);
                    return;
                }
                $vdesc = (isset($data["vdescBx_$id"]) && $data["vdescBx_$id"] !== "") ? moedaSql($data["vdescBx_$id"]) : 0;
                $voutros = (isset($data["voutrosBx_$id"]) && $data["voutrosBx_$id"] !== "") ? moedaSql($data["voutrosBx_$id"]) : 0;

                if ($tipo == 'receita') {
                    $tituloPai = (new Rec())->findById($id);
                    $antes = clone $tituloPai->data();
                    $tabelaSaldo = new RecSaldo();
                    $idPai = 'id_rec';
                    $idEnt = $tituloPai->id_entc;
                } elseif ($tipo == 'despesa') {
                    $tituloPai = (new Pag())->findById($id);
                    $antes = clone $tituloPai->data();
                    $tabelaSaldo = new PagSaldo();
                    $idPai = 'id_pag';
                    $idEnt = $tituloPai->id_entf;
                }

                $saldo = ($tituloPai->saldo + $voutros) - ($vbaixa + $vdesc);
                if ($saldo < 0) {
                    $json['message'] = html_entity_decode($this->message->warning(
                        "Valor Líquido não pode ficar negativo! Corrija os valores."
                    )->render());
                    $transacao->rollback();
                    echo json_encode($json);
                    return;
                }

                $tabela = $tabelaSaldo->getEntity();

                $tabelaSaldo->id_emp2 = $id_empresa;
                $tabelaSaldo->$idPai = $id;
                $tabelaSaldo->id_ent = $idEnt;
                $tabelaSaldo->datapag = date_fmt($data["dataBx"], "Y-m-d");
                $tabelaSaldo->valor = $tituloPai->saldo; //valor original da parcela
                $tabelaSaldo->vdesc = $vdesc;
                $tabelaSaldo->voutros = $voutros;
                $tabelaSaldo->vpago = $vbaixa;
                $tabelaSaldo->saldo = $saldo;
                $tabelaSaldo->id_users = $id_user;

                if (!$tabelaSaldo->save) {
                    $json['message'] = $this->message->error("ERRO!")->render();
                    echo json_encode($json);
                    $transacao->rollback();
                    return;
                }

                $baixasGeradas['baixas'][] = $tabelaSaldo->data();
                $baixasGeradas['tipo'] = $tipo;

                $log = new Log();
                $log->registrarLog("C", $tabela, $id, null, $tabelaSaldo->data());


                if ($tipo == 'receita') {
                    $sum = new RecSaldo();
                } elseif ($tipo == 'despesa') {
                    $sum = new PagSaldo();
                }

                $desconto = $sum->find("{$idPai} = :id_pai", "id_pai={$id}", "SUM(vdesc) as desconto", false)->fetch();
                $outros = $sum->find("{$idPai} = :id_pai", "id_pai={$id}", "SUM(voutros) as outros", false)->fetch();
                $pago = $sum->find("{$idPai} = :id_pai", "id_pai={$id}", "SUM(vpago) as pago", false)->fetch();

                $tituloPai->id_entp = $data['idportBx'];
                $tituloPai->id_users = $id_user;

                $tituloPai->vdesc = $desconto->desconto;
                $tituloPai->voutros = $outros->outros;
                $tituloPai->vpago = $pago->pago;

                $tituloPai->saldo = $saldo;

                $tituloPai->databaixa = ($tituloPai->saldo == 0) ? date_fmt($data["dataBx"], "Y-m-d") : null;
                $tituloPai->baixado = ($tituloPai->saldo == 0) ? "S" : "N";

                if (!$tituloPai->save) {
                    $json['message'] = $this->message->error("ERRO!")->render();
                    echo json_encode($json);
                    $transacao->rollback();
                    return;
                }
                $tabelaPai = $tituloPai->getEntity();

                $log = new Log();
                $log->registrarLog("U", $tabelaPai, $id, $antes, $tituloPai->data());
            }
        }

        $baixas = $this->registraBaixas($baixasGeradas);

        if ($baixas['status'] == "error") {
            $json['message'] = $this->message->error($baixas['message'])->render();
            echo json_encode($json);
            $transacao->rollback();
            return;
        }

        // var_dump($baixas);
        // $transacao->rollBack(); //tirar essa linha depois de testar
        // exit;
        $transacao->commit();


        $json['message'] = $this->message->success("Baixa(s) efetuada(s) com sucesso!")->render();
        $json['baixaid'] = $baixas['baixaid'];
        $json['databaixa'] = $dataBaixa;
        $json['retornobaixas'] = true;
        $json['url'] = url("baixar/pdf");
        echo json_encode($json);
    }

    public function baixasPdf($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);

        $id = $data['id'];

        $loteBaixa = (new Baixas())->findById($id);
        $baixadoEm = date_fmt($loteBaixa->databaixa, 'd/m/Y');
        $baixas = (new BaixasItens())->find("id_baixas = :id_baixas", "id_baixas={$id}")->fetch(true);

        if (!empty($baixas)) {
            // Determina qual campo tem valor (id_pagsaldo ou id_recsaldo)
            $firstItem = $baixas[0];
            $isPagsaldo = !is_null($firstItem->id_pagsaldo);
            $isRecsaldo = !is_null($firstItem->id_recsaldo);

            foreach ($baixas as $b) {
                $registroExiste = false;

                $titulo = "";
                $emissao = "";
                $vencimento = "";
                $tipo = "";
                // Verifica se o registro ainda existe na tabela original
                if ($isPagsaldo && !is_null($b->id_pagsaldo)) {
                    // Verifica se o registro existe na tabela pagsaldo
                    $pagsaldo = (new Pagsaldo())->findById($b->id_pagsaldo);
                    $registroExiste = $pagsaldo !== null;
                    $registroPai = (new Pag())->findById($b->id_pagrec);
                    $titulo = $registroPai->titulo;
                    $emissao = $registroPai->datacad;
                    $vencimento = $registroPai->dataven;
                    $tipo = 'pag';
                } elseif ($isRecsaldo && !is_null($b->id_recsaldo)) {
                    // Verifica se o registro existe na tabela recsaldo
                    $recsaldo = (new Recsaldo())->findById($b->id_recsaldo);
                    $registroExiste = $recsaldo !== null;
                    $registroPai = (new Rec())->findById($b->id_pagrec);
                    $titulo = $registroPai->titulo;
                    $emissao = $registroPai->datacad;
                    $vencimento = $registroPai->dataven;
                    $tipo = 'rec';
                }

                // Adiciona o novo índice indicando se o registro ainda existe
                $b->registro_existe = $registroExiste;
                $b->titulo = $titulo;
                $b->datacad = date_fmt($emissao, 'd/m/Y');
                $b->dataven = date_fmt($vencimento, 'd/m/Y');
                $b->entidade_nome = (new Ent())->findById($b->id_ent)->nome;
            }
        }

        $html = $this->view->render("tcsistemas.financeiro/baixar/relatorio-baixas-pdf", [
            "emp" => $emp,
            "user" => $this->user,
            "dados" => $baixas,
            "baixadoEm" => $baixadoEm,
            "tipo" => $tipo
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

    public function buscaBaixas($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $page = !empty($data['page']) ? (int)$data['page'] : 1;
        $limit = !empty($data['limit']) ? (int)$data['limit'] : 15;
        $offset = ($page - 1) * $limit;

        $terms = ["id_emp2 = :id_emp2"];
        $params = ["id_emp2={$id_empresa}"];

        // Filtro por lote (id)
        if (!empty($data['lote'])) {
            $terms[] = "id = :id";
            $params[] = "id={$data['lote']}";
        }

        // Filtro por data_baixa
        if (!empty($data['data_baixa'])) {
            $terms[] = "DATE(databaixa) = :databaixa";
            $params[] = "databaixa={$data['data_baixa']}";
        }

        // Filtro por tipo
        if (!empty($data['tipo']) && $data['tipo'] !== 'todos') {
            $terms[] = "tipo = :tipo";
            $params[] = "tipo={$data['tipo']}";
        }

        $termsStr = implode(" AND ", $terms);
        $paramsStr = implode("&", $params);

        $query = (new Baixas())->find($termsStr, $paramsStr);

        $total = $query->count();

        $baixas = $query->limit($limit)->offset($offset)->fetch(true);

        if (!empty($baixas)) {
            foreach ($baixas as $b) {
                $b->data_baixa = date_fmt($b->databaixa, "d/m/Y");
                $b->url = url("baixar/pdf");
            }
        }

        $baixasData = [];
        if (!empty($baixas)) {
            $baixasData = objectsToArray($baixas);
        }


        $filtrosPaginacao = $data;
        $filtrosPaginacao['page'] = $page;
        $filtrosPaginacao['limit'] = $limit;

        $json['registros'] = $baixasData;
        $json['total'] = $total ?? 0;
        $json['page'] = $page;
        $json['limit'] = $limit;
        $json['paginacao'] = $filtrosPaginacao;
        $json['baixaslist'] = true;

        echo json_encode($json);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
