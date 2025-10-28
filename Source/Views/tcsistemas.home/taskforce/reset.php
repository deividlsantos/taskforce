<?php $this->layout("_login", $front); ?>

<article class="auth">
    <div class="auth_content container content">
        <header class="auth_header">
            <h1>Criar nova senha</h1>
            <p>Informe e repita uma nova senha para recuperar o acesso.</p>
        </header>

        <form class="auth_form" action="<?= url("recuperar/resetar"); ?>" method="post" enctype="multipart/form-data">            
            <input type="hidden" name="token" value="<?= $code; ?>" />
            <input type="hidden" name="email" value="<?= $email; ?>" />
            <?= csrf_input(); ?>
            <label>
                <div class="unlock-alt">
                    <span class="icon-envelope">Nova Senha:</span>
                    <span><a title="Voltar e entrar" href="<?= url("login"); ?>">Voltar e entrar!</a></span>
                </div>
                <input type="password" name="senha" placeholder="Nova senha:" required />
            </label>

            <label>
                <div class="unlock-alt"><span class="icon-envelope">Repita a nova senha:</span></div>
                <input type="password" name="senhaRe" placeholder="Repita a nova senha:" required />
            </label>

            <button class="auth_form_btn transition gradient gradient-green gradient-hover">Alterar Senha</button>
        </form>
    </div>
</article>