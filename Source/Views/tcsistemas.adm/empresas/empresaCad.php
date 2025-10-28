<?php

use Source\Models\Emp1;

$this->layout("_theme", $front);
?>
<style>
    .cadastro-container {
        max-width: 880px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .cadastro-header {
        padding: 1.5rem 2rem;
        background: linear-gradient(45deg, #2563eb, #1e40af);
        color: #fff;
    }

    .cadastro-header__titulo {
        font-size: 1.8rem;
        font-weight: 600;
        letter-spacing: -0.025em;
        color: #fff;
    }

    .cadastro-section {
        padding: 2rem;
        border-bottom: 1px solid #e2e8f0;
        background: rgba(241, 245, 249, 0.3);
    }

    .cadastro-section__titulo {
        font-size: 1.3rem;
        color: #1e40af;
        margin-bottom: 1.5rem;
        position: relative;
        padding-left: 1.75rem;
    }

    .cadastro-section__titulo::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        background: currentColor;
        border-radius: 50%;
    }

    .cadastro-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }

    .cadastro-input-group {
        position: relative;
    }

    .cadastro-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #1f2937;
        font-size: 0.875rem;
    }

    .cadastro-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
    }

    .cadastro-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    .cadastro-input--cnpj {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232563eb"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 12h2v2H7zm8 0h2v2h-2zm-8 4h2v2H7zm4-4h2v2h-2zm4 0h2v2h-2zm-8-4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 24px;
    }

    .cadastro-upload {
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .cadastro-upload:hover {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }

    .cadastro-button {
        background: linear-gradient(45deg, #2563eb, #1e40af);
        color: #fff;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cadastro-button2 {
        background: linear-gradient(45deg, #2563eb, #1e40af);
        color: #fff;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 30px;
    }

    .cadastro-input[type="tel"],
    .cadastro-input[type="email"],
    select.cadastro-input {
        appearance: none;
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 16px;
    }

    select.cadastro-input {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232563eb"><path d="M7 10l5 5 5-5z"/></svg>');
    }

    .cadastro-input[type="tel"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232563eb"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>');
    }

    .cadastro-input[type="email"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232563eb"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>');
    }

    @media (max-width: 768px) {
        .cadastro-container {
            margin: 0;
            border-radius: 0;
        }

        .cadastro-section {
            padding: 1.5rem;
        }

        .cadastro-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="cadastro-container">
    <header class="cadastro-header">
        <h1 class="cadastro-header__titulo">Cadastro Corporativo</h1>
    </header>

    <form class="cadastro-form" action="<?= url("emp2/salvar") ?>" method="POST" enctype="multipart/form-data">
        <section class="cadastro-section">
            <h2 class="cadastro-section__titulo">Informações da Empresa</h2>
            <input type="text" hidden name="id" value="<?= $empresa != "" ? $empresa->id : "" ?>">
            <div class="cadastro-grid">
                <div class="cadastro-input-group">
                    <label for="grupo">Grupo:</label>
                    <select id="grupo" name="grupo" class="cadastro-input">
                        <option value="">Selecione um grupo</option>
                        <?php if (!empty($grupos)) : ?>
                            <?php foreach ($grupos as $grupo) : ?>
                                <option value="<?= $grupo->id; ?>" <?= ($empresa && $empresa->id_emp1 == $grupo->id) ? 'selected' : '' ?>>
                                    <?= $grupo->descricao; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="razao_social">Razão Social</label>
                    <input type="text" class="cadastro-input" id="razao_social" name="razao" value="<?= $empresa != "" ? $empresa->razao : "" ?>" required>
                </div>


                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="nome_fantasia">Nome Fantasia</label>
                    <input type="text" class="cadastro-input" id="nome_fantasia" name="fantasia" value="<?= $empresa != "" ? $empresa->fantasia : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="cnpj">CNPJ</label>
                    <input type="text" class="cadastro-input cadastro-input--cnpj mask-cnpj" id="cnpj" name="cnpj" value="<?= $empresa != "" ? $empresa->cnpj : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label">Logo Corporativa</label>
                    <div class="cadastro-upload">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 0.5rem;">
                            <path d="M19 13v5a2 2 0 0 1-2 2H6l-4-4V5a2 2 0 0 1 2-2h8m4-2h-8l8 8h-8a2 2 0 0 0-2 2v8l4-4h10a2 2 0 0 0 2-2V5l-4-4z" />
                        </svg>
                        <p>Arraste ou clique para enviar</p>
                        <input type="file" id="logo" name="logo" value="<?= $empresa != "" ? $empresa->logo : "" ?>" accept="image/*">
                    </div>
                </div>
            </div>
        </section>


        <section class="cadastro-section">
            <h2 class="cadastro-section__titulo">Endereço</h2>
            <div class="cadastro-grid">
                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="logradouro">Logradouro</label>
                    <input type="text" class="cadastro-input" id="logradouro" name="logradouro" value="<?= $empresa != "" ? $empresa->endereco : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="numero">Número</label>
                    <input type="text" class="cadastro-input" id="numero" name="numero" value="<?= $empresa != "" ? $empresa->numero : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="bairro">Bairro</label>
                    <input type="text" class="cadastro-input" id="bairro" name="bairro" value="<?= $empresa != "" ? $empresa->bairro : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="cidade">Cidade</label>
                    <input type="text" class="cadastro-input" id="cidade" name="cidade" value="<?= $empresa != "" ? $empresa->cidade : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="uf">UF</label>
                    <select class="cadastro-input" id="uf" name="uf" value="<?= $empresa != "" ? $empresa->uf : "" ?>">
                        <?php
                        $estados = [
                            "AC" => "Acre",
                            "AL" => "Alagoas",
                            "AP" => "Amapá",
                            "AM" => "Amazonas",
                            "BA" => "Bahia",
                            "CE" => "Ceará",
                            "DF" => "Distrito Federal",
                            "ES" => "Espírito Santo",
                            "GO" => "Goiás",
                            "MA" => "Maranhão",
                            "MT" => "Mato Grosso",
                            "MS" => "Mato Grosso do Sul",
                            "MG" => "Minas Gerais",
                            "PA" => "Pará",
                            "PB" => "Paraíba",
                            "PR" => "Paraná",
                            "PE" => "Pernambuco",
                            "PI" => "Piauí",
                            "RJ" => "Rio de Janeiro",
                            "RN" => "Rio Grande do Norte",
                            "RS" => "Rio Grande do Sul",
                            "RO" => "Rondônia",
                            "RR" => "Roraima",
                            "SC" => "Santa Catarina",
                            "SP" => "São Paulo",
                            "SE" => "Sergipe",
                            "TO" => "Tocantins"
                        ];
                        ?>
                        <option value="">Selecione</option>
                        <?php foreach ($estados as $sigla => $nome) : ?>
                            <option value="<?= $sigla ?>" <?= $empresa != "" && $empresa->uf == $sigla ? "selected" : "" ?>><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="cep">CEP</label>
                    <input type="text" class="cadastro-input mask-cep" id="cep" name="cep" value="<?= $empresa != "" ? $empresa->cep : "" ?>" pattern="\d{5}-\d{3}">
                </div>
            </div>
        </section>

        <section class="cadastro-section">
            <h2 class="cadastro-section__titulo">Contatos</h2>
            <div class="cadastro-grid">
                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="telefone1">Telefone 1</label>
                    <input type="tel" class="cadastro-input" id="fone1" name="telefone1" value="<?= $empresa != "" ? $empresa->fone1 : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="telefone2">Telefone 2</label>
                    <input type="tel" class="cadastro-input" id="fone2" name="telefone2" value="<?= $empresa != "" ? $empresa->fone2 : "" ?>">
                </div>

                <div class="cadastro-input-group">
                    <label class="cadastro-label" for="email">E-mail</label>
                    <input type="email" class="cadastro-input" id="email" name="email" value="<?= $empresa != "" ? $empresa->email : "" ?>">
                </div>
            </div>
        </section>

        <section class="cadastro-section">
            <button type="submit" class="cadastro-button"><i class="fa fa-save"></i>
                Salvar Cadastro
            </button>
            <div>
                <a class="cadastro-button2" href="<?= url("emp2") ?>">Voltar</a>
            </div>
        </section>
    </form>
</div>

</html>