<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log!</title>
    <link rel="stylesheet" href="<?= url('Source/Views/css/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?= url('Source/Views/tcsistemas.log/assets/css/log.css'); ?>">
    <link rel="stylesheet" href="<?= url('Source/Views/css/fontawesome/css/all.css'); ?>">
    <script src="<?= url("Source/Views/tcsistemas.log/assets/js/jquery-3.7.1.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/tcsistemas.log/assets/js/script.js"); ?>"></script>
    <script src="<?= url("Source/Views/tcsistemas.log/assets/js/js.js"); ?>"></script>
</head>

<body>
    <a id='expira' href="<?= url("logs/exp"); ?>">Teste</a>
    <!--<h1 class="titleLog">LOGs</h1>-->
    <div class="content">
        <div class="container-form">
            <form id="formLog" method="post" data-url="<?= url("logs/pesqLogs"); ?>">
                <div class="form-group">
                    <div class="custom-select" id="empresaSelect">
                        <div>
                            <label>Empresa:</label>
                        </div>
                        <div>
                            <button type="button" style="width: 300px;">Selecione uma opção</button>
                        </div>
                        <div class="custom-options">
                            <?php
                            foreach ($empresas as $empresa):
                            ?>
                                <label><input type="checkbox" value="<?= $empresa->id; ?>" data-url="<?= url("logs/"); ?>"><span><?= mb_strimwidth($empresa->razao, 0, 25, '...'); ?></span></label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-select" id="acao">
                        <div>
                            <label>Ação</label>
                        </div>
                        <div>
                            <button type="button" style="width: 200px;">Selecione uma opção</button>
                        </div>
                        <div class="custom-options">
                            <label><input type="checkbox" value="create"><span>Criação</span></label>
                            <label><input type="checkbox" value="update"><span>Alteração</span></label>
                            <label><input type="checkbox" value="delete"><span>Exclusão</span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-select" id="usuario">
                        <div>
                            <label>Usuario</label>
                        </div>
                        <div>
                            <button type="button" style="width: 200px;">Selecione uma opção</button>
                        </div>
                        <div class="custom-options" id="opt-usuario">
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width: 20%;">
                    <div class="custom-select" id="campo">
                        <div>
                            <label>Tabela</label>
                        </div>
                        <div>
                            <button type="button" style="width: 250px;">Selecione uma opção</button>
                        </div>
                        <div class="custom-options">
                            <?php
                            foreach ($tabelas as $tabela):
                            ?>
                                <label><input type="checkbox" value="<?= htmlspecialchars($tabela); ?>" id=""><span><?= htmlspecialchars($tabela); ?></span></label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="periodo">
                        <div>
                            <input type="date" class="data" style="margin-right: 5px;" id="inicial">
                        </div>
                        <label>Até</label>
                        <div>
                            <input type="date" class="data" style="margin-left: 5px;" id="final">
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width: 150px;">
                    <button type="submit" class="btn-filtro"><i class="fa-solid fa-filter"></i></button>
                </div>
            </form>
        </div>
        <div class="informacao">
            <div class="table-container">
                <table class="tabela-fixa" id="minhaTabela">
                    <thead>
                        <tr>
                            <th>ID <button onclick="ordenarTabela(0)" class="ordenar"><i class="fa fa-sort"></i></button></th>
                            <th>DATA <button onclick="ordenarTabela(1)" class="ordenar"><i class="fa fa-sort"></i></button></th>
                            <th>HORA</th>
                            <th>EMPRESA</th>
                            <th>AÇÃO</th>
                            <th>TABELA</th>
                            <th>USUARIO</th>
                        </tr>
                    </thead>
                    <tbody id="resultado">
                    </tbody>
                </table>
                <div id="loading" style="display:none; text-align:center; padding:20px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p style="margin-top:10px; color:#007bff;">Carregando registros...</p>
                </div>
            </div>
        </div>
    </div>
    <div class="vlrAcao" hidden>
        <table id="tabelaAntes" style="display:block;">
            <thead></thead>
            <tbody></tbody>
        </table>
        <table id="tabelaDepois" style="display:block;">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
    </div>
    <script>
        document.querySelectorAll(".custom-select").forEach(customSelect => {
            const button = customSelect.querySelector("button");

            // Abre/fecha o dropdown
            button.addEventListener("click", () => {
                customSelect.classList.toggle("open");
            });

            // Delegação: escuta mudanças dentro do .custom-select
            customSelect.addEventListener("change", e => {
                if (e.target.matches("input[type=checkbox]")) {
                    let values = [];
                    customSelect.querySelectorAll("input[type=checkbox]:checked").forEach(c => {
                        let labelText = c.parentNode.querySelector("span").textContent.trim();
                        values.push(labelText);
                    });
                    button.textContent = values.length ?
                        values.join(", ") :
                        button.getAttribute("data-placeholder") || "Selecione uma opção";
                }
            });
        });

        document.addEventListener("click", e => {
            document.querySelectorAll(".custom-select.open").forEach(openSelect => {
                if (!openSelect.contains(e.target)) {
                    openSelect.classList.remove("open");
                }
            });
        });

        
    </script>
</body>

</html>