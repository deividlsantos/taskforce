<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use Source\Boot\Session;
use Source\Boot\SqlServerConn;
use Source\Models\Emp2;

/**
 * ##################
 * ###   STRING   ###
 * ##################
 */

/** * Conver moeda para Real  
 * @access public 
 * @param String $valor
 * @param String $digitos padrao = 2
 * @return String
 */
function moedaBR($valor, $digitos = 2)
{
    $valor = $valor !== null ? (float)$valor : 0.0;
    $digitos = (int)$digitos;
    return number_format($valor, $digitos, ",", ".");
}

/** * Converter moeda Real para banco mysql
 * @access public 
 * @param String $valor 
 * @return String
 */
function moedaSql($valor)
{
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);

    return $valor;
}

/** * Criptografa String 
 * @access public 
 * @param String $str 
 * @param String $qtd_quebra_str(não é necessário)
 * @return String
 */
function ll_encode($str, $qtd_quebra_str = 2)
{

    if (is_null($str) || trim($str) == '') {
        return $str;
    }

    $catacter_validos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $chaves = array(
        'ZrYBHUcfg6KOLPQzT5V4G3N2J1h0uIMsexybnjiAWmkoDRlpaSE9t8v7XCFdwq',
        'lONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LP',
        'ZS4qseiJIuv6M0QWg2lONApmRTXDaw5LPKycftYkon1VGE3jxdrz9CF87BHUbh',
        'lONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LP',
        'ONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPl',
        'kon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPlONApmRZS',
        'JIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPlONApmRZSkon3ji',
        'WgycftYCFTXD87xdrz94qseaw5LPlONApmRZSkon3jiJIBHUbhuv6M0Q21VGEK',
        'drz94qseaw5LPlONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87x',
        'D87drz94qseaw5LPlONApmRZSkoin3jJIBHUbhuv6M0Q21VGEKWgycftYCFTXx'
    );
    $chaves_dg_verificador = array('z', 'S', 'e', 'X', 'd', 'r', 'C', 'F', 't', 'V');

    $str_bloco = str_split($str, $qtd_quebra_str);
    $str_retorno = '';

    foreach ($str_bloco as $vlr) {
        $valor_rand = rand(0, 9);

        $str = strtr(
            $vlr,
            $catacter_validos,
            $chaves[$valor_rand]
        );

        $str_retorno .= $str . $chaves_dg_verificador[$valor_rand];
    }
    return strrev(base64_encode($str_retorno));
}


//ok
/** * DesCriptografa String 
 * @access public 
 * @param String $str 
 * @param String $qtd_quebra_str(não é necessário)
 * @return String
 */
function ll_decode($str, $qtd_quebra_str = 2)
{

    if (is_null($str) || trim($str) == '') {
        return $str;
    }

    $str = base64_decode(strrev($str));
    $catacter_validos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $chaves = array(
        'ZrYBHUcfg6KOLPQzT5V4G3N2J1h0uIMsexybnjiAWmkoDRlpaSE9t8v7XCFdwq',
        'lONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LP',
        'ZS4qseiJIuv6M0QWg2lONApmRTXDaw5LPKycftYkon1VGE3jxdrz9CF87BHUbh',
        'lONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LP',
        'ONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPl',
        'kon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPlONApmRZS',
        'JIBHUbhuv6M0Q21VGEKWgycftYCFTXD87xdrz94qseaw5LPlONApmRZSkon3ji',
        'WgycftYCFTXD87xdrz94qseaw5LPlONApmRZSkon3jiJIBHUbhuv6M0Q21VGEK',
        'drz94qseaw5LPlONApmRZSkon3jiJIBHUbhuv6M0Q21VGEKWgycftYCFTXD87x',
        'D87drz94qseaw5LPlONApmRZSkoin3jJIBHUbhuv6M0Q21VGEKWgycftYCFTXx'
    );
    $chaves_dg_verificador = array('z', 'S', 'e', 'X', 'd', 'r', 'C', 'F', 't', 'V');

    $str_bloco = str_split($str, $qtd_quebra_str + 1);
    $str_retorno = '';

    foreach ($str_bloco as $vlr) {
        $dg_verificador = array_search(substr($vlr, -1), $chaves_dg_verificador);
        //$vlr = trim(substr($vlr, 0, -1));
        $vlr = (substr($vlr, 0, -1));
        $str_retorno .= strtr(
            $vlr,
            $chaves[$dg_verificador],
            $catacter_validos
        );
    }
    return $str_retorno;
}

