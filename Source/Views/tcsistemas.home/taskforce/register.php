<?php $this->layout("tcsistemas.home/taskforce/index", $front); ?>

<div class="optin">
    <div class="conteiner2">
        <div class="conteudo">
            <div class="column_register">
                <h2 class="title">Já possui uma conta?</h2>
                <p class="description">Para continuar conectado</p>
                <p class="description">Por favor logue com suas informações pessoais.</p>
                <a href="<?= url("login") ?>" class="btn1">Entrar</a>
            </div>
            <div class="column_register second-column">
                <h2 class="title title_criar">Criar conta</h2>
                <form class="form" action="<?= url("cadastrar") ?>" method="post">
                    <input type="text" name="nome" placeholder="Nome:" required>
                    <input type="email" name="email" placeholder="Informe seu e-mail:" required>
                    <input type="password" name="senha" placeholder="Informe sua senha:" required>
                    <button class="btn2">Criar</button>
                </form>
            </div>
        </div>
    </div>
</div>