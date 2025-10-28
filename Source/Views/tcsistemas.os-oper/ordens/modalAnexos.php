<!-- Modal -->
<div class="modal fade modal-operanexos" id="anexosModal" data-url="<?= url("oper_ordens/anexos"); ?>" tabindex="-1" role="dialog" aria-labelledby="anexosModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-header">
                    <h2 class="modal-title" id="anexosModalLabel">Anexos</h2>
                    <button id="toggleViewButton" type="button" class="fa-solid fa-grip"></button>
                    <button type="button" id="close-anexos" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="galleryContainer" class="gallery-container gallery-3">
                        <div class="gallery-item attach-plus">
                            <form>
                                <label for="fileInput" class="btn"><span class="fa fa-camera icon-text"></span></label>
                                <input type="file" id="fileInput" accept="image/*" capture="environment" style="display: none;">
                            </form>
                        </div>
                        <div class="gallery-item attach-plus">
                            <form>
                                <label for="pastasInput" class="btn"><span class="fa-solid fa-plus plus-icon"></span></label>
                                <input type="file" id="pastasInput" accept="image/*,.pdf,.txt,.doc,.docx,.xls,.xlsx,.rar,.zip" style="display: none;">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>