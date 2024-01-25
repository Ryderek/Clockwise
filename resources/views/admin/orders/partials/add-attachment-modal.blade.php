<form method="POST" action="{{ route('attachment.create') }}" class="modal fade" id="addAttachmentModal" tabindex="-1" aria-labelledby="addAttachmentModalLabel" aria-hidden="true" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAttachmentModalLabel">Dodawanie załacznika</h5>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="createAttachmentId" id="createAttachmentId" value="0" />
                <div class="mb-3"> 
                    <input type="hidden" class="form-control" name="attachmentRelatorSlug" value="order" readonly required>
                    <input type="hidden" class="form-control" name="attachmentRelatorId" value="{{ $order->orderId }}" readonly required>
                    <input type="text" class="form-control" name="attachmentTitle" placeholder="Tytuł załącznika" aria-label="Tytuł załącznika" required>
                    <input type="file" class="form-control mt-2" name="attachmentContent" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="sumbit" class="btn btn-primary">Dodaj załącznik</button>
            </div>
        </div>
    </div>
</form>