<!-- Modal de Edição -->
<div class="modal fade editmat-modal" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label class="editarItem">Editar: <span id="editItemName"></span></label>
                    </div>
                    <div class="form-group" hidden>
                        <label for="editItemId">ID do Item</label>
                        <input type="text" class="form-control" id="editItemId" readonly>
                    </div>
                    <div class="form-group quantity-input">
                        <button type="button" class="btnqtde" id="decreaseQuantity2">-</button>
                        <input type="number" class="form-control" id="quantityInput2" value="1" min="1">
                        <button type="button" class="btnqtde" id="increaseQuantity2">+</button>
                        <button type="button" class="btnSaveOs3" id="saveOs3edit"><i class="fa-solid fa-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>