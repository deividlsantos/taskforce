<?php $this->layout("tcsistemas.home/taskforce/index", $front); ?>

<div class="optin">
    <div class="conteiner2">
        <div class="conteudo">
            <div class="column_recover">
                <img class="logo_tf" src="<?= url("Source/Images/LOGO-06.png") ?>" alt="TaskForce" title="TaskForce">
            </div>
            <div class="column_recover second-column">
                <h1 class="title_criar">Recuperar senha</h1>
                <p class="description">Informe seu e-mail para receber um link de recuperação.</p>
                <form class="auth_form" data-reset="true" action="<?= url("recuperar"); ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_input(); ?>

                    <div class="form_recover">
                        <span class="email">Email:</span>
                        <input type="email" name="email" placeholder="Informe seu e-mail:" required>
                        <button class="btn2">Recuperar</button>
                        <a class="btn2" href="<?= url("login"); ?>">Voltar e entrar!</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>