function traduzirDiaSemana($diaEmIngles)
{
    // Array de tradução de dias da semana
    $diasDaSemana = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];

    // Verifica se o dia em inglês está no array e retorna a tradução
    if (isset($diasDaSemana[$diaEmIngles])) {
        return $diasDaSemana[$diaEmIngles];
    } else {
        return 'Dia inválido'; // Caso o dia não seja válido
    }
}

function fmt_numeros($valor)
{
    // Converte para número decimal
    $numero = floatval($valor);

    // Se for um número inteiro (sem casas decimais significativas), remove o ponto e os zeros
    if (intval($numero) == $numero) {
        return intval($numero);
    }

    // Se houver casas decimais, troca ponto por vírgula
    return number_format($numero, 3, ',', '');
}

function float_br_to_us(string $valor): float
{
    $valor = str_replace('.', '', $valor); // Remove separador de milhar
    $valor = str_replace(',', '.', $valor); // Troca vírgula por ponto decimal
    return (float) $valor;
}

function float_us_to_br(float $valor): string
{
    return number_format($valor, 2, ',', '');
}


function str_to_single($palavra)
{
    $irregulares = [
        'mãos' => 'mão',
        'cães' => 'cão',
        'pães' => 'pão',
        'peixes' => 'peixe',
        'fósseis' => 'fóssil',
        'táxis' => 'táxi',
        'filiais' => 'filial', // Adicionando a regra para "Filiais"
    ];

    $original = $palavra;
    $minuscula = mb_strtolower($palavra);

    // Se a palavra estiver na lista de exceções, retorna diretamente
    if (isset($irregulares[$minuscula])) {
        $singular = $irregulares[$minuscula];
    } else {
        // Regras regulares de plural para singular
        $regras = [
            '/(ões)$/i' => 'ão',   // Ações → Ação
            '/(ães)$/i' => 'ão',   // Pães → Pão
            '/(éis)$/i' => 'el',   // Papéis → Papel
            '/(is)$/i'  => 'il',   // Fuzis → Fuzil
            '/(eis)$/i' => 'el',   // Anéis → Anel
            '/(ores)$/i' => 'or',  // Amores → Amor
            '/(ses)$/i' => 's',    // Meses → Mês
            '/(res)$/i' => 'r',    // Motores → Motor
            '/(ns)$/i' => 'm',     // Hiperlinks → Hiperlink
            '/(ais)$/i' => 'al',   // Filiais → Filial
            '/(eis)$/i' => 'ei',   // Reis → Rei
            '/(s)$/i' => ''        // Carros → Carro
        ];

        $singular = $minuscula; // Caso não haja substituição

        foreach ($regras as $regex => $substituto) {
            if (preg_match($regex, $minuscula)) {
                $singular = preg_replace($regex, $substituto, $minuscula);
                break;
            }
        }
    }

    // Preserva a capitalização original
    if (mb_strtoupper($original) === $original) {
        return mb_strtoupper($singular); // Tudo maiúsculo
    } elseif (mb_strtoupper(mb_substr($original, 0, 1)) . mb_substr($original, 1) === $original) {
        return mb_strtoupper(mb_substr($singular, 0, 1)) . mb_substr($singular, 1); // Primeira letra maiúscula
    }

    return $singular; // Tudo minúsculo
}


/**
 * ##################
 * ###   STYLE   ###
 * ##################
 */

/**
 * Função para retornar a cor de acordo com o status
 *
 * @param [type] $status
 * @return void
 */
function cor_status($status)
{
    $color = ($status == 'futuras') ? 'black' : (($status == 'pausadas') ? 'orange' : (($status == 'andamento') ? 'blue' : (($status == 'concluidas') ? 'green' : 'purple')));

    return $color;
}

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

/**
 * @param string $email
 * @return bool
 */
function is_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $password
 * @return bool
 */
function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN)) {
        return true;
    }

    return false;
}

