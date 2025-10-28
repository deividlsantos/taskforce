<?php

namespace Source\Controllers;

use Source\Boot\WhatsAppService;
use Source\Models\Expira;

class ExpiracaoController
{
    public function enviarAvisos()
    {
        $empresas = Expira::buscarInativas();

        if (empty($empresas)) {
            echo json_encode(['mensagem' => 'Nenhuma empresa inativa há mais de 2 dias.']);
            return;
        }

        $numeros = [
            //"5517988159292@c.us",
            "5517996248371@c.us"
        ]; // número formatado corretamente

        // Monta a mensagem única
        $mensagem = "Empresas inativas há mais de 2 dias:\n\n";

        foreach ($empresas as $empresa) {
            $cnpj = cnpj_cript($empresa['CNPJ'], "D");
            $mensagem .= "- CNPJ: {$cnpj} está inativa desde {$empresa['ultexec']}\n";
        }

        // Envia mensagem única
        foreach ($numeros as $numero) {
            $enviado = WhatsAppService::enviarMensagem($numero, $mensagem);
        }

        // Retorna resultado
        $resultado = [
            'mensagem' => $mensagem,
            'enviado' => $enviado,
            'total_empresas' => count($empresas)
        ];

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
