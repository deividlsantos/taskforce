<div class="form-row" hidden>
    <label for="id_tipo">Código</label>
    <input type="text" id="id_tipo" name="id_tipo"
        value="<?= ($tipo != "") ? ll_encode($tipo->id) : ''; ?>">
</div>
<div class="fcad-form-row">
    <div class="fcad-form-group">
        <label for="descricao">Descrição <span class="required">*</span></label>
        <input type="text" id="descricao" name="descricao" value="<?= !empty($tipo) ? $tipo->descricao : ''; ?>">
    </div>
</div>