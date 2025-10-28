<!-- Modal -->
<div class="modal fade modal-operobs" id="obsModal" data-url="<?= url("oper_ordens/obs"); ?>" tabindex="-1" role="dialog" aria-labelledby="opermatModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="obsModalLabel">Observações</h2>                
                <button type="button" id="close-obs" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="txtObsTarefa" rows="5" placeholder="Observação"></textarea>
                <div class="fcad-form-row">
                    <div id="charCount1" class="char-count">500</div>
                    <button type="button" class="btnSaveObs direita" id="saveObs"><i class="fa-solid fa-check"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>