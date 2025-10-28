<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
    <title><?= $this->e($titulo) ?></title>
    <link href="<?= url("Source/Views/css/bootstrap.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/boot.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontgoogleapis/fontsgoogleapis.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/fontawesome/css/all.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/select2.min.css"); ?>" rel="stylesheet">
    <link href="<?= url("Source/Views/css/opermob.css?v=") . filemtime('Source/Views/css/opermob.css'); ?>" rel="stylesheet">
    <script src="<?= url("Source/Views/js/moment.min.js?v=") . filemtime('Source/Views/js/moment.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.js?v=") . filemtime('Source/Views/js/jquery.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/bootstrap.bundle.min.js?v=") . filemtime('Source/Views/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/app.js?v=") . filemtime('Source/Views/js/app.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/tablesorter/js/jquery.tablesorter.min.js?v=") . filemtime('Source/Views/js/tablesorter/js/jquery.tablesorter.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/chart.js?v=") . filemtime('Source/Views/js/chart.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.form.js?v=") . filemtime('Source/Views/js/jquery.form.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery-ui.min.js?v=") . filemtime('Source/Views/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/jquery.mask.js?v=") . filemtime('Source/Views/js/jquery.mask.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/fullcalendar.js?v=") . filemtime('Source/Views/js/fullcalendar.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/gcal.min.js?v=") . filemtime('Source/Views/js/gcal.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/select2.min.js?v=") . filemtime('Source/Views/js/select2.min.js'); ?>"></script>
    <script src="<?= url("Source/Views/js/opermob.js?v=") . filemtime('Source/Views/js/opermob.js'); ?>"></script>


</head>

<body>
    <div class="container">
        <?php
        if (!empty($nav)):
        ?>
            <div class="oper-nav">
                <a href="<?= url($this->e($navback)); ?>"><span class="fa-solid fa-left-long"></span></a>
                <span><?= $this->e($nav); ?></span>
                <span><a href="<?= url($this->e($navlink)) ?>" id="os-refresh" class="btn" style="margin-right: 20px; color:#fff;" type="button"><i class="fa-solid fa-rotate"></a></i><i class="fa-solid fa-ellipsis-vertical"></i></span>
            </div>
        <?php
        endif;
        ?>
        <!-- Card central -->
        <?= $this->section("content"); ?>

    </div>

    <?= $this->section("js"); ?>
</body>

</html>