<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="fcad-form-row" style="margin-right: 0px; padding-right: 0px;">
        <div class="fcad-form-group coluna50">
            <div class="func-container tela-settings-usr">
                <form class="form-cadastros form-sett-usr" id="form-turno" action="<?= url("profile") ?>">
                    <div class="fcad-form-row" hidden>
                        <label for="id_users">Código</label>
                        <input type="text" id="id_users" name="id_users" value="<?= ll_encode(($front['user'])->id); ?>">
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="nome">Usuário <span class="required">*</span></label>
                            <input type="text" id="nome" name="nome" class="" value="<?= ($front['user'])->nome; ?>" required>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="email">Email</label>
                            <input type="text" id="email" name="email" class="" value="<?= ($front['user'])->email; ?>"
                                disabled>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="senha">Senha</label>
                            <div class="password-container">
                                <input type="password" id="senha" name="senha" class="" value="">
                                <button type="button" class="toggle-password" data-target="#senha">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fcad-form-group">
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
                        <button class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                    </div>
                </form>
            </div>
            <div class="rodape" hidden>
                <span class="required">*</span> = Campos Obrigatórios
            </div>
        </div>
        <?php
        if (!empty($usuario) && $usuario->tipo == 5):
        ?>
            <div class="fcad-form-group coluna30 direita">
                <div>
                    <label>Empresa Atual</label>
                    <h2><?= $usuario->empresa; ?></h2>
                </div>
                <div>
                    <form class="d-flex flex-column" action="<?= url("swapdv") ?>" id="form-trocar-empresa" method="post">
                        <div class="fcad-form-row">
                            <label>Trocar Empresa</label>
                            <select id="emp_dev" name="emp_dev">
                                <option value="">Selecione uma empresa</option>
                                <?php if (!empty($empresas)) : ?>
                                    <?php foreach ($empresas as $empresa) : ?>
                                        <option value="<?= $empresa->id; ?>"><?= "{$empresa->id} - {$empresa->razao}"; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="fcad-form-row">
                            <div class="fcad-form-group">
                                <button class="btn btn-success" id="trocar_empresa"><i class="fa fa-retweet"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php
        endif
        ?>
    </div>
</body>

</html>