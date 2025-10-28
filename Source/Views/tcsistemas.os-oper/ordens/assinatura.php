<div class="modal fade modal-assinatura" id="modalAssinatura" tabindex="-1" aria-labelledby="modalAssinaturaLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Mensagem vertical à esquerda -->
                <div class="left-message">
                    <p>Esse lado para baixo</p>
                </div>

                <!-- Canvas para assinatura -->
                <canvas id="signature-pad"></canvas>

                <!-- Botões alinhados à direita -->
                <div class="modal-footer modal-footer-right">
                    <button type="button" class="btn btn-secondary" id="limpasign">Limpar</button>
                    <button type="button" class="btn btn-primary" id="savesign">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>