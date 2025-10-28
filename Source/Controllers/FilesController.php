<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Models\Arq;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\Emp;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Func;
use Source\Models\Horas;
use Source\Models\Log;
use Source\Models\Turno;

class FilesController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->arquivos != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        $front = [
            "titulo" => "Arquivos - Taskforce",
            "user" => $this->user,
            "tituloPai" => "Arquivos"
        ];

        echo $this->view->render("tcsistemas.ponto/files/files", [
            "front" => $front
        ]);
    }

    public function select(): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;


        $front = [
            "titulo" => "Arquivos - Taskforce",
            "user" => $this->user,
            "secTit" => "Selecione:"
        ];

        echo $this->view->render("tcsistemas.ponto/files/filesSelect", [
            "front" => $front
        ]);
    }

    public function lista(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_empresa);

        $arq = (new Arq())->find()->order("id desc")->fetch(true);

        $func = (new Ent())->find(
            "tipo = :tipo",
            "tipo=3"
        )->fetch(true);

        $front = [
            "titulo" => "Arquivos - Taskforce",
            "user" => $this->user,
            "secTit" => "Arquivos"
        ];

        echo $this->view->render("tcsistemas.ponto/files/filesList", [
            "front" => $front,
            "arquivo" => $arq,
            "func" => $func,
            "emp" => $emp
        ]);
    }

    public function filesFormFunc(?array $data): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        $func = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=3&status=A"
        )->fetch(true);

        $front = [
            "titulo" => "Arquivos - Taskforce",
            "user" => $this->user,
            "secTit" => "Incluir Documentos do Colaborador"
        ];

        echo $this->view->render("tcsistemas.ponto/files/filesFuncCad", [
            "front" => $front,
            "func" => $func
        ]);
    }

    public function filesFormEmp(?array $data): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;


        $front = [
            "titulo" => "Arquivos - Taskforce",
            "user" => $this->user,
            "secTit" => "Incluir Documentos da Empresa"
        ];

        echo $this->view->render("tcsistemas.ponto/files/filesEmpCad", [
            "front" => $front
        ]);
    }



    public function salvar(array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (empty($_FILES)) {
            $json['message'] = $this->message->error("ERRO. Nenhum arquivo selecionado!")->render();
            echo json_encode($json);
            return;
        }

        $arquivoTmp = $_FILES['arquivo']['tmp_name'];
        $arquivoNome = $_FILES['arquivo']['name'];
        $tipoMime = mime_content_type($arquivoTmp);
        $extensao = strtolower(pathinfo($arquivoNome, PATHINFO_EXTENSION));

        if (!(strpos($tipoMime, 'image/') === 0 || in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']) || $tipoMime === 'application/pdf' || $extensao === 'pdf')) {
            $json['message'] = $this->message->error("ERRO. Não é permitido arquivos que não sejam imagens ou pdf")->render();
            echo json_encode($json);
            return;
        }

        if (!empty($_FILES["arquivo"]["name"]) && !empty($data['funcionario'])) {

            if (empty($data["func"])) {
                $json['message'] = $this->message->error("ERRO. Selecione um Colaborador!")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data["categoria"])) {
                $json['message'] = $this->message->error("ERRO. Selecione uma categoria!")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['descricao'])) {
                $json['message'] = $this->message->error("Campo 'DESCRIÇÃO' é obrigatório!")->render();
                echo json_encode($json);
                return;

                if (!filter_var($data['descricao'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
                    echo json_encode($json);
                    return;
                }
            }

            $arquivo = $_FILES["arquivo"];

            $chvArq = md5(uniqid(rand(), true));
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

            $arq = new Arq();

            $arq->id_emp2 = $id_empresa;
            $arq->id_func = ll_decode($data["func"]);
            $arq->tipo = "F";
            $arq->categoria = $data["categoria"];
            $arq->arquivo = $arquivo["name"];
            $arq->descricao = $data["descricao"];
            $arq->nome_arquivo = $id_empresa . "_" . $chvArq . "_" . ll_decode($data["func"]);
            $arq->extensao = $extensao;
            $arq->chave = $chvArq;
            $arq->id_users = $id_user;

            $ftpConn = ftp_connect(ll_decode(FTP_SERVER));

            if ($ftpConn) {
                $ftpLogin = ftp_login($ftpConn, ll_decode(FTP_USER), ll_decode(FTP_PASS));
                if ($ftpLogin) {
                    $ftpOrigem = $arquivo['tmp_name'];

                    // Verifique se o arquivo de origem existe
                    if (!file_exists($ftpOrigem)) {
                        $json["message"] = $this->message->warning("Arquivo de origem não encontrado: $ftpOrigem")->render();
                        echo json_encode($json);
                        exit;
                    }

                    $ftpDestino = '/tcponto/docs/emp_' . $id_empresa . "/" . $arq->nome_arquivo . '.' . $extensao;

                    $ftpDiretorio = dirname($ftpDestino);

                    if (!@ftp_chdir($ftpConn, $ftpDiretorio)) {
                        if (!ftp_mkdir($ftpConn, $ftpDiretorio)) {
                            ftp_close($ftpConn);
                            $json["message"] = $this->message->warning("ERRO AO CRIAR O DIRETÓRIO")->render();
                            echo json_encode($json);
                            exit;
                        }
                    }

                    ftp_chdir($ftpConn, "/");

                    ftp_pasv($ftpConn, true);

                    $envio = ftp_put($ftpConn, $ftpDestino, $ftpOrigem, FTP_BINARY);

                    if ($envio) {
                        if (!$arq->save) {
                            $json["message"] = $this->message->warning("ERRO AO CADASTRAR")->render();
                            echo json_encode($json);
                            exit;
                        }
                        $log = new Log();
                        $log->registrarLog("C", $arq->getEntity(), $arq->id, null, $arq->data());
                    } else {
                        ftp_close($ftpConn);
                        $json["message"] = $this->message->warning("ERRO NO ENVIO")->render();
                        echo json_encode($json);
                        exit;
                    }
                } else {
                    ftp_close($ftpConn);
                }
                ftp_close($ftpConn);
            }
        }

        if (!empty($_FILES["arquivo"]["name"]) && !empty($data['empresa'])) {

            $arquivo = $_FILES["arquivo"];

            $chvArq = md5(uniqid(rand(), true));
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

            $arq = new Arq();

            $arq->id_emp2 = $id_empresa;
            $arq->tipo = "E";
            $arq->arquivo = $arquivo["name"];
            $arq->descricao = $data["descricao"];
            $arq->nome_arquivo = $id_empresa . "_" . $chvArq;
            $arq->extensao = $extensao;
            $arq->chave = $chvArq;
            $arq->id_users = $id_user;

            $ftpConn = ftp_connect(ll_decode(FTP_SERVER));

            if ($ftpConn) {
                $ftpLogin = ftp_login($ftpConn, ll_decode(FTP_USER), ll_decode(FTP_PASS));
                if ($ftpLogin) {
                    $ftpOrigem = $arquivo['tmp_name'];

                    // Verifique se o arquivo de origem existe
                    if (!file_exists($ftpOrigem)) {
                        $json["message"] = $this->message->warning("Arquivo de origem não encontrado: $ftpOrigem")->render();
                        echo json_encode($json);
                        exit;
                    }

                    $ftpDestino = '/tcponto/docs/emp_' . $id_empresa . "/" . $arq->nome_arquivo . '.' . $extensao;

                    $ftpDiretorio = dirname($ftpDestino);

                    if (!@ftp_chdir($ftpConn, $ftpDiretorio)) {
                        if (!ftp_mkdir($ftpConn, $ftpDiretorio)) {
                            ftp_close($ftpConn);
                            $json["message"] = $this->message->warning("ERRO AO CRIAR O DIRETÓRIO")->render();
                            echo json_encode($json);
                            exit;
                        }
                    }

                    ftp_chdir($ftpConn, "/");

                    ftp_pasv($ftpConn, true);

                    $envio = ftp_put($ftpConn, $ftpDestino, $ftpOrigem, FTP_BINARY);

                    if ($envio) {
                        if (!$arq->save) {
                            $json["message"] = $this->message->warning("ERRO AO CADASTRAR")->render();
                            echo json_encode($json);
                            exit;
                        }
                        $log = new Log();
                        $log->registrarLog("C", $arq->getEntity(), $arq->id, null, $arq->data());
                    } else {
                        ftp_close($ftpConn);
                        $json["message"] = $this->message->warning("ERRO NO ENVIO")->render();
                        echo json_encode($json);
                        exit;
                    }
                } else {
                    ftp_close($ftpConn);
                }
                ftp_close($ftpConn);
            }
        }

        $this->message->success("ARQUIVO INCLUÍDO COM SUCESSO!")->flash();

        $json["redirect"] = url("files/lista");
        echo json_encode($json);
    }

    public function apagar($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;
        $id_arq = ll_decode($data['id_arq']);

        if (ll_intValida($id_arq)) {
            $arq = (new Arq())->findById($id_arq);
            $antes = clone $arq->data();

            $ftpConn = ftp_connect(ll_decode(FTP_SERVER));
            if ($ftpConn) {
                $ftpLogin = ftp_login($ftpConn, ll_decode(FTP_USER), ll_decode(FTP_PASS));
                if ($ftpLogin) {
                    $ftpArquivo = '/tcponto/docs/emp_' . $id_empresa . "/" . $arq->nome_arquivo . '.' . $arq->extensao;

                    ftp_pasv($ftpConn, true);

                    if (ftp_delete($ftpConn, $ftpArquivo) == true) {
                        if ($arq->destroy()) {
                            $this->message->warning("ARQUIVO EXCLUÍDO COM SUCESSO")->flash();
                            $json["redirect"] = url("files/lista");
                            echo json_encode($json);
                        }
                        $log = new Log();
                        $log->registrarLog("D", $arq->getEntity(), $arq->id, $antes, null);
                    } else {
                        echo "ERRO";
                    }
                } else {
                    ftp_close($ftpConn);
                }
                ftp_close($ftpConn);
            }
        }
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
