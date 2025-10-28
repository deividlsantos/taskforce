<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
    <title>Folha de Ponto</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            padding-bottom: 2%;
            position: relative;
            /* Adicionado para que elementos posicionados absolutamente dentro do body sejam relativos a ele */
            min-height: 100vh;
            /* Garante que o body ocupe pelo menos 100% da altura da viewport */
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 25px;
            margin-bottom: 20px;
            position: relative;
        }

        .header .company-info {
            font-size: 12px;
            text-align: left;
        }

        .header .company-info div {
            margin-top: 5px;
        }

        .header img {
            width: 100px;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 0;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .info {
            display: grid;
            grid-template-columns: 1fr 5fr 1fr 5fr;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 15px;
            ;
        }

        .info .left,
        .info .center,
        .info .right,
        .info .far-right {
            padding: 0 10px;
        }

        .info div {
            margin-bottom: 5px;
        }

        .info div span {
            font-weight: bold;
        }


        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th,
        .attendance-table td {
            border-bottom: 1px solid #000;
            padding: 2px;
            text-align: center;
            font-size: 15px;
            height: 3px;
        }

        .attendance-table th {
            background-color: #f2f2f2;
        }

        .attendance-table tr {
            height: 2px;
        }

        .empresa {
            font-weight: bold;
            font-size: 18px;
        }

        .footer {
            font-size: 13px;
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
        }

        .footer-titles {
            grid-column: span 4;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin-bottom: 2px;
            padding-bottom: 0px;
            background-color: #f2f2f2;
        }

        .footer-titles .title-left {
            grid-column: 1 / 2;
            font-weight: bold;
            text-align: left;
        }

        .footer-titles .title-center {
            grid-column: 2 / 3;
        }

        .footer-titles .title-right {
            grid-column: 3 / 4;
            font-weight: bold;
            text-align: left;
        }

        .footer-titles .title-far-right {
            grid-column: 4 / 5;
        }

        .footer .left {
            grid-column: 1 / 2;
        }

        .footer .center {
            grid-column: 2 / 3;
        }

        .footer .right {
            grid-column: 3 / 4;
        }

        .footer .far-right {
            grid-column: 4 / 5;
        }

        .footer div div {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .underline {
            position: relative;
        }

        .underline::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            /* Ajuste a posição vertical do sublinhado */
            width: 50%;
            /* Define o comprimento do sublinhado */
            height: 2px;
            /* Define a espessura do sublinhado */
            background-color: black;
            /* Define a cor do sublinhado */
        }

        .termos {
            font-size: 10px;
            font-weight: bold;
            border-bottom: 2px solid #000;
            position: absolute;
            margin-top: 5%;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            /* Centraliza o texto, opcional */
            padding: 5px 0;
            /* Ajuste o padding conforme necessário */
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 120px;
            font-weight: bold;
        }

        .signature {
            flex: 1;
            text-align: center;
        }

        .signature {
            margin-left: 50px;
            padding-right: 50px;
            margin-bottom: 10%;
        }

        .signature .line {
            display: inline-block;
            width: 400px;
            border-bottom: 1px solid black;
            margin-bottom: 10px;
        }

        .signature .label {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-info">
            <div class="empresa"><?= $emp->razao; ?></div>
            <div><?= $emp->endereco; ?> - <?= $emp->cidade; ?> – <?= $emp->uf; ?> | CEP: <?= $emp->cep; ?></div>
            <div>Fone: <?= $emp->fone1; ?></div>
            <div>Email: <?= $emp->email; ?></div>
            <div>CPF/CNPJ: <?= $emp->cnpj; ?></div>
            <div class="title">Folha de Ponto - <?= $func->nome . " " . $func->fantasia; ?></div>
        </div>
        <?= $logo; ?>
    </div>
    <div class="info">
        <div class="left">
            <div><span>Nome:</span></div>
            <div><span>Competência:</span></div>
        </div>
        <div class="center">
            <div><?= $func->nome . " " . $func->fantasia; ?></div>
            <div><?= retornaNomeMes($ponto1->mes) . "/" . $ponto1->ano; ?></div>
        </div>
        <div class="right">
            <div><span>Cargo/Função:</span></div>
            <div><span>Matrícula:</span></div>
            <div><span>CTPS:</span></div>
        </div>
        <div class="far-right">
            <div><?= $entfun->cargo; ?></div>
            <div><?= $entfun->matricula; ?></div>
            <div><?= $entfun->ctps; ?></div>
        </div>
    </div>
    <table class="attendance-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Início</th>
                <th>Início do intervalo</th>
                <th>Fim do intervalo</th>
                <th>Fim</th>
                <?php
                if ($extras != '' && $extras > '00:00'):
                    ?>
                    <th>Horas extras</th>
                    <?php
                endif;
                ?>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ponto2 as $hrDia): ?>
                <tr>
                    <td><?= str_pad($hrDia->dia, 2, "0", STR_PAD_LEFT) . "/" . str_pad($ponto1->mes, 2, "0", STR_PAD_LEFT) . "/" . $ponto1->ano; ?>
                    </td>
                    <td><?= validaHoraPonto(substr($hrDia->hora_ini, 0, 5)) ? substr($hrDia->hora_ini, 0, 5) : $hrDia->hora_ini; ?>
                    </td>
                    <td><?= validaHoraPonto(substr($hrDia->intervalo_ini, 0, 5)) ? substr($hrDia->intervalo_ini, 0, 5) : $hrDia->intervalo_ini; ?>
                    </td>
                    <td><?= validaHoraPonto(substr($hrDia->intervalo_fim, 0, 5)) ? substr($hrDia->intervalo_fim, 0, 5) : $hrDia->intervalo_fim; ?>
                    </td>
                    <td><?= validaHoraPonto(substr($hrDia->hora_fim, 0, 5)) ? substr($hrDia->hora_fim, 0, 5) : $hrDia->hora_fim; ?>
                    </td>
                    <?php
                    if ($extras != '' && $extras > '00:00'):
                        ?>
                        <td><?= $hrDia->extra_horas ?></td>
                        <?php
                    endif;
                    ?>
                    <td><?php
                    if (!empty($faltas)):
                        foreach ($faltas as $obs):
                            if ($obs->id == $hrDia->obs):
                                echo $obs->descricao;
                            endif;
                        endforeach;
                    endif;
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="footer">
        <div class="footer-titles">
            <div class="title-left">Vencimentos</div>
            <div class="title-center"></div>
            <div class="title-right">Descontos</div>
            <div class="title-far-right"></div>
        </div>
        <div class="left">
            <div class="underline">Domingo/Feriado</div>
            <div class="underline">Hora Extra (50%)</div>
            <div class="underline">Hora Extra (100%)</div>
            <div class="underline">M.O Especializada</div>
        </div>
        <div class="center">
            <div class="underline">Hour In Itinere</div>
            <div class="underline">Bonus Noturno</div>
            <div class="underline">Bonus Insalubridade</div>
        </div>
        <div class="right">
            <div class="underline">Adiantamentos</div>
            <div class="underline">Faltas</div>
            <div class="underline">Atrasos</div>
        </div>
        <div class="far-right">

        </div>
    </div>
    <div class="signatures">
        <div class="signature">
            <div class="line"></div>
            <div class="label">Assinatura do Funcionário</div>
        </div>
        <div class="signature">
            <div class="line"></div>
            <div class="label">Assinatura da Empresa</div>
        </div>
    </div>


    <div class="termos">Nos termos da Portaria MTB nr. 3.162, de 08/09/1986 e 3.081, de 11/04/1984, o presente Cartão de
        Ponto substitui o Quadro de Horário de Trabalho, inclusive o de menores</div>



</body>

</html>