/**
 * @param mixed $cpf
 * @return bool
 */
function validaCPF($cpf)
{
    // Remove caracteres especiais
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcula o primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

/** * Valida Inteiro
 * @access public 
 * @param String $valor
 * @return BOOL(true/false)
 */
function ll_intValida($valor)
{
    if (!filter_var($valor, FILTER_VALIDATE_INT)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Função para retornar apenas valores de horas do cartão de ponto.
 * @param mixed $time
 * @return bool|int
 */
function validaHoraPonto($time)
{
    $pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
    return preg_match($pattern, $time);
}

/**
 * Função que verifica se uma string contém um valor procurado
 * @param mixed $string - Valor completo
 * @param mixed $valor - Valor a ser encontrado
 * @return bool
 */
function str_contem($string, $valor)
{
    return strpos($string, $valor) !== false;
}

/**
 * Função que trava o código se houver caracteres maliciosos
 * @param mixed $string
 * @return boolean
 */
function str_verify($string): bool
{
    if (preg_match('/[<>]|<script|on\w+\s*=/', $string)) {
        return false;
    }
    return true;
}

function float_verify($numero, $decimais = 2)
{
    // Formata como string com 2 ou 3 casas e vírgula
    if (is_float($numero) || is_int($numero)) {
        $numero = number_format($numero, $decimais, ',', '.'); // ex: 1.00 => "1,00"
    }

    // Define o padrão para verificar
    $padrao = '/^\d{1,3}(\.\d{3}){0,1},\d{' . $decimais . '}$/'; // ajusta para o número de casas decimais desejado
    if (preg_match($padrao, $numero)) {
        return true;
    } else {
        return false;
    }
}


/**
 * ##################
 * ###   SENHAS   ###
 * ##################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }
    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

function gerarSenha($tamanho = 6)
{
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $senha = '';
    $max = strlen($caracteres) - 1;

    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $caracteres[random_int(0, $max)];
    }

    return $senha;
}

/**
 * ###############
 * ###   URL   ###
 * ###############
 */

/**
 * Retorna URL
 *
 * @param string|null $uri
 * @return string
 */
function url(string $uri = null): string
{
    if ($uri) {
        return URL_BASE . "/{$uri}";
    }
    return URL_BASE;
}

/**
 * @return string
 */
function url_back(): string
{
    return ($_SERVER['HTTP_REFERER'] ?? url());
}


/**
 * @param string $url
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}

/**
 * ################
 * ###   DATE   ###
 * ################
 */

/**
 * @param string $date
 * @param string $format
 * @return string
 */
function date_fmt(?string $date, string $format = "d/m/Y H\hi"): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format($format);
}

/**
 * @param string $date
 * @return string
 */
function date_fmt_br(?string $date): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format(CONF_DATE_BR);
}

/**
 * @param string $date
 * @return string
 */
function date_fmt_sql(?string $date): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format(CONF_DATE_APP);
}

/** * Retorna Data Subtrai Por QTD
 * @access public 
 * @param String $dataUS 
 * @param String $qtd 
 * @param String $Y_ou_M_ou_D 
 * @return String
 */
function data_Subtrair_Por_Qtd($dataUS, $qtd, $Y_ou_M_ou_D = 'D')
{
    $dataRETORNO = new DateTime($dataUS);
    $dataRETORNO->sub(new DateInterval('P' . $qtd . $Y_ou_M_ou_D));
    return $dataRETORNO->format('Y-m-d');
}



/**
 * Validar Data
 *
 * @param $date
 * @param string $format
 * @return bool
 */
function validate_date($date, $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * RETORNA NOME DO MÊS
 * @param mixed $month
 * @return string[]
 */
function retornaNomeMes($month)
{
    // Array com os nomes dos meses em português
    $months = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];

    // Verifica se o número do mês está dentro do intervalo válido
    if (array_key_exists($month, $months)) {
        return $months[$month];
    } else {
        return 'Número do mês inválido';
    }
};

