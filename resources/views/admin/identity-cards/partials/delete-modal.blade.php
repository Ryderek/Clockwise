<form method="POST" action="{{ route('identity-card.remove') }}" class="modal fade" id="removeIdentityCardModal" tabindex="-1" aria-labelledby="removeIdentityCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeIdentityCardModalLabel">Usuwanie karty</h5>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="deleteIdentityCardId" id="deleteIdentityCardId" value="0" />
                <div class="mb-3">
                    Czy na pewno chcesz usunąć kartę należącą do <b id="removeIdentityCardName"></b>?<br />
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