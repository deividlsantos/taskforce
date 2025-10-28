<?php

namespace Source\Controllers;


use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Obras;

class TempController extends Controller
{
    public function __construct()
    {
        exit;
        parent::__construct();

        if ($this->user->tipo != 5) {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->find(null, null, "*", false)->fetch(true);

        $front = [
            "titulo" => "Materiais - Taskforce",
            "user" => $this->user,
            "secTit" => "Temp"
        ];

        echo $this->view->render("tcsistemas.temporario/temp", [
            "front" => $front,
            "empresa" => $empresa
        ]);
    }


    public function atualizaRegistros($data): void
    {
        $id = $data['temp_emp2'];

        $fa = (new Obras())->find("id_emp2 = :id", "id={$data['temp_emp2']}", "*", false)->fetch(true);
        
        foreach ($fa as $vlr) {

            $vlr->proprietario = (new Ent())->findById($vlr->id_ent_cli)->nome;
            if(!$vlr->save()){
                $this->message->error("Erro ao atualizar o registro {$vlr->id}")->flash();
                echo json_encode($this->message->render());
                return;
            }
            
        }

        $json['message'] = $this->message->success("Registros atualizados com sucesso!")->render();
        echo json_encode($json);
        return;
    }



    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
