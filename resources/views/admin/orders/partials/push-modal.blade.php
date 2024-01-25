<div class="modal fade" id="pushOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="pushOrderLabel" aria-hidden="true">
    <form class="modal-dialog" method="POST" action="{{ route('order.push') }}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5" id="pushOrderLabel">Zatwierdzanie zlecenia</h3>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @csrf
            </div>
            <div class="modal-body">
                Czy na pewno chcesz przekazać zlecenie <b id="pushOrderOrderName"></b> dla kontrahenta <b id="pushOrderCustomerName"></b>?<br /><br />
                <span class="text-danger">Po zatwierdzeniu, zlecenie zostanie przeniesione do zakładki "<span id="pushOrderTabName" ></span>". Tej operacji nie można cofnąć.</span>
                <input type="hidden" id="pushOrderWithId" name="pushOrderWithId" />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn">Zatwierdź</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </div>
    </form>
</div>
