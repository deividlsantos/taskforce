<!-- Modal -->
<div class="modal fade modal-opermat" id="opermatModal" data-url="<?= url("oper_ordens/mat") ?>" tabindex="-1" role="dialog" aria-labelledby="opermatModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="obsModalLabel">Produtos/Materiais</h2>
                <button type="button" id="close-opermat" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body" id="opermatBody">
                <form>
                    <div class="form-group">
                        <select class="form-control opermat-select" id="selectItem">
                        </select>
                    </div>
                    <div class="form-group quantity-input">
                        <button type="button" class="btnqtde" id="decreaseQuantity">-</button>
                        <input type="number" class="form-control" id="quantityInput" value="1" min="1">
                        <button type="button" class="btnqtde" id="increaseQuantity">+</button>
                        <button type="button" class="btnSaveOs3" id="saveOs3"><i class="fa-solid fa-check"></i></button>
                    </div>
                </form>
            </div>
            <div class="opermat-container"></div>
        </div>
    </div>
</div>