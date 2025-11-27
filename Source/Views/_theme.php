<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
    <title><?= $this->e($titulo) ?></title>
    <link href="<?= url("Source/Views/css/bootstrap.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontgoogleapis/fontsgoogleapis.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontawesome/css/all.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontawesome/css/awesomeregular.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/select2.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/boot.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/style.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/app.css?v=") . filemtime("Source/Views/css/app.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/form.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/awesomplete.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/telas.css"); ?>" rel="stylesheet">
    <link href="<?= url('Source/Views/css/mobile.css?v=') . filemtime('Source/Views/css/mobile.css'); ?>"
        rel="stylesheet">
    <link href="<?= url("Source/Views/css/vis.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/tooltipster.bundle.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fullcalendar.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/agenda.css?v=") . filemtime('Source/Views/css/agenda.css'); ?>"
        rel="stylesheet">
    <script src="<?= url("Source/Views/js/moment.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/app.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/tablesorter/js/jquery.tablesorter.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/chart.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.form.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery-ui.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.mask.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/tinymce/jquery.tinymce.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/tinymce/tinymce.min.js"); ?>"></script>
    <script
        src="<?= url("Source/Views/js/login/scripts.js?v=") . filemtime('Source/Views/js/login/scripts.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/select2.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/awesomplete.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/autonumeric.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/fullcalendar.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/tooltipster.bundle.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/gcal.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/locale-all.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/vis.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/daypilot-all.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/folhas.js?v=") . filemtime("Source/Views/js/folhas.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/timeline.js?v=") . filemtime("Source/Views/js/timeline.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/rascunhos.js?v=") . filemtime("Source/Views/js/rascunhos.js"); ?>"></script>
    <style>
        .content {
            position: relative;
            background-size: cover;
            /* ou cover */
            background-repeat: no-repeat;
            background-position: center;
            background-color: rgba(255, 255, 255, 0.5);
            background-blend-mode: lighten;
            overflow: visible;
            width: 100%;
            z-index: auto;
        }
    </style>
</head>