function calcularDataPascoa($ano)
{
    $a = $ano % 19;
    $b = floor($ano / 100);
    $c = $ano % 100;
    $d = floor($b / 4);
    $e = $b % 4;
    $f = floor(($b + 8) / 25);
    $g = floor(($b - $f + 1) / 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = floor($c / 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = floor(($a + 11 * $h + 22 * $l) / 451);
    $mes = floor(($h + $l - 7 * $m + 114) / 31);
    $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

    return new DateTime("$ano-$mes-$dia");
}

function calcularSextaFeiraSanta($ano)
{
    $dataPascoa = calcularDataPascoa($ano);
    $dataSextaFeiraSanta = clone $dataPascoa;
    $dataSextaFeiraSanta->modify('-2 days');
    return $dataSextaFeiraSanta;
}

/**
 * Transforma a hora em segundos
 *
 * @param {HH:mm} $time
 * @return void
 */
function timeToSeconds($time): int
{
    list($hours, $minutes) = explode(':', $time);
    return ($hours * 3600) + ($minutes * 60);
}

function secondsToTime($seconds)
{
    $seconds = min($seconds, 86399);

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
}

function calculaDataRecorrente($dataInicial, $recorrencia, $forcarProximoCiclo = false)
{
    if (strtolower($recorrencia) === 'livre') {
        return $dataInicial;
    }

    $dataInicialObj = new DateTime($dataInicial);
    $dataAtual = new DateTime();
    $novaData = clone $dataInicialObj;

    if (is_int($recorrencia)) {
        while ($novaData <= $dataAtual) {
            $novaData->modify("+{$recorrencia} days");
        }

        if ($forcarProximoCiclo) {
            $novaData->modify("+{$recorrencia} days");
        }

        return $novaData->format('Y-m-d');
    }

    if (!is_string($recorrencia)) {
        throw new InvalidArgumentException("Recorrência inválida.");
    }

    switch (strtolower($recorrencia)) {
        case 'semanal':
            $interval = new DateInterval('P7D');
            break;
        case 'mensal':
            $interval = new DateInterval('P1M');
            break;
        case 'bimestral':
            $interval = new DateInterval('P2M');
            break;
        case 'trimestral':
            $interval = new DateInterval('P3M');
            break;
        case 'semestral':
            $interval = new DateInterval('P6M');
            break;
        case 'anual':
            $interval = new DateInterval('P1Y');
            break;
        default:
            throw new InvalidArgumentException("Recorrência inválida: $recorrencia");
    }

    while ($novaData <= $dataAtual) {
        $novaData->add($interval);
    }

    if ($forcarProximoCiclo) {
        $novaData->add($interval); // <--- Aqui é o que estava faltando
    }

    return $novaData->format('Y-m-d');
}

/**
 * ####################
 * ###   SESSIONS   ###
 * ####################
 */

function set_session(Session $session, Emp2 $emp)
{
    $session->set("authEmp", $emp->id);
    $session->set("mostraValorPdf", ll_encode($emp->mostraValorPdf));
    $session->set("servicosComMedicoes", ll_encode($emp->servicosComMedicoes));
    $session->set("os_financeiro_auto", ll_encode($emp->os_financeiro_auto));
    $session->set("mostraDataLegal", ll_encode($emp->mostraDataLegal));
    $session->set("bloqueia2tarefasPorOper", ll_encode($emp->bloqueia2tarefasPorOper));
    $session->set("equipamentoObrigatorio", ll_encode($emp->equipamentoObrigatorio));
    $session->set("servicosComEquipamentos", ll_encode($emp->servicosComEquipamentos));
}


/**
 * ####################
 * ###   REQUESTS   ###
 * ####################
 */

/**
 * @return string
 */
function csrf_input(): string
{
    $session = new \Source\Boot\Session();
    $session->csrf();
    return "<input type='hidden' name='csrf' value='" . ($_SESSION['csrf_token'] ?? "") . "'/>";
}

/**
 * @param $request
 * @return bool
 */
function csrf_verify($request): bool
{
    $session = new \Source\Boot\Session();
    if (empty($session->csrf_token) || empty($request['csrf']) || $request['csrf'] != $session->csrf_token) {
        return false;
    }
    return true;
}

/**
 * @return string|null
 */
function flash(): ?string
{
    $session = new Session();
    if ($flash = $session->flash()) {
        echo $flash;
    }
    return null;
}

/**
 * @param string $key
 * @param integer $limit
 * @param integer $seconds
 * @return boolean
 */
function request_limit(string $key, int $limit = 5, int $seconds = 60): bool
{
    $session = new Session();
    if ($session->has($key) && $session->$key->time >= time() && $session->$key->requests < $limit) {
        $session->set($key, [
            "time" => time() + $seconds,
            "requests" => $session->$key->requests + 1
        ]);
        return false;
    }

    if ($session->has($key) && $session->$key->time >= time() && $session->$key->requests >= $limit) {
        return true;
    }

    $session->set($key, [
        "time" => time() + $seconds,
        "requests" => 1
    ]);

    return false;
}

/**
 * @param string $field
 * @param string $value
 * @return boolean
 */
function request_repeat(string $field, string $value): bool
{
    $session = new Session();
    if ($session->has($field) && $session->$field == $value) {
        return true;
    }

    $session->set($field, $value);
    return false;
}


/**
 * ################
 * ###   CORE   ###
 * ################
 */



/**
 * Converte um objeto datalayer em array
 *
 * @param $objects
 * @return void
 */
function objectsToArray($objects)
{
    if (!is_array($objects)) {
        $objects = [$objects]; // Transforma um objeto único em um array
    }

    return array_map(function ($obj) {
        return (array)$obj->data; // Acessando a propriedade pública 'data'
    }, $objects);
}


/**
 * RETORNA DIAS DA SEMANA PRA CADA MÊS
 * @param mixed $year
 * @param mixed $month
 * @return array<string|null>[]
 */
function retornaCalendario($year, $month, $feriadosDoBanco)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $holidays = retornaFeriados($year, $feriadosDoBanco);
    $monthDays = [];

    // Mapeamento dos dias da semana para os códigos correspondentes
    $weekdayCodes = [
        'Monday' => 0,
        'Tuesday' => 1,
        'Wednesday' => 2,
        'Thursday' => 3,
        'Friday' => 4,
        'Saturday' => 5,
        'Sunday' => 6,
    ];

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $weekday = date('l', strtotime($date)); // Obtém o dia da semana

        $isHoliday = isset($holidays[$date]) ? $holidays[$date] : null;

        $monthDays[] = [
            'data' => $date,
            'dia_semana' => $weekday,
            'feriado' => $isHoliday,
            'cod_dia_semana' => $weekdayCodes[$weekday], // Adiciona o código do dia da semana
        ];
    }

    return $monthDays;
}

