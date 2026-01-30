<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Materiais;
use DOMDocument;
use Source\Models\Municipios;

class NfeController extends Controller
{
    protected $view;

    public function __construct()
    {
        parent::__construct();
        $this->view = new Engine(__DIR__ . "/../Views", "php");
    }

    public function index(): void
    {

        $id_emp2 = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_emp2);        

        $municipio = (new Municipios())->findById($emp->municipio_id);

        $front = [
            "titulo"   => "Gerar NFe",
            "user"     => $this->user,
            "secTitle" => "NFe"
        ];

        echo $this->view->render("tcsistemas.nfe/gerarXml", [
            "front" => $front,
            "emp"   => $emp,
            "municipio" => $municipio
        ]);
    }

    public function formXml(): void
    {
        $id_emp2 = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_emp2);

        $front = [
            "titulo"   => "Gerar NFe",
            "user"     => $this->user,
            "secTitle" => "NFe"
        ];
        echo $this->view->render("tcsistemas.nfe/formNfe", [
            "front" => $front,
            "emp"   => $emp
        ]);
    }

    public function danfe(): void
    {
        $id_emp2 = $this->user->id_emp2;

        $emp = (new Emp2())->findById($id_emp2);
        $ent = (new Ent())->find('tipo = :tipo', 'tipo=1')->fetch(true); // destinatário fixo para teste
        $front = [
            "titulo"   => "Gerar NFe",
            "user"     => $this->user,
            "secTitle" => "NFe"
        ];
        echo $this->view->render("tcsistemas.nfe/danfe", [
            "front" => $front,
            "emp"   => $emp,
            "ent"   => $ent[0] // primeiro destinatário encontrado
        ]);
    }
    /* =====================================================
     * 1 — CHAVE DE ACESSO
     * ===================================================== */
    private function gerarChaveNfe(array $d): string
    {
        if (empty($d['cnf'])) {
            $d['cnf'] = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        }

        $cUF    = '35'; // SP
        $anoMes = date('ym', strtotime($d['data_emissao']));
        $cnpj   = str_pad(preg_replace('/\D/', '', $d['emit_cnpj']), 14, '0', STR_PAD_LEFT);
        $mod    = '55';
        $serie  = str_pad($d['serie'], 3, '0', STR_PAD_LEFT);
        $numero = str_pad($d['numero_nf'], 9, '0', STR_PAD_LEFT);
        $tpEmis = '1';

        // cNF: exatamente 8 dígitos (já salvo no banco)
        $cNF = str_pad(preg_replace('/\D/', '', $d['cnf']), 8, '0', STR_PAD_LEFT);

        $base = $cUF
            . $anoMes
            . $cnpj
            . $mod
            . $serie
            . $numero
            . $tpEmis
            . $cNF;

        return $base . $this->calcularDV($base);
    }




    private function calcularDV(string $chave): int
    {
        $peso = 2;
        $soma = 0;

        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += $chave[$i] * $peso;
            $peso = ($peso == 9 ? 2 : $peso + 1);
        }

        $dv = 11 - ($soma % 11);
        return ($dv >= 10 ? 0 : $dv);
    }

    function montarDadosDaNfe(array $d, string $chave, string $dte, string $dts): array
    {
        /* ===================== */
        /* IDE                  */
        /* ===================== */
        $dados = [
            'id'        => 'NFe' . $chave,
            'cUF'       => substr($chave, 0, 2),
            'cNF'       => str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
            'natOp'     => $d['natOp'],
            'mod'       => '55',
            'serie'     => (int)$d['serie'],
            'nNF'       => (int)$d['numero_nf'],
            'dhEmi'     => $dte . 'T' . date('H:i:sP'),
            'tpNF'      => '1',
            'idDest'    => '1',
            'cMunFG'    => '3550308',
            'tpImp'     => '1',
            'tpEmis'    => '1',
            'tpAmb'     => '2',
            'finNFe'    => '1',
            'indFinal'  => '0',
            'indPres'   => '1',
            'procEmi'   => '0',
            'verProc'   => '1.0',
        ];

        /* ===================== */
        /* EMITENTE             */
        /* ===================== */
        $dados['emit'] = [
            'CNPJ' => preg_replace('/\D/', '', $d['emit_cnpj']),
            'xNome' => $d['emit_razao'],
            'IE'   => $d['emit_ie'],
            'CRT'  => '1',
            'ender' => [
                'xLgr'    => $d['emit_endereco'],
                'nro'     => $d['emit_num'],
                'xBairro' => $d['emit_bairro'],
                'cMun'    => '3550308',
                'xMun'    => $d['emit_cidade'],
                'UF'      => $d['emit_estado'],
                'CEP'     => preg_replace('/\D/', '', $d['emit_cep']),
                'cPais'   => '1058',
                'xPais'   => 'BRASIL',
            ]
        ];

        /* ===================== */
        /* DESTINATÁRIO         */
        /* ===================== */
        $dados['dest'] = [
            'CNPJ' => preg_replace('/\D/', '', $d['dest_cnpj']),
            'xNome' => $d['dest_razao'],
            'indIEDest' => '9',
            'ender' => [
                'xLgr'    => $d['dest_endereco'],
                'nro'     => $d['dest_num'],
                'xBairro' => $d['dest_bairro'],
                'cMun'    => '3550308',
                'xMun'    => $d['dest_cidade'],
                'UF'      => $d['dest_estado'],
                'CEP'     => preg_replace('/\D/', '', $d['dest_cep']),
                'cPais'   => '1058',
                'xPais'   => 'BRASIL',
            ]
        ];

        /* ===================== */
        /* ITENS / PRODUTOS     */
        /* ===================== */
        $dados['itens'] = [];

        foreach ($d['prod_desc'] as $i => $desc) {

            $qtd   = (float)$d['prod_qtd'][$i];
            $valor = (float)$d['prod_valor'][$i];
            $total = round($qtd * $valor, 2);

            $dados['itens'][] = [
                'prod' => [
                    'cProd'   => $i + 1,
                    'cEAN'    => 'SEM GTIN',
                    'xProd'   => $desc,
                    'NCM'     => $d['prod_ncm'][$i] ?? '00000000',
                    'CFOP'    => '5102',
                    'uCom'    => 'UN',
                    'qCom'    => number_format($qtd, 4, '.', ''),
                    'vUnCom'  => number_format($valor, 2, '.', ''),
                    'vProd'   => number_format($total, 2, '.', ''),
                    'indTot'  => '1',
                ],
                'icms' => [
                    'tag' => 'ICMSSN102',
                    'dados' => [
                        'orig'  => '0',
                        'CSOSN' => '102'
                    ]
                ]
            ];
        }

        /* ===================== */
        /* TOTAIS               */
        /* ===================== */
        $vProd = array_sum(array_column(array_map(
            fn($i) => ['v' => (float)$i['prod']['vProd']],
            $dados['itens']
        ), 'v'));

        $dados['total'] = [
            'vBC'     => '0.00',
            'vICMS'   => '0.00',
            'vBCST'   => '0.00',
            'vST'     => '0.00',
            'vProd'   => number_format($vProd, 2, '.', ''),
            'vFrete'  => '0.00',
            'vSeg'    => '0.00',
            'vDesc'   => '0.00',
            'vII'     => '0.00',
            'vIPI'    => '0.00',
            'vPIS'    => '0.00',
            'vCOFINS' => '0.00',
            'vOutro'  => '0.00',
            'vNF'     => number_format($vProd, 2, '.', ''),
        ];

        /* ===================== */
        /* PAGAMENTO            */
        /* ===================== */
        $dados['pag'] = [
            'indPag' => '0',
            'tPag'   => '90',
            'vPag'   => number_format($vProd, 2, '.', ''),
        ];

        return $dados;
    }

    public function produtoBusca(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["results" => []]);
            return;
        }

        // 🔹 termo digitado no Select2
        $termo = trim($_POST['termo'] ?? '');

        // 🔹 parâmetros base
        $params = "";
        $where  = "1 = 1";

        // 🔍 Filtra SOMENTE pelo nome (descricao)
        if ($termo !== '') {
            $where .= " AND descricao LIKE :termo";
            $params .= "&termo=%{$termo}%";
        }

        // 🔎 Busca no Model Materiais
        $materiais = (new Materiais())
            ->find($where, $params)
            ->fetch(true);

        $results = [];

        // 🔁 Monta o retorno do Select2
        if ($materiais) {
            foreach ($materiais as $item) {
                $results[] = [
                    "id"     => $item->id,
                    "text"   => $item->descricao, // texto exibido
                    "valor"   => $item->valor
                ];
            }
        }

        echo json_encode([
            "results" => $results
        ]);
    }

    /* =====================================================
     * 2 — MONTA XML (SEM ASSINATURA)
     * ===================================================== */
    /**
     * Monta o XML da NF-e (modelo 55 / versão 4.00)
     * Usa exatamente as variáveis atuais do sistema
     */
    function montarXmlNFe(array $d, string $chave, string $dte, string $dts): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $dte = date('Y-m-d', strtotime($d['data_emissao'] ?? date('Y-m-d')));
        $dts = date('Y-m-d', strtotime($d['data_saida'] ?? date('Y-m-d')));

        $ns = 'http://www.portalfiscal.inf.br/nfe';

        /* ===================== */
        /* RAIZ                  */
        /* ===================== */
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $ns = 'http://www.portalfiscal.inf.br/nfe';

        /* ================= ROOT ================= */
        $nfe = $dom->createElementNS($ns, 'NFe');
        $dom->appendChild($nfe);

        $infNFe = $dom->createElement('infNFe');
        $infNFe->setAttribute('Id', 'NFe' . $chave);
        $infNFe->setAttribute('versao', '4.00');
        $nfe->appendChild($infNFe);

        /* ================= IDE ================= */
        $ide = $dom->createElement('ide');
        $ide->appendChild($dom->createElement('cUF', $d['ide_cUF']));
        $ide->appendChild($dom->createElement('cNF', $d['ide_cNF']));
        $ide->appendChild($dom->createElement('natOp', $d['ide_natOp']));
        $ide->appendChild($dom->createElement('mod', $d['ide_mod']));
        $ide->appendChild($dom->createElement('serie', $d['ide_serie']));
        $ide->appendChild($dom->createElement('nNF', $d['ide_nNF']));
        $ide->appendChild($dom->createElement('dhEmi', $d['ide_dhEmi']));
        $ide->appendChild($dom->createElement('dhSaiEnt', $d['ide_dhSaiEnt']));
        $ide->appendChild($dom->createElement('tpNF', $d['ide_tpNF']));
        $ide->appendChild($dom->createElement('idDest', $d['ide_idDest']));
        $ide->appendChild($dom->createElement('cMunFG', $d['ide_cMunFG']));
        $ide->appendChild($dom->createElement('tpImp', $d['ide_tpImp']));
        $ide->appendChild($dom->createElement('tpEmis', $d['ide_tpEmis']));
        $ide->appendChild($dom->createElement('cDV', $d['ide_cDV']));
        $ide->appendChild($dom->createElement('tpAmb', $d['ide_tpAmb']));
        $ide->appendChild($dom->createElement('finNFe', $d['id_finNFe']));
        $ide->appendChild($dom->createElement('indFinal', $d['ide_indFinal']));
        $ide->appendChild($dom->createElement('indPres', $d['ide_indPres']));
        $ide->appendChild($dom->createElement('procEmi', $d['ide_procEmi']));
        $ide->appendChild($dom->createElement('verProc', $d['ide_verProc']));
        $infNFe->appendChild($ide);

        /* ================= EMITENTE ================= */
        $emit = $dom->createElement('emit');
        if (!empty($d['emit_CNPJ'])) $emit->appendChild($dom->createElement('CNPJ', $d['emit_CNPJ']));
        if (!empty($d['emit_CPF']))  $emit->appendChild($dom->createElement('CPF', $d['emit_CPF']));
        $emit->appendChild($dom->createElement('xNome', $d['emit_xNome']));
        $emit->appendChild($dom->createElement('xFant', ($d['emit_xFant']) ?? $d['emit_xNome']));
        $emit->appendChild($dom->createElement('IE', $d['emit_IE']));
        $emit->appendChild($dom->createElement('IM', $d['emit_IM']));
        $emit->appendChild($dom->createElement('CNAE', $d['emit_CNAE']));
        $emit->appendChild($dom->createElement('CRT', $d['emit_CRT']));

        $enderEmit = $dom->createElement('enderEmit');
        $enderEmit->appendChild($dom->createElement('xLgr', $d['emit_xLgr']));
        $enderEmit->appendChild($dom->createElement('nro', $d['emit_nro']));
        $enderEmit->appendChild($dom->createElement('xCpl', $d['emit_xCpl']));
        $enderEmit->appendChild($dom->createElement('xBairro', $d['emit_xBairro']));
        $enderEmit->appendChild($dom->createElement('cMun', $d['emit_cMun']));
        $enderEmit->appendChild($dom->createElement('xMun', $d['emit_xMun']));
        $enderEmit->appendChild($dom->createElement('UF', $d['emit_UF']));
        $enderEmit->appendChild($dom->createElement('CEP', $d['emit_CEP']));
        $enderEmit->appendChild($dom->createElement('cPais', $d['emit_cPais']));
        $enderEmit->appendChild($dom->createElement('xPais', $d['emit_xPais']));
        $enderEmit->appendChild($dom->createElement('fone', $d['emit_fone']));
        $emit->appendChild($enderEmit);

        $infNFe->appendChild($emit);

        /* ================= DESTINATÁRIO ================= */
        $dest = $dom->createElement('dest');
        if (!empty($d['dest_CNPJ'])) $dest->appendChild($dom->createElement('CNPJ', $d['dest_CNPJ']));
        if (!empty($d['dest_CPF']))  $dest->appendChild($dom->createElement('CPF', $d['dest_CPF']));
        $dest->appendChild($dom->createElement('xNome', $d['dest_xNome']));
        $dest->appendChild($dom->createElement('indIEDest', $d['dest_indIEDest']));
        $dest->appendChild($dom->createElement('IE', $d['dest_IE']));
        $dest->appendChild($dom->createElement('email', $d['dest_email']));

        $enderDest = $dom->createElement('enderDest');
        $enderDest->appendChild($dom->createElement('xLgr', $d['dest_xLgr']));
        $enderDest->appendChild($dom->createElement('nro', $d['dest_nro']));
        $enderDest->appendChild($dom->createElement('xCpl', $d['dest_xCpl']));
        $enderDest->appendChild($dom->createElement('xBairro', $d['dest_xBairro']));
        $enderDest->appendChild($dom->createElement('cMun', $d['dest_cMun']));
        $enderDest->appendChild($dom->createElement('xMun', $d['dest_xMun']));
        $enderDest->appendChild($dom->createElement('UF', $d['dest_UF']));
        $enderDest->appendChild($dom->createElement('CEP', $d['dest_CEP']));
        $enderDest->appendChild($dom->createElement('cPais', $d['dest_cPais']));
        $enderDest->appendChild($dom->createElement('xPais', $d['dest_xPais']));
        $enderDest->appendChild($dom->createElement('fone', $d['dest_fone']));
        $dest->appendChild($enderDest);

        $infNFe->appendChild($dest);

        /* ================= PRODUTOS ================= */
        foreach ($d['produtos'] as $i => $p) {

            $det = $dom->createElement('det');
            $det->setAttribute('nItem', $i + 1);

            $prod = $dom->createElement('prod');
            $prod->appendChild($dom->createElement('cProd', $p['prod_cProd']));
            $prod->appendChild($dom->createElement('cEAN', $p['prod_cEAN']));
            $prod->appendChild($dom->createElement('xProd', $p['prod_xProd']));
            $prod->appendChild($dom->createElement('NCM', $p['prod_NCM']));
            $prod->appendChild($dom->createElement('CFOP', $p['prod_CFOP']));
            $prod->appendChild($dom->createElement('uCom', $p['prod_uCom']));
            $prod->appendChild($dom->createElement('qCom', $p['prod_qCom']));
            $prod->appendChild($dom->createElement('vUnCom', $p['prod_vUnCom']));
            $prod->appendChild($dom->createElement('vProd', $p['prod_vProd']));
            $prod->appendChild($dom->createElement('cEANTrib', $p['prod_cEANTrib']));
            $prod->appendChild($dom->createElement('uTrib', $p['prod_uTrib']));
            $prod->appendChild($dom->createElement('qTrib', $p['prod_qTrib']));
            $prod->appendChild($dom->createElement('vUnTrib', $p['prod_vUnTrib']));
            $prod->appendChild($dom->createElement('indTot', $p['prod_indTot']));
            $det->appendChild($prod);

            $det->appendChild($dom->createElement('imposto'));
            $infNFe->appendChild($det);
        }

        /* ================= TOTAIS ================= */
        $total = $dom->createElement('total');
        $icmstot = $dom->createElement('ICMSTot');

        foreach (
            [
                'vBC',
                'vICMS',
                'vICMSDeson',
                'vFCPUFDest',
                'vICMSUFDest',
                'vICMSUFRemet',
                'vFCP',
                'vBCST',
                'vST',
                'vFCPST',
                'vFCPSTRet',
                'vProd',
                'vFrete',
                'vSeg',
                'vDesc',
                'vII',
                'vIPI',
                'vIPIDevol',
                'vPIS',
                'vCOFINS',
                'vOutro',
                'vNF',
                'vTotTrib'
            ] as $campo
        ) {
            $icmstot->appendChild($dom->createElement($campo, $d[$campo] ?? '0.00'));
        }

        /* ===== IBS / CBS (FUTURO) ===== */
        if (!empty($d['vBCIBSCBS'])) {
            $icmstot->appendChild($dom->createElement('vBCIBSCBS', $d['vBCIBSCBS']));
            // gIBS, gIBSUF, gIBSMun, gCBS entram aqui quando o XSD permitir
        }

        $total->appendChild($icmstot);
        $infNFe->appendChild($total);

        /* ================= TRANSPORTE ================= */
        $transp = $dom->createElement('transp');
        $transp->appendChild($dom->createElement('modFrete', $d['modFrete']));

        $transporta = $dom->createElement('transporta');
        $transporta->appendChild($dom->createElement('CNPJ', $d['transporta_CNPJ']));
        $transporta->appendChild($dom->createElement('xNome', $d['transporta_xNome']));
        $transporta->appendChild($dom->createElement('IE', $d['transporta_IE']));
        $transporta->appendChild($dom->createElement('xEnder', $d['transporta_xEnder']));
        $transporta->appendChild($dom->createElement('xMun', $d['transporta_xMun']));
        $transporta->appendChild($dom->createElement('UF', $d['transporta_UF']));
        $transp->appendChild($transporta);

        $vol = $dom->createElement('vol');
        $vol->appendChild($dom->createElement('qVol', $d['vol_qVol']));
        $vol->appendChild($dom->createElement('esp', $d['vol_esp']));
        $vol->appendChild($dom->createElement('marca', $d['vol_marca']));
        $vol->appendChild($dom->createElement('nVol', $d['vol_nVol']));
        $vol->appendChild($dom->createElement('pesoL', $d['vol_pesoL']));
        $vol->appendChild($dom->createElement('pesoB', $d['vol_pesoB']));
        $transp->appendChild($vol);

        $infNFe->appendChild($transp);

        return $dom->saveXML();
    }

    function assinarXmlNFe(string $xml, string $certPath, string $certSenha): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);

        $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
        $id = $infNFe->getAttribute('Id');

        // Lê certificado
        $pfx = file_get_contents($certPath);
        openssl_pkcs12_read($pfx, $certs, $certSenha);

        $privateKey = openssl_pkey_get_private($certs['pkey']);
        $publicCert = $certs['cert'];

        // Digest
        $infNFeC14N = $infNFe->C14N(true, false);
        $digest = base64_encode(hash('sha1', $infNFeC14N, true));

        // Signature
        $sig = $dom->createElement('Signature');
        $sig->setAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');

        $signedInfo = $dom->createElement('SignedInfo');

        $canon = $dom->createElement('CanonicalizationMethod');
        $canon->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canon);

        $sigMethod = $dom->createElement('SignatureMethod');
        $sigMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $signedInfo->appendChild($sigMethod);

        $ref = $dom->createElement('Reference');
        $ref->setAttribute('URI', '#' . $id);

        $transforms = $dom->createElement('Transforms');

        $t1 = $dom->createElement('Transform');
        $t1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($t1);

        $t2 = $dom->createElement('Transform');
        $t2->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $transforms->appendChild($t2);

        $ref->appendChild($transforms);

        $digestMethod = $dom->createElement('DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $ref->appendChild($digestMethod);

        $ref->appendChild($dom->createElement('DigestValue', $digest));
        $signedInfo->appendChild($ref);

        $sig->appendChild($signedInfo);

        openssl_sign(
            $signedInfo->C14N(true, false),
            $signatureValue,
            $privateKey,
            OPENSSL_ALGO_SHA1
        );

        $sig->appendChild(
            $dom->createElement('SignatureValue', base64_encode($signatureValue))
        );

        $keyInfo = $dom->createElement('KeyInfo');
        $x509Data = $dom->createElement('X509Data');
        $x509Data->appendChild(
            $dom->createElement('X509Certificate', str_replace(
                ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\n"],
                '',
                $publicCert
            ))
        );

        $keyInfo->appendChild($x509Data);
        $sig->appendChild($keyInfo);

        $infNFe->parentNode->appendChild($sig);

        return $dom->saveXML();
    }

    public function emitirNfe()
    {

        // 1️⃣ Dados vindos do formulário
        $post = $_POST; // ou $this->request->all()
        // var_dump($post);
        // exit;
        // 2️⃣ Datas
        $dte = date('Y-m-d');
        $dts = date('Y-m-d');

        // 3️⃣ Gera a chave da NF-e (usa dados do emitente)
        $chave = $this->gerarChaveNfe($post);

        // 4️⃣ Monta o array de dados no formato da NF-e
        $dados = $this->montarDadosDaNfe($post, $chave, $dte, $dts);

        // 5️⃣ Gera o XML
        $xml = $this->montarXmlNFe($post, $chave, $dte, $dts);

        // 6️⃣ Assina o XML
        $xmlAssinado = $this->assinarXmlNFe(
            $xml,
            __DIR__ . '/../certs/certificado.pfx',
            'SENHA_DO_CERT'
        );

        // 7️⃣ (teste) salvar arquivo
        file_put_contents(
            __DIR__ . '/../xml/nfe-assinada.xml',
            $xmlAssinado
        );

        // 👉 daqui pra frente: enviar pra SEFAZ
    }




    public function clienteBusca()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["results" => []]);
            return;
        }

        $termo = trim($_POST['termo'] ?? '');

        $params = "tipo=1";
        $where  = "tipo = :tipo";

        if ($termo !== '') {

            $termoCpf = $this->termoParaCpfCnpjLike($termo);

            $where .= " AND (
            nome LIKE :termo
            OR cpfcnpj LIKE :termoCpf
        )";

            $params .= "&termo="  . urlencode("%{$termo}%");
            $params .= "&termoCpf={$termoCpf}";
        }

        $ent = (new Ent())
            ->find($where, $params)
            ->fetch(true);

        $cidade = (new Municipios())->findById($ent[0]->municipio_id);
         
        $results = [];

        if ($ent) {
            foreach ($ent as $item) {
                $results[] = [
                    "id"       => $item->id,
                    "ie"       => $item->inscrg,
                    "text"     => $item->nome,
                    "cnpj"     => $item->cpfcnpj,
                    "endereco" => $item->endereco,
                    "numero"   => $item->numero,
                    "bairro"   => $item->bairro,
                    "cidade"   => $cidade->nome,
                    "estado"   => $item->uf,
                    "cep"      => $item->cep,
                    "fone"     => $item->fone1,
                    "email"    => $item->email
                ];
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["results" => $results]);
        exit;
    }


    private function termoParaCpfCnpjLike(string $termo): string
    {
        $nums = preg_replace('/\D/', '', $termo);

        if ($nums === '') {
            return '';
        }

        // transforma 396 -> 3%9%6
        return '%' . implode('%', str_split($nums)) . '%';
    }
}
