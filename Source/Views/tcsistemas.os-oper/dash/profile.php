<?php
$this->layout("_theme_oper", $front);
?>

<body>
    <div class="func-container tela-settings-usr">
        <form class="form-cadastros form-oper" id="form-opermob" action="<?= url("oper_dash/oper") ?>">
            <div class="fcad-form-row" hidden>
                <label for="id_users">Código</label>
                <input type="text" id="id_users" name="id_users" value="<?= ll_encode(($front['user'])->id); ?>">
                <input type="text" id="opermob" name="opermob" value="opermob">
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="nome">Usuário</label>
                    <input type="text" id="nome" name="nome" class="" value="<?= ($front['user'])->nome; ?>" required>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" class="" value="<?= ($front['user'])->email; ?>"
                        disabled>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna30">
                    <label for="senha">Senha</label>
                    <div class="password-container">
                        <input type="password" id="senha" name="senha" class="" value="">
                        <button type="button" class="toggle-password" data-target="#senha">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna30">
                    <label for="senha_re">Confirmar Senha</label>
                    <div class="password-container">
                        <input type="password" id="senha_re" name="senha_re" class="" value="">
                        <button type="button" class="toggle-password" data-target="#senha_re">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="fcad-form-row">

            </div>
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
            </div>
        </form>
    </div>
    <div class="rodape" hidden>
        <span class="required">*</span> = Campos Obrigatórios
    </div>
</body>

</html>