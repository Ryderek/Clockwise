<form method="POST" action="{{ route('tool.delete') }}" class="modal fade" id="removeToolModal" tabindex="-1" aria-labelledby="removeToolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeToolModalLabel">Usuwanie narzędzia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="deleteToolId" id="deleteToolId" value="0" />
                <div class="mb-3">
                    Czy na pewno chcesz usunąć narzędzie <b id="removeToolName"></b>?<br />
                    <span class="text-danger">Tej operacji nie można cofnąć.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary">Usuń</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </div>
    </div>
</form>