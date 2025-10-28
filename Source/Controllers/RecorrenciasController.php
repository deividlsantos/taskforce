<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\RecorrenciasCliServ;
use Source\Models\Setor;

class RecorrenciasController extends Controller
{


    public function __construct()
    {
        parent::__construct();
    }
    
    // public function index(): void
    // {
    //     $id_user = $this->user->id;
    //     $id_empresa = $this->user->id_emp2;


    // }

    public function verifica($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $recorrencia = (new RecorrenciasCliServ())->findRecorrencia($data['cliente'], $data['servico']);        

        if ($recorrencia) {
            $json['status'] = true;
            $json['recorrencia'] = $recorrencia->id_recorrencia;
            $json['dia'] = $recorrencia->dia;
        } else {
            $json['status'] = false;            
        }
        echo json_encode($json);
    }    

    public function excluir($data): void
    {
        // $id_setor = ll_decode($data['id_setor']);

        // if (ll_intValida($id_setor)) {
        //     $setor = (new Setor())->findById($id_setor);
        //     $antes = clone $setor->data();

        //     if (!$setor->destroy()) {
        //         $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
        //         echo json_encode($json);
        //     }

        //     $log = new Log();
        //     $log->registrarLog("D", "setor", $setor->id, $antes, null);

        //     $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
        //     $json["reload"] = true;
        //     echo json_encode($json);
        // }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