function retornaFeriados($year, $feriadosDoBanco)
{
    $feriados = [];
    foreach ($feriadosDoBanco as $row) {
        $recorrente = 0;
        $padrao = 0;
        // Se o feriado for recorrente, ajuste o ano
        $data = new DateTime($row->dias);
        $id = $row->id;
        if ($row->recorrente) {
            $data->setDate($year, $data->format('m'), $data->format('d'));
            $recorrente = 1;
        }
        if ($row->padrao) {
            $padrao = 1;
        }
        $feriados[$data->format('Y-m-d')] = $recorrente . "@" . $row->descricao . "@" . $padrao . "@" . $id;
    }

    // Adicionar Sexta-feira Santa
    $sextaFeiraSanta = calcularSextaFeiraSanta($year);
    $feriados[$sextaFeiraSanta->format('Y-m-d')] = '1@Sexta-feira Santa@1@0';

    return $feriados;
}

function set_indice_dias($valores)
{
    // Definindo o mapeamento de valores para índices
    $mapeamentoIndices = [
        'Segunda-feira' => 0,
        'Terça-feira' => 1,
        'Quarta-feira' => 2,
        'Quinta-feira' => 3,
        'Sexta-feira' => 4,
        'Sábado' => 5,
        'Domingo' => 6,
        'Segunda à Sexta' => 7,
        'Segunda à Sábado' => 8
    ];

    // Separando os valores usando explode
    $arrayValores = explode(',', $valores);

    // Criando o novo array com índices personalizados
    $novoArray = [];

    foreach ($arrayValores as $valor) {
        if (isset($mapeamentoIndices[$valor])) {
            $novoArray[$mapeamentoIndices[$valor]] = $valor;
        }
    }

    return $novoArray;
}

