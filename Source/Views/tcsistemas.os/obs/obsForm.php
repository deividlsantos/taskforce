<div class="form-row" hidden>
    <label for="id_obs">Código</label>
    <input type="text" id="id_obs" name="id_obs"
        value="<?= ($obs != "") ? ll_encode($obs->id) : ''; ?>">
</div>
<div class="fcad-form-row">
    <div class="fcad-form-group">
        <label for="descricao">Descrição <span class="required">*</span></label>
        <textarea type="text" id="descricao" name="descricao"><?= ($obs != "") ? $obs->descricao : ''; ?></textarea>
    </div>
</div>