<?php
namespace Source\Boot;

class WhatsAppService
{
    /**
     * Envia uma mensagem via WhatsApp chamando o servidor Node local.
     *
     * @param string $numero Número no formato internacional (ex: 559999999999@c.us)
     * @param string $mensagem Texto da mensagem
     * @return bool Retorna true se o envio foi bem-sucedido
     */
    public static function enviarMensagem(string $numero, string $mensagem): bool
    {
        $url = 'http://localhost:3000/enviar'; // seu servidor Node

        $data = [
            'numero' => $numero,
            'mensagem' => $mensagem
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Opcional: registrar log
        file_put_contents(
            __DIR__ . '/../../logWhats/whatsapp.log',
            "[" . date('Y-m-d H:i:s') . "] {$numero} => {$mensagem} | HTTP {$httpCode}\n",
            FILE_APPEND
        );

        return $httpCode === 200;
    }
}
