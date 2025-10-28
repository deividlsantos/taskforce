

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
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding-top: 80px; }
        .wrapper { max-width: 450px; margin: 0 auto; }
        a { display: inline-block; margin-top: 20px; text-decoration: none; color: #fff; background: #a52834; padding: 8px 15px; border-radius: 4px; }
    </style>
</head>


<body style="background-color: #fff;">
    <article class="optin_page">
        <div class="center">

            <div class="logo">
                <img src="<?= url("Source/Images/warning.png") ?>" alt="Task Force" title="Task Force">
            </div>
            <!-- <img alt="<?= $data->title; ?>" title="<?= $data->title; ?>" src="<?= $data->image; ?>" /> -->

            <h1><?= $data->title; ?></h1>
            <p><?= $data->desc; ?></p>
            <?php if (!empty($data->link)) : ?>
                <a class="btn-custom" style="width: 15em;"
                    href="https://wa.me/5517988020232?text=N%C3%A3o%20consigo%20logar%20no%20Taskforce." title="<?= $data->linkTitle; ?>"><?= $data->linkTitle; ?></a>
            <?php endif; ?>
            <a class="btn-custom" style="width: 15em;"
                    href="<?= url("login")?>" title="Login">VOLTAR AO LOGIN</a>

        </div>
    </article>
</body>
