<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" id="form-users" action="<?= url("users/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("users") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_users" name="id_users" value="<?= ($users != "") ? ll_encode($users->id) : ''; ?>" hidden>
        <div class="users-form">
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="tipo_user">Tipo:</label>
                    <select id="tipo_user" name="tipo" class="<?= $user->nivel < 5 ? 'select-readonly' : ''; ?>">
                        <option value="1" <?= ($users != "" && $users->tipo == 1) ? 'selected' : ''; ?>>Administrador/Gestor</option>
                        <option value="2" <?= ($users != "" && $users->tipo == 2) ? 'selected' : ''; ?>>Operador de campo</option>
                        <option value="3" <?= ($users != "" && $users->tipo == 3) ? 'selected' : ''; ?>>Operador Desktop</option>
                        <option value="5" <?= ($users != "" && $users->tipo == 5) ? 'selected' : ''; ?>>Desenvolvedor</option>
                    </select>
                </div>
                <div class="fcad-form-group coluna40">
                    <label for="ent_user">Colaborador:</label>
                    <select id="ent_user" name="ent">
                        <option value="0">Selecione</option>
                        <?php
                        if (!empty($colaboradores)) :
                            foreach ($colaboradores as $colaborador) :
                                $temp = "";
                                if (!empty($users)):
                                    if ($colaborador->id == $users->id_ent):
                                        $temp = "selected";
                                    endif;
                                endif;
                        ?>
                                <option value="<?= $colaborador->id; ?>" <?= $temp; ?>><?= $colaborador->nome; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= ($users != "") ? $users->email : ''; ?>">
                </div>
                <div class="fcad-form-group coluna40">
                    <label for="nome_user">Nome Usuário:</label>
                    <input type="text" id="nome_user" name="nome" value="<?= ($users != "") ? $users->nome : ''; ?>">
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
            <div class="fcad-form-row permissao-menu">
                <label for="permissoes">Permissões Menu</label>
            </div>
            <div class="fcad-form-row">
                <div class="checkbox-group permissao-item coluna15">
                    <label for="os_sidebar">Ordem de Serviço</label>
                    <input class="coluna05" type="checkbox" id="os_sidebar" name="os_sidebar" <?= ($users != "" && $users->os == "X") ? 'checked' : ''; ?>>
                </div>
                <div class="checkbox-group permissao-item coluna15">
                    <label for="financeiro_sidebar">Financeiro</label>
                    <input class="coluna05" type="checkbox" id="financeiro_sidebar" name="financeiro_sidebar" <?= ($users != "" && $users->financeiro == "X") ? 'checked' : ''; ?>>
                </div>
                <div class="checkbox-group permissao-item coluna15">
                    <label for="cadgeral_sidebar">Cadastros Gerais</label>
                    <input class="coluna05" type="checkbox" id="cadgeral_sidebar" name="cadgeral_sidebar" <?= ($users == "" || $users->cadastros == "X") ? 'checked' : ''; ?>>
                </div>
                <div class="checkbox-group permissao-item coluna15">
                    <label for="ponto_sidebar">Cartão de Ponto</label>
                    <input class="coluna05" type="checkbox" id="ponto_sidebar" name="ponto_sidebar" <?= ($users != "" && $users->ponto == "X") ? 'checked' : ''; ?>>
                </div>
                <div class="checkbox-group permissao-item coluna15">
                    <label for="arquivos_sidebar">Arquivos</label>
                    <input class="coluna05" type="checkbox" id="arquivos_sidebar" name="arquivos_sidebar" <?= ($users == "" || $users->arquivos == "X") ? 'checked' : ''; ?>>
                </div>
            </div>
        </div>
    </form>
</div>