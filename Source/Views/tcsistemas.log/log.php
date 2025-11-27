<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log!</title>
    <link rel="stylesheet" href="<?= url('Source/Views/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= url('Source/Views/tcsistemas.log/assets/css/log.css'); ?>">
    <link rel="stylesheet" href="<?= url('Source/Views/css/fontawesome/css/all.css'); ?>">
    <script src="<?= url("Source/Views/tcsistemas.log/assets/js/jquery-3.7.1.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/tcsistemas.log/assets/js/script.js"); ?>"></script>
</head>

<body>
    <div class="content">
        <div class="container-form">
            <form id="formLog" method="post" data-url="<?= url("logs/pesqLogs"); ?>">
                <div class="form-group">
                    <div class="custom-select" id="empresaSelect">
                        <input type="text" class="url" value="<?= url("logs/acao"); ?>" hidden>
                        <div>
                            <label>Empresa:</label>
                        </div>
                        <div>
                            <button type="button" style="width: 300px;">Selecione uma opção</button>
                        </div>
                        <div class="custom-options">
                            <?php foreach ($empresas as $empresa): ?>
                                <label>
                                    <input type="checkbox" value="<?= $empresa->id; ?>" data-url="<?= url("logs/"); ?>">
                                    <span><?= mb_strimwidth($empresa->razao, 0, 25, '...'); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-select" id="acao">
                        <div><label>Ação</label></div>
                        <div><button type="button" style="width: 200px;">Selecione uma opção</button></div>
                        <div class="custom-options">
                            <label><input type="checkbox" value="create"><span>Criação</span></label>
                            <label><input type="checkbox" value="update"><span>Alteração</span></label>
                            <label><input type="checkbox" value="delete"><span>Exclusão</span></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-select" id="usuario">
                        <div><label>Usuário</label></div>
                        <div><button type="button" style="width: 200px;">Selecione uma opção</button></div>
                        <div class="custom-options" id="opt-usuario"></div>
                    </div>
                </div>

                <div class="form-group" style="width: 20%;">
                    <div class="custom-select" id="campo">
                        <div><label>Tabela</label></div>
                        <div><button type="button" style="width: 250px;">Selecione uma opção</button></div>
                        <div class="custom-options">
                            <?php foreach ($tabelas as $tabela): ?>
                                <label><input type="checkbox" value="<?= htmlspecialchars($tabela); ?>">
                                    <span><?= htmlspecialchars($tabela); ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="periodo">
                        <div><input type="date" class="data" style="margin-right: 5px;" id="inicial"></div>
                        <label>Até</label>
                        <div><input type="date" class="data" style="margin-left: 5px;" id="final"></div>
                    </div>
                </div>

                <div class="form-group" style="width: 150px;">
                    <button type="submit" class="btn-filtro"><i class="fa-solid fa-filter"></i></button>
                </div>
            </form>
        </div>

        <div class="informacao">
            <div class="table-container" style="max-height: 500px; overflow-y: auto;">
                <table class="tabela-fixa" id="minhaTabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DATA</th>
                            <th>HORA</th>
                            <th>EMPRESA</th>
                            <th>AÇÃO</th>
                            <th>TABELA</th>
                            <th>USUÁRIO</th>
                        </tr>
                    </thead>
                    <tbody id="resultado"></tbody>
                </table>
                <div id="loading" style="display:none; text-align:center; padding:20px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p style="margin-top:10px; color:#007bff;">Carregando registros...</p>
                </div>
            </div>
        </div>

        <div class="vlrAcao" hidden>
            <div class="table-container">
                <table id="tabelaAntes" class="table table-bordered">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="table-container">
                <table id="tabelaDepois" class="table table-bordered">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".custom-select").forEach(customSelect => {
                const button = customSelect.querySelector("button");

                button.addEventListener("click", () => {
                    customSelect.classList.toggle("open");
                });

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
        });
    </script>
</body>

</html>