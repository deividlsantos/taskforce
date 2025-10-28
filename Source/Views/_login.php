<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
    <title><?= $titulo ?></title>
    
    <script src="<?= url("Source/Views/js/jquery.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.form.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery-ui.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.mask.js"); ?>"></script>    
    <script src="<?= url("Source/Views/js/tinymce/jquery.tinymce.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/tinymce/tinymce.min.js"); ?>"></script>    
    <script src="<?= url("Source/Views/js/login/loginscripts.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/login/animsition.min.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/login/countdowntime.js"); ?>"></script>
    <script src="<?= url("Source/Views/js/login/daterangepicker.js"); ?>"></script>    
    <script src="<?= url("Source/Views/js/main.js"); ?>"></script>
    <link href="<?= url("Source/Views/css/boot.css"); ?>" rel="stylesheet">    
    <link href="<?= url("Source/Views/css/style.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontgoogleapis/fontsgoogleapis.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login/animate.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login/animsition.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login/daterangepicker.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/login/hamburgers.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/main.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/util.css"); ?>" rel="stylesheet">

</head>

<body>

    <div class="ajax_load" style="z-index: 999;">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <p class="ajax_load_box_title">Aguarde, carregando...</p>
        </div>
    </div>

    <div class="ajax_response"><?= flash(); ?></div>


    <?= $this->section("content"); ?>

</body>

</html>