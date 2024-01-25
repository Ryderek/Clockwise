<div class="modal fade" id="removeOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="removeOrderLabel" aria-hidden="true">
    <form class="modal-dialog" method="POST" action="{{ route('order.remove') }}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5" id="removeOrderLabel">Usuwanie zlecenia</h3>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @csrf
            </div>
            <div class="modal-body">
                Czy na pewno chcesz usunąć to zlecenie?<br /><br />
                <span class="text-danger">Po zatwierdzeniu, zlecenie zostanie usunięte. Tej operacji nie można cofnąć.</span>
                <input type="hidden" id="removeOrderWithId" name="removeOrderWithId" />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn">Zatwierdź</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </div>
    </form>
</div>
