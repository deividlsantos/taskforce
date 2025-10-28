<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
    <link href="<?= url("Source/Views/css/app.css") ?>" rel="stylesheet" type="text/css">
    <link href="<?= url("Source/Views/css/fontgoogleapis/fontsgoogleapis.css"); ?>" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">

    <title>TaskForce</title>
</head>

<body style="background-color: #fff;">
    <article class="optin_page">
        <div class="center">

            <div class="logo">
                <img src="<?= url("Source/Images/concluido-img.png") ?>" alt="Task Force" title="Task Force">
            </div>
            <!-- <img alt="<?= $data->title; ?>" title="<?= $data->title; ?>" src="<?= $data->image; ?>" /> -->

            <h1><?= $data->title; ?></h1>
            <p><?= $data->desc; ?></p>
            <?php if (!empty($data->link)) : ?>
                <a class="btn-custom"
                    href="<?= $data->link; ?>" title="<?= $data->linkTitle; ?>"><?= $data->linkTitle; ?></a>
            <?php endif; ?>

        </div>
    </article>
</body>