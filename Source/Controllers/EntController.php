<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Arq;
use Source\Models\Auth;
use Source\Models\Bank;
use Source\Models\Ent;
use Source\Models\EntCli;
use Source\Models\EntFor;
use Source\Models\EntFun;
use Source\Models\EntPort;
use Source\Models\Log;
use Source\Models\Turno;

class EntController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $entfilha = $data['entfilha'];
        $tipo = ($entfilha == 'cliente') ? 1 : (($entfilha == 'fornecedor') ? 2 : (($entfilha == 'colaborador') ? 3 : 4));
        $sectitulo = ($entfilha == 'cliente') ? "Clientes" : (($entfilha == 'fornecedor') ? "Fornecedores" : (($entfilha == 'colaborador') ? "Colaboradores" : "Contas Bancárias (Portadores)"));

        $ent = (new Ent())->find(
            "status = :status AND tipo = :tipo",
            "status=A&tipo={$tipo}"
        )->fetch(true);

        $entInativos = (new Ent())->find(
            "status = :status AND tipo = :tipo",
            "status=I&tipo={$tipo}"
        )->fetch(true);

        if ($tipo == 1) {
            if ($this->user->id_emp2 != 1 && $this->user->cadastros != "X") {
                $this->message->error("Você não tem permissão para acessar essa página")->flash();
                redirect("dash");
            }
            $filha = (new EntCli())->find()->fetch(true);
        } elseif ($tipo == 2) {
            if ($this->user->id_emp2 != 1 && $this->user->cadastros != "X") {
                $this->message->error("Você não tem permissão para acessar essa página")->flash();
                redirect("dash");
            }
            $filha = (new EntFor())->find()->fetch(true);
        } elseif ($tipo == 3) {
            if ($this->user->id_emp2 != 1 && $this->user->cadastros != "X") {
                $this->message->error("Você não tem permissão para acessar essa página")->flash();
                redirect("dash");
            }
            $filha = (new EntFun())->find()->fetch(true);
        } else {
            if ($this->user->id_emp2 != 1 && $this->user->financeiro != "X") {
                $this->message->error("Você não tem permissão para acessar essa página")->flash();
                redirect("dash");
            }
            $filha = (new EntPort())->find()->fetch(true);
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => "Listagem de " . $sectitulo
        ];

        echo $this->view->render("tcsistemas.financeiro/ent/entList", [
            "front" => $front,
            "ent" => $ent,
            "entInativos" => $entInativos,
            "filha" => $filha,
            "tipo" => $tipo
        ]);
    }

    public function form(?array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $refererUrl = $_SERVER['HTTP_REFERER'] ?? null;

        // Verifica se o referer está disponível e se é uma das páginas permitidas
        if ($refererUrl && (
            strpos($refererUrl, '/cliente') !== false ||
            strpos($refererUrl, '/fornecedor') !== false ||
            strpos($refererUrl, '/colaborador') !== false ||
            strpos($refererUrl, '/portador') !== false
        )) {
        } else {
            header("Location: " . url("dash"));
            exit;
        }

        $uri = explode('ent/', $refererUrl);
        $ent = "";
        $entFilha = "";
        $secTit = "Cadastrar ";
        $tipo = "";
        $entidade = "";
        $arq = "";

        if ($uri[1] == 'cliente') {
        } elseif ($uri[1] == 'fornecedor') {
        } elseif ($uri[1] == 'colaborador') {
        } else {
            if ($this->user->id_emp2 != 1 && $this->user->os != "X") {
                $this->message->error("Você não tem permissão para acessar essa página")->flash();
                redirect("dash");
            }
        }

        if ($uri[1] == 'cliente') {
            $tipo = 1;
            $entidade = "Cliente";
        } elseif ($uri[1] == 'fornecedor') {
            $tipo = 2;
            $entidade = "Fornecedor";
        } elseif ($uri[1] == 'colaborador') {
            $tipo = 3;
            $entidade = "Colaborador";
        } else {
            $tipo = 4;
            $entidade = "Conta Bancária (Portador)";
        }

        if (isset($data['id_ent'])) {
            $id_ent = ll_decode($data['id_ent']);
            if (ll_intValida($id_ent)) {
                $ent = (new Ent())->findById($id_ent);
                if ($ent->tipo == 1) {
                    $entFilha = (new EntCli())->findByIdEnt($id_ent);
                } elseif ($ent->tipo == 2) {
                    $entFilha = (new EntFor())->findByIdEnt($id_ent);
                } elseif ($ent->tipo == 3) {
                    $entFilha = (new EntFun())->findByIdEnt($id_ent);
                    $arq = (new Arq())->find("id_func = :id_func", "id_func={$id_ent}")->fetch(true);
                } else {
                    $entFilha = (new EntPort())->findByIdEnt($id_ent);
                }
            }
            $secTit = "Visualizar/Editar ";
        }

        $bank = (new Bank())->find(null, null, "*", false)->fetch(true);

        $turnoDefault = (new Turno())->findById(13);
        $turnosGerais = (new Turno())->find()->fetch(true);

        $turno = [];

        if ($turnoDefault) {
            $turno[] = $turnoDefault;
        }

        if (!empty($turnosGerais)) {
            $turno = array_merge($turno, $turnosGerais);
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . $entidade
        ];

        echo $this->view->render("tcsistemas.financeiro/ent/entCad", [
            "front" => $front,
            "ent" => $ent,
            "entFilha" => $entFilha,
            "tipo" => $tipo,
            "uri" => $uri[1],
            "bank" => $bank,
            "turno" => $turno,
            "arquivos" => $arq,
            "id_emp" => $id_empresa
        ]);
    }

    public function salvar($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (empty($data['ent_tipo'])) {
            $json['message'] = $this->message->warning("Selecione o TIPO")->render();
            echo json_encode($json);
            return;
        }

        $labelnome = "RAZAO SOCIAL";
        $labelfantasia = "FANTASIA";
        $labelcpf = "CNPJ";
        $labelrg = "INSCRIÇÃO ESTADUAL";

        if ($data['ent_fisjur'] == '1') {
            $labelnome = "NOME";
            $labelfantasia = "APELIDO";
            $labelcpf = "CPF";
            $labelrg = "RG";

            // if (empty($data['ent_cpf'])) {
            //     $json['message'] = $this->message->warning("Campo '{$labelcpf}' é obrigatório!")->render();
            //     echo json_encode($json);
            //     return;
            // }
            if (!str_verify($data['ent_cpf'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo '{$labelcpf}'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        } else {
            // if (empty($data['ent_cnpj'])) {
            //     $json['message'] = $this->message->warning("Campo '{$labelcpf}' é obrigatório!")->render();
            //     echo json_encode($json);
            //     return;
            // }
            if (!str_verify($data['ent_cnpj'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo '{$labelcpf}'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_inscrg'])) {
            if (!str_verify($data['ent_inscrg'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo '{$labelrg}'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (empty($data['ent_nome'])) {
            $json['message'] = $this->message->warning("Campo '{$labelnome}' é obrigatório!")->render();
            echo json_encode($json);
            return;
        }
        if (!str_verify($data['ent_nome'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo '{$labelnome}'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['ent_fantasia'])) {
            if (!str_verify($data['ent_fantasia'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo '{$labelfantasia}'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_endereco'])) {
            if (!str_verify($data['ent_endereco'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'ENDEREÇO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_numero'])) {
            if (!str_verify($data['ent_numero'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NÚMERO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_complemento'])) {
            if (!str_verify($data['ent_complemento'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'COMPLEMENTO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_bairro'])) {
            if (!str_verify($data['ent_bairro'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'BAIRRO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_cidade'])) {
            if (!str_verify($data['ent_cidade'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CIDADE'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_uf'])) {
            if (!str_verify($data['ent_uf'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'UF'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_cep'])) {
            if (!str_verify($data['ent_cep'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CEP'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_email']) && !is_email($data['ent_email'])) {
            $json['message'] = $this->message->warning("Digite um email válido!")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($data['ent_fone1'])) {
            if (!str_verify($data['ent_fone1'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FONE'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['ent_fone2'])) {
            if (!str_verify($data['ent_fone2'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CELULAR'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['func_matricula'])) {
            if (!str_verify($data['func_matricula'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'MATRÍCULA'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['func_ctps'])) {
            if (!str_verify($data['func_ctps'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CTPS'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['func_cargo'])) {
            if (!str_verify($data['func_cargo'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'CARGO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['func_salario'])) {
            if (!str_verify($data['func_salario'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'SALÁRIO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (!empty($data['func_admissao'])) {
            if (!validate_date($data['func_admissao'])) {
                $json['message'] = $this->message->error("ERRO! Data inválida campo 'ADMISSÃO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }
        }

        if (empty($data['func_turno']) && $data['ent_tipo'] == '3') {
            $json['message'] = $this->message->error("ERRO! Selecione um 'TURNO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $id_ent = ll_decode($data['id_ent']);

        if (ll_intValida($id_ent)) {
            $ent = (new Ent())->findById($id_ent);
            $antes = clone $ent->data();
            $acao = "U";
        } else {
            $ent = new Ent();
            $antes = null;
            $acao = "C";
        }

        $ent->id_emp2 = $id_empresa;
        $ent->tipo = $data['ent_tipo'];
        $ent->nome = $data['ent_nome'];
        $ent->fantasia = $data['ent_fantasia'];
        $ent->fisjur = $data['ent_fisjur'];
        $ent->cpfcnpj = ($data['ent_fisjur'] == '1') ? $data['ent_cpf'] : $data['ent_cnpj'];
        $ent->inscrg = $data['ent_inscrg'];
        $ent->endereco = $data['ent_endereco'];
        $ent->numero = $data['ent_numero'];
        $ent->complemento = $data['ent_complemento'];
        $ent->bairro = $data['ent_bairro'];
        $ent->cidade = $data['ent_cidade'];
        $ent->uf = $data['ent_uf'];
        $ent->cep = $data['ent_cep'];
        $ent->email = $data['ent_email'];
        $ent->fone1 = $data['ent_fone1'];
        $ent->fone2 = $data['ent_fone2'];
        $ent->status = isset($data['ent_status']) && $data['ent_status'] == 'on' ? 'A' : 'I';
        $ent->id_users = $id_user;
        $tabelaEnt = $ent->getEntity();

        $depois = $ent->data();

        if (!$ent->save) {
            $json['message'] = $this->message->error("Erro ao cadastrar, por favor verifique os dados!")->render();
            echo json_encode($json);
            return;
        } else {
            if ($data['ent_tipo'] == '1') {
                $tipo = "cliente";
                if (ll_intValida($id_ent)) {
                    $entcli = (new EntCli())->findByIdEnt($id_ent);
                    $antescli = clone $entcli->data();
                    $acaocli = "U";
                    if (empty($entcli)) {
                        $entcli = new EntCli();
                        $antescli = null;
                        $acaocli = "C";
                    }
                } else {
                    $entcli = new EntCli();
                    $antescli = null;
                    $acaocli = "C";
                }
                $entcli->id_emp2 = $id_empresa;
                $entcli->id_ent = $ent->id;
                $entcli->obs = $data['cli_obs'];
                $entcli->id_users = $id_user;
                if (!$entcli->save) {
                    $json['message'] = $this->message->error("Erro ao salvar dados do Cliente!")->render();
                    echo json_encode($json);
                    return;
                };
                $logcli = new Log();
                $logcli->registrarLog($acaocli, $entcli->getEntity(), $entcli->id, $antescli, $entcli->data());
            } elseif ($data['ent_tipo'] == '2') {
                $tipo = "fornecedor";
                if (ll_intValida($id_ent)) {
                    $entfor = (new EntFor())->findByIdEnt($id_ent);
                    $antesfor = clone $entfor->data();
                    $acaofor = "U";
                    if (empty($entfor)) {
                        $entfor = new EntFor();
                        $antesfor = null;
                        $acaofor = "C";
                    }
                } else {
                    $entfor = new EntFor();
                    $antesfor = null;
                    $acaofor = "C";
                }
                $entfor->id_emp2 = $id_empresa;
                $entfor->id_ent = $ent->id;
                $entfor->obs = $data['cli_obs'];
                $entfor->id_users = $id_user;
                if (!$entfor->save) {
                    $json['message'] = $this->message->error("Erro ao salvar dados do Fornecedor!")->render();
                    echo json_encode($json);
                    return;
                };
                $logfor = new Log();
                $logfor->registrarLog($acaofor, $entfor->getEntity(), $entfor->id, $antesfor, $entfor->data());
            } elseif ($data['ent_tipo'] == '3') {
                $tipo = "colaborador";
                if (ll_intValida($id_ent)) {
                    $entfunc = (new EntFun())->findByIdEnt($id_ent);
                    $antesfunc = clone $entfunc->data();
                    $acaofunc = "U";
                    if (empty($entfunc)) {
                        $entfunc = new EntFun();
                        $antesfunc = null;
                        $acaofunc = "C";
                    }
                } else {
                    $entfunc = new EntFun();
                    $antesfunc = null;
                    $acaofunc = "C";
                }
                $entfunc->id_emp2 = $id_empresa;
                $entfunc->id_ent = $ent->id;
                $entfunc->matricula = $data['func_matricula'];
                $entfunc->ctps = $data['func_ctps'];
                $entfunc->cargo = $data['func_cargo'];
                $entfunc->salario = $data["func_salario"] != "" ? str_replace(['.', ','], ['', '.'], $data['func_salario']) : 0;
                $entfunc->admissao = date_fmt_sql($data['func_admissao']);
                $entfunc->id_turno = $data['func_turno'];
                $entfunc->depto = $data['func_setor'];
                $entfunc->id_users = $id_user;
                if (!$entfunc->save) {
                    $json['message'] = $this->message->error("Erro ao salvar dados do Colaborador!")->render();
                    echo json_encode($json);
                    return;
                };
                $logfunc = new Log();
                $logfunc->registrarLog($acaofunc, $entfunc->getEntity(), $entfunc->id, $antesfunc, $entfunc->data());
            } elseif ($data['ent_tipo'] == '4') {
                $tipo = "portador";
                if (ll_intValida($id_ent)) {
                    $entport = (new EntPort())->findByIdEnt($id_ent);
                    $antesport = clone $entport->data();
                    $acaoport = "U";
                    if (empty($entport)) {
                        $entport = new EntPort();
                        $antesport = null;
                        $acaoport = "C";
                    }
                } else {
                    $entport = new EntPort();
                    $antesport = null;
                    $acaoport = "C";
                }

                $entport->id_emp2 = $id_empresa;
                $entport->id_ent = $ent->id;
                $entport->banco = $data['port_banco'];
                $entport->agencia = $data['port_agencia'];
                $entport->agenciadv = $data['port_agdv'];
                $entport->conta = $data['port_conta'];
                $entport->contadv = $data['port_cdv'];
                $entport->titular = $data['port_titular'];
                $entport->obs = $data['port_obs'];
                $entport->id_users = $id_user;
                if (!$entport->save) {
                    $json['message'] = $this->message->error("Erro ao salvar dados do Portador!")->render();
                    echo json_encode($json);
                    return;
                };
                $logport = new Log();
                $logport->registrarLog($acaoport, $entport->getEntity(), $entport->id, $antesport, $entport->data());
            }
        }

        $log = new Log();
        $log->registrarLog($acao, $tabelaEnt, $ent->id, $antes, $depois);

        if (ll_intValida($id_ent)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
            $json["redirect"] = url("ent/" . $tipo);
        } else {
            if (!empty($data['modalcli'])) {
                if ($data['modalcli'] == "novo") {
                    $json['form'] = "#cliente-os";
                } elseif ($data['modalcli'] == "pag") {
                    $json['form'] = "#fornecedor-pag";
                } else {
                    $json['form'] = "#cliente";
                }
                $json["message"] = $this->message->success("CADASTRADO COM SUCESSO!")->render();
                $json["idcli"] = $ent->id;
                $json["nomecli"] = $ent->nome;
            } else {
                $this->message->success("CADASTRADO COM SUCESSO!")->flash();
                $json["redirect"] = url("ent/" . $tipo);
            }
        }
        echo json_encode($json);
    }

    public function verificar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $entidade = $data['ent'];
        $cpfcnpj = $data['valor'];

        switch ($entidade) {
            case 1:
                $ent = "Cliente";
                break;
            case 2:
                $ent = "Fornecedor";
                break;
            case 3:
                $ent = "Colaborador";
                break;
            case 4:
                $ent = "Portador";
                break;
        }

        $verificar = (new Ent())->find(
            "cpfcnpj like :cpfcnpj",
            "cpfcnpj={$cpfcnpj}"
        )->count();

        if ($verificar > 0) {
            echo "existente";
        } else {
            echo "disponivel";
        };
    }

    public function reativar($data): void
    {
        $id_ent = ll_decode($data['id_ent']);

        if (ll_intValida($id_ent)) {
            $ent = (new Ent())->findById($id_ent);
            $ent->status = "A";
            $ent->id_users = $this->user->id;

            $antes = clone $ent->data();

            if ($ent->save()) {
                $this->message->success("REGISTRO REATIVADO COM SUCESSO")->flash();
                $json["reload"] = true;
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("U", $ent->getEntity(), $ent->id, $antes, $ent->data());
        }
    }

    public function excluir($data): void
    {
        $id_ent = ll_decode($data['id_ent']);

        if (ll_intValida($id_ent)) {
            $ent = (new Ent())->findById($id_ent);
            $acaoString = "EXCLUÍDO";
            if ($ent->tipo == 3) {
                $acaoString = "INATIVADO";
            }
            $ent->status = "I";
            $ent->id_users = $this->user->id;

            $antes = clone $ent->data();

            if ($ent->save()) {
                $this->message->warning("REGISTRO {$acaoString} COM SUCESSO")->flash();
                $json["reload"] = true;
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $ent->getEntity(), $ent->id, $antes, null);
        }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