<body>
    <div class="ajax_load" style="z-index: 999;">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <p class="ajax_load_box_title">Aguarde, carregando...</p>
        </div>
    </div>

    <div class="ajax_response"><?= flash(); ?></div>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="<?= $user->url; ?>">
                    <span class="align-middle"><img class="dash-img"
                            src="<?= url("Source/Images/tflogobranco.png"); ?>"></span>
                </a>
                <ul class="sidebar-nav">
                    <?php

                    use Source\Models\Emp1;
                    use Source\Models\Emp2;
                    use Source\Models\Users;

                    if ($user->os == "X" || $user->id_emp2 == 1):
                    ?>
                        <li class="sidebar-item-pai">
                            <a class="sidebar-link" href="<?= url("ordens") ?>">
                                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle"> Ordens
                                    Serv.</span> <span style="float:right;"><i data-feather="chevron-down"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?= url('ordens') ?>"><i class="submenu-icon" data-feather="monitor"></i>
                                        Ordens</a></li>
                                <li><a href="<?= url('obras') ?>"><i class="submenu-icon"><span
                                                class="fa <?= (new Emp2())->findById($user->id_emp2)->iconeLabel; ?>"></span></i>
                                        <?= (new Emp2())->findById($user->id_emp2)->labelFiliais; ?></a></li>
                                <?php
                                if (ll_decode($_SESSION['servicosComEquipamentos']) == "X"):
                                ?>
                                    <li><a href="<?= url('equipamentos') ?>"><i class="submenu-icon" data-feather="truck"></i>
                                            Equipamentos</a></li>
                                <?php
                                endif;
                                //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
                                if ($user->tipo != 3):
                                ?>
                                    <!-- <li><a href="<?= url('agenda') ?>"><i class="submenu-icon" data-feather="calendar"></i> Agenda</a></li> -->
                                    <?php
                                    if (ll_decode($_SESSION['servicosComEquipamentos']) == "X"):
                                    ?>
                                        <li><a href="<?= url('checklist') ?>"><i class="submenu-icon" data-feather="list"></i>
                                                Checklist</a></li>
                                    <?php
                                    endif;
                                    ?>
                                    <li hidden><a href="<?= url('timeline') ?>"><i class="submenu-icon"
                                                data-feather="clock"></i> Linha do Tempo</a></li>
                                    <li><a href="<?= url('materiais') ?>"><i class="submenu-icon" data-feather="archive"></i>
                                            Produtos/Materiais</a></li>
                                    <li><a href="<?= url('obs') ?>"><i class="submenu-icon" data-feather="edit"></i>
                                            Obs.Ordens</a></li>
                                    <li><a href="<?= url('servico') ?>"><i class="submenu-icon" data-feather="tool"></i>
                                            Serviços</a></li>
                                    <li><a href="<?= url('setor') ?>"><i class="submenu-icon" data-feather="grid"></i>
                                            Setores</a></li>
                                    <li><a href="<?= url('tipo') ?>"><i class="submenu-icon" data-feather="tag"></i>
                                            Tipos de OS</a></li>
                                    <li><a href="<?= url('relatorios') ?>"><i class="submenu-icon"
                                                data-feather="trending-up"></i> Relatórios</a></li>
                                    <!-- <li hidden><a href="<?= url('status') ?>"><i class="submenu-icon" data-feather="check-circle"></i> Status (*)</a></li> -->
                                <?php
                                endif;
                                ?>
                            </ul>
                        </li>
                    <?php
                    endif;
                    if ($user->financeiro == "X" || $user->id_emp2 == 1):
                    ?>
                        <li class="sidebar-item-pai">
                            <a class="sidebar-link" href="#">
                                <i class="align-middle" data-feather="dollar-sign"></i> <span
                                    class="align-middle">Financeiro</span><span style="float:right;"><i
                                        data-feather="chevron-down"></i></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?= url('dash-financeiro') ?>"><i class="submenu-icon" data-feather="monitor"></i>
                                        Dashboard</a></li>
                                <li><a href="<?= url('contas') ?>"><i class="submenu-icon"
                                            data-feather="arrow-up-circle"></i> Receitas/Despesas</a></li>
                                <li><a href="<?= url('ent/portador') ?>"><i class="submenu-icon"
                                            data-feather="credit-card"></i> Contas Bancárias</a></li>
                                <li><a href="<?= url('custogeral') ?>"><i class="submenu-icon"
                                            data-feather="bar-chart-2"></i> Custos Gerais</a></li>
                                <li><a href="<?= url('operacao') ?>"><i class="submenu-icon" data-feather="dollar-sign"></i>
                                        Operações</a></li>
                                <li><a href="<?= url('plconta') ?>"><i class="submenu-icon" data-feather="layers"></i> Plano
                                        de Contas</a></li>
                                <li><a href="<?= url('financeirorel') ?>"><i class="submenu-icon" data-feather="trending-up"></i>Relatórios</a></li>
                                <li hidden><a href="<?= url('baixar') ?>"><i class="submenu-icon" data-feather="users"></i>
                                        Baixas</a></li>
                            </ul>
                        </li>
                    <?php
                    endif;
                    if ($user->cadastros == "X" || $user->id_emp2 == 1):
                    ?>
                        <li class="sidebar-item-pai">
                            <a class="sidebar-link" href="<?= url("ent") ?>">
                                <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Cad.
                                    Gerais</span> <span style="float:right;"><i data-feather="chevron-down"></i></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?= url('ent/cliente') ?>"><i class="submenu-icon" data-feather="users"></i>
                                        Clientes</a></li>
                                <li><a href="<?= url('ent/colaborador') ?>"><i class="submenu-icon"
                                            data-feather="briefcase"></i> Colaboradores</a></li>
                                <li><a href="<?= url('ent/fornecedor') ?>"><i class="submenu-icon"
                                            data-feather="package"></i> Fornecedores</a></li>
                                <li><a href="<?= url('turno') ?>"><i class="submenu-icon" data-feather="calendar"></i>
                                        Turnos</a></li>
                                <?php
                                if ($user->tipo == 5):
                                ?>
                                    <li><a href="<?= url('users') ?>"><i class="submenu-icon" data-feather="user"></i>
                                            Usuários</a></li>
                                <?php
                                endif;
                                ?>
                            </ul>
                        </li>
                    <?php
                    endif;
                    if ($user->ponto == "X" || $user->id_emp2 == 1):
                    ?>
                        <li class="sidebar-item-pai">
                            <a class="sidebar-link" href="#">
                                <i class="align-middle" data-feather="clock"></i> <span
                                    class="align-middle">Ponto</span><span style="float:right;"><i
                                        data-feather="chevron-down"></i></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?= url('ponto/fechamento') ?>"><i class="submenu-icon"
                                            data-feather="clock"></i> Gerar</a></li>
                                <li><a href="<?= url('ponto/folhas') ?>"><i class="submenu-icon" data-feather="search"></i>
                                        Conciliar</a></li>
                                <li><a href="<?= url('faltas') ?>"><i class="submenu-icon" data-feather="edit"></i>
                                        Obs.Ponto</a></li>
                                <li><a href="<?= url('ponto/feriados') ?>"><i class="submenu-icon"
                                            data-feather="calendar"></i> Feriados</a></li>
                            </ul>
                        </li>
                    <?php
                    endif;
                    if ($user->arquivos == "X" || $user->id_emp2 == 1):
                    ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="<?= url("files/lista") ?>">
                                <i class="align-middle" data-feather="folder"></i> <span
                                    class="align-middle">Arquivos</span>
                            </a>
                        </li>
                    <?php
                    endif;
                    ?>
                </ul>
                <?php
                if ($user->tipo == 5):
                ?>
                    <div class="sidebar-item">
                        <a class="sidebar-link" href="<?= url("emp1") ?>">
                            <i class="align-middle me-1" data-feather="shield"></i> Grupos
                        </a>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link" href="<?= url("emp2") ?>">
                            <i class="align-middle me-1" data-feather="shield"></i> Empresas
                        </a>
                    </div>
                <?php
                endif;
                ?>
                <div class="sidebar-item">
                    <a class="sidebar-link" href="<?= url("logout") ?>">
                        <i class="align-middle me-1" data-feather="log-out"></i> Log out
                    </a>
                </div>
            </div>
        </nav>

        <div class="main">
            <?php
            $usuario = (new Users())->findById($user->id);
            $empresa = (new Emp2())->findById($usuario->id_emp2);
            $grupo = (new Emp1())->findById($empresa->id_emp1);
            $empresasDoGrupo = (new Emp2())->find("id_emp1 = :id_emp1", "id_emp1={$empresa->id_emp1}", "*", false)->fetch(true);

            $emp2 = (new Emp2())->findById($user->id_emp2);

            $cnpjCriptografada = cnpj_cript($emp2->cnpj, 'C');
            //$cnpjCriptografada = cnpj_cript("05.262.158/0001-53", 'C');
            // $cnpjDescriptografada = cnpj_cript('56.419.031/0001-36', 'C');
            // var_dump($cnpjDescriptografada);

            $consulta = verificaPermissao($cnpjCriptografada);
            //$consulta = verificaPermissao("ytauqwawetdqqqwsyw");
            //$consulta = verificaPermissao("twaueiaiepdqqqwsiq");

            $licenca = verificaExpiracao($consulta);

            ?>
            <nav class="navbar navbar-expand navbar-light navbar-bg <?= $licenca == '2' ? 'licenca-vencida' : '' ?>">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
                <?php

                $currentUri = $_SERVER['REQUEST_URI'];

                if (!str_contains($currentUri, '/emp1') && !str_contains($currentUri, '/emp2')) {
                ?>

                    <?php
                    if ($empresa->id_emp1 != 1 && $user->tipo != 3 && $user->tipo != 5):
                        if (count($empresasDoGrupo) > 1):
                    ?>
                            <div class="navbar-title d-flex justify-content-center flex-grow-1">
                                <?= $grupo->descricao; ?>
                            </div>
                            <div class="select-emp d-flex align-items-center">
                                <form action="<?= url("swap") ?>" method="post" id="form-empgrupo">
                                    <select name="empresa_select" id="emp2-select" class="form-select swap-select">
                                        <?php
                                        foreach ($empresasDoGrupo as $emp):
                                        ?>
                                            <option value="<?= ll_encode($emp->id); ?>" <?= ($emp->id == $user->id_emp2) ? "selected" : ""; ?>><?= $emp->razao; ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <button type="submit" class="btn-swap" id="btn-empgrupo"><i
                                            class="fa-solid fa-retweet"></i></button>
                                </form>
                            </div>
                    <?php
                        endif;
                    endif;
                    ?>
                <?php
                }
                ?>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                                data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                                data-bs-toggle="dropdown">
                                <span class="text-dark"><?= $this->e($user->nome); ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="<?= url("profile") ?>"><i class="fa-solid fa-user"></i>
                                    Perfil</a>
                                <a class="dropdown-item <?= $_SESSION['authEmp'] == 1 ? "disabled-link" : ""; ?>"
                                    href="<?= url("emp") ?>"><i class="fa-solid fa-building"></i>
                                    Empresa</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= url("logout") ?>"><i
                                        class="fa-solid fa-right-from-bracket"></i>
                                    Log out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content" style="<?= (!empty($empTit)) ? "background-image: url('" . CONF_FILES_URL . $emp2->logo . "')" : ""; ?>">
                <div class="container">
                    <h1 class="titulo-pai d-flex">
                        <?= (!empty($tituloPai)) ? $this->e($tituloPai) : ""; ?>
                        <?= (!empty($tituloPai) && !empty($secTit)) ? '<span style="display: inline-block; width: 100px;"></span>' : ""; ?>
                        <span class="titulo-secao" ><?= (!empty($secTit)) ? $this->e($secTit) : ""; ?></span>
                        <span
                            class="titulo-secao titulo-empresa direita" style="word-break: break-all; font-size: 1.8rem;"><?= (!empty($empTit)) ? $this->e($empTit) : ""; ?></span>
                        <!-- <span class="rel-options titulo-empresa">
                                <?php if (!empty($relatorio)): ?>
                                    <button class="btn"><i class="fa fa-gear"></i></button>
                                <?php endif; ?>
                            </span> -->
                    </h1>
                    <div class="container-fluid" style="margin-top: 20px; z-index: 9999999;">
                        <?= $this->section("content"); ?>
                    </div>
                </div>
            </main>
            <footer class="footer d-none d-md-block <?= $licenca == '2' ? 'licenca-vencida' : '' ?>">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-md-4 text-start">
                            <p class="mb-0">
                                Taskforce - Desenvolvido por <a class="text-muted" href="https://www.tcsistemas.com" target="_blank"><strong>TC Sistemas</strong></a>(Versão 1.1.0)
                            </p>
                        </div>
                        <?php
                        if ($licenca == '2'):
                        ?>
                            <div class="col-md-4">
                                <p class="mb-0 licenca-text" style="color: red; text-align: center;">
                                    <strong>ATENÇÃO!!! ENTRAR EM CONTATO COM A TASKFORCE</strong>
                                </p>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div class="col-md-4 direita" style="text-align: right;">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a class="text-muted" href="#">Support</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="#">Help Center</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="#">Privacy</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="#">Terms</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?= $this->section("js"); ?>
</body>

</html>