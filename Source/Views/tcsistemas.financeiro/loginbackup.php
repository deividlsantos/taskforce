<?php $this->layout("_login", $front); ?>



<div class="login">
    <div><img src="<?= url("Source/Images/crm.png"); ?>" width="100%"></div>
    <article class="login_box radius">        
        <h1 class="hl icon-coffee">Login</h1>        

        <form name="login" action="<?= url("login"); ?>" method="post">
            <?= csrf_input(); ?>
            <label>
                
                <input name="email" type="email" value="<?= ($cookie ?? null); ?>" placeholder="Informe seu e-mail" />
            </label>

            <label>
                
                <input name="senha" type="password" placeholder="Informe sua senha:"/>
            </label>

            <label class="check">
                <input type="checkbox" <?= (!empty($cookie) ? "checked" : ""); ?> name="save" />
                <span>Lembrar email?</span>
            </label>

            <button class="radius gradient gradient-dark-blue-3 gradient-hover icon-sign-in">Entrar</button>
        </form>

        <footer>
            <p>Desenvolvido por www.<b>tcsistemas</b>.com</p>
            <p>&copy; <?= date("Y"); ?> - todos os direitos reservados</p>
        </footer>
    </article>
</div>