/**
 * Função para verificar se o turno engloba o sábado ou o domingo
 * @param mixed $numero variável que contem o número pra ser verificado
 * @param mixed $lista variável que contem string
 * @return bool
 */
function verificaNumero($numero, $lista)
{
    if (empty($lista)) {
        return false;
    }

    $listaArray = array_map('trim', explode(',', $lista));

    return in_array($numero, $listaArray);
}

/**
 * @param mixed $value
 * @param mixed $month
 * @param mixed $year
 * @return string|null
 */
function validateDateRange($value, $month, $year)
{
    // Verifica se o formato é 'aa-bb'
    if (!preg_match('/^(\d{1,2})-(\d{1,2})$/', $value, $matches)) {
        return "Formato inválido, deve ser 'dd-dd'(ex: 01-15).";
    }

    $aa = (int) $matches[1];
    $bb = (int) $matches[2];

    // Verifica se aa e bb estão dentro do limite
    if ($aa < 0 || $bb < 0) {
        return "os dias não podem ser menores que 0.";
    }

    // Verifica se aa é maior que bb
    if ($aa > $bb) {
        return "O dia de ínicio não pode ser menor que o dia do término.";
    }

    // Verifica os dias máximos do mês
    $maxDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    if ($aa > $maxDays || $bb > $maxDays) {
        return "os valores dos dias não podem ultrapassar o número máximo de dias do mês.";
    }

    return null; // Tudo certo
}

/**
 * Função para calcular horas trabalhadas no dia
 * @param mixed $ini - Início do expediente
 * @param mixed $intIni - Saída pro almoço
 * @param mixed $intFim - Retorno do almoço
 * @param mixed $fim - Fim do expediente
 * @return array
 */
function calcHorasTrabalhadas($ini, $intIni, $intFim, $fim)
{
    // Cria objetos DateTime para os horários de início e fim
    $startMorning = new DateTime($ini);
    $endAfternoon = new DateTime($fim);

    // Calcula os intervalos
    $intervalMorning = $startMorning->diff($endAfternoon);
    $totalMinutes = $intervalMorning->h * 60 + $intervalMorning->i;

    // Se intIni e intFim não forem 0, calcula o intervalo de almoço
    if ($intIni != '00:00' && $intFim != '00:00') {
        $endMorning = new DateTime($intIni);
        $startAfternoon = new DateTime($intFim);

        // Recalcula os intervalos
        $intervalMorning = $startMorning->diff($endMorning);
        $intervalAfternoon = $startAfternoon->diff($endAfternoon);

        // Soma os intervalos
        $totalMinutes = ($intervalMorning->h * 60 + $intervalMorning->i) + ($intervalAfternoon->h * 60 + $intervalAfternoon->i);
    }

    // Converte para horas e minutos
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;

    return [$hours, $minutes];
}



/** * GERAR PDF
 * @access public 
 * @param String $html - conteúdo do PDF  
 * @param String $nomeArquivo - nome do Arquivo Gerado  
 * @param String $modoPapel - R = retrato, P = paisagem  
 * @param String $retornar - D = download, P = print na tela, S = salvar em pasta   
 * @param String $caminho - pasta onde será salvo se $retornar = S   
 * @return SE OK String com nome, se não FALSE
 */
function ll_pdfGerar($html, $nomeArquivo = '', $modoPapel = 'R', $retornar = 'D', $caminho = '', $txtRodape = '')
{

    $modoPapel = strtoupper($modoPapel);
    $retornar  = strtoupper($retornar);

    if ($nomeArquivo == '') {
        $nomeArquivo = md5(uniqid(time())) . '.pdf';
    } else {
        $nomeArquivo = $nomeArquivo . '.pdf';
    }

    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);

    if ($modoPapel == "P") {
        $dompdf->setPaper("A4", "landscape"); // Altera o papel para modo paisagem.
    } else {
        $dompdf->setPaper("A4", "portrait"); // Altera o papel para modo paisagem.

    }


    $dompdf->loadHtml($html);
    $dompdf->render();

    $texto = "Powered by Taskforce - " . $txtRodape;

    // ® símbolo pra quando registrar a marca
    // Adicionar rodapé com numeração de páginas
    $canvas = $dompdf->getCanvas();
    $height = $canvas->get_height();
    $canvas->page_text(520, $height - 30, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 8, [0, 0, 0]);
    $canvas->page_text(30, $height - 30, $texto, null, 8, [0, 0, 0]);

    if ($retornar == 'S') {
        $pdf = $dompdf->output();

        $arquivo = $caminho . $nomeArquivo;

        if (file_put_contents($arquivo, $pdf)) { //Tenta salvar o pdf gerado
            return $nomeArquivo;
        } else {
            return false;
        }
    } else if ($retornar == 'D') {
        $dompdf->stream(
            $nomeArquivo,
            array(
                "Attachment" => true
            )
        );
    } else if ($retornar == 'P') {
        $dompdf->stream(
            $nomeArquivo,
            array(
                "Attachment" => false
            )
        );
    }
}

/**
 * #######################################################################
 * ###   FUNÇÕES EXCLUSIVAS PARA VERIFICAÇÃO DE PAGAMENTO DO SISTEMA   ###
 * #######################################################################
 */


/** * Criptografa Números 
 * @access public 
 * @param String $str 
 * @param String $tipo 'C' para criptografar e 'D' para descriptografar
 * @return String
 */
function cnpj_cript($str, $tipo)
{
    $numeros = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '-', '/', ','];
    $letras  = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f'];

    $len = strlen($str);

    // Escolhe o sentido
    if ($tipo === 'C') {
        // Criptografar (números/formatação -> letras)
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < count($numeros); $j++) {
                if ($str[$i] === $numeros[$j]) {
                    $str[$i] = $letras[$j];
                    break;
                }
            }
        }
    } elseif ($tipo === 'D') {
        // Descriptografar (letras -> números/formatação)
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < count($letras); $j++) {
                if ($str[$i] === $letras[$j]) {
                    $str[$i] = $numeros[$j];
                    break;
                }
            }
        }
    }

    return trim($str);
}

/**
 * Função para verificar a permissão de uso do sistema
 * @param mixed $cnpj_emp2
 */
function verificaPermissao($cnpj_emp2)
{
    $conn = SqlServerConn::connect();

    if (!$conn) {
        return null;
    }

    if (defined(('ENV_TEST') && ENV_TEST) || (defined('FREETDS_ENABLED') && FREETDS_ENABLED)) {
        // FreeTDS não suporta bind do jeito que você queria
        $cnpj = $cnpj_emp2;
        $sql = "
            SELECT TOP 1 Expiracao
            FROM TCEmp
            WHERE CNPJ = '{$cnpj}'
              AND Expiracao IS NOT NULL
            ORDER BY Expiracao DESC
        ";
        $stmt = $conn->query($sql);
    } else {
        // Driver SQL Server oficial, bind normal funciona
        $stmt = $conn->prepare("
            SELECT TOP 1 Expiracao
            FROM TCEmp
            WHERE CNPJ = :cnpj
              AND Expiracao IS NOT NULL
            ORDER BY Expiracao DESC
        ");
        $stmt->bindValue(':cnpj', $cnpj_emp2);
        $stmt->execute();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['Expiracao'] : null;
}

/**
 * Verifica Expiração da licença
 * @param mixed $data
 * @return int 1 = Não faz nada, 2 = Avisar que está próximo do vencimento, 3 = Bloquear o sistema
 */
function verificaExpiracao($data)
{
    // 1) Se for nula ou vazia
    if (empty($data)) {
        return 1;
    }

    // Converte a string do banco para objeto DateTime
    try {
        $dataBanco = new DateTime($data);
    } catch (Exception $e) {
        // Se por algum motivo a data vier inválida, trate como nula
        return 1;
    }

    $hoje = new DateTime(); // data e hora atual
    // Zera a parte de hora para comparar apenas datas, caso queira
    $hoje->setTime(0, 0, 0);
    $dataBanco->setTime(0, 0, 0);

    // 2) Se dataBanco >= hoje
    if ($dataBanco >= $hoje) {
        // Verifica se está dentro de 5 dias
        $diferenca = $hoje->diff($dataBanco)->days;
        if ($diferenca <= 5) {
            return 2;
        }

        return 2;
    }

    // 3) Se dataBanco < hoje
    return 3;
}
