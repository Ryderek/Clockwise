<div class="modal fade" id="doneWorkTimeModal" tabindex="-1" aria-labelledby="doneWorkTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route("production.processing.done") }}">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="doneWorkTimeModalLabel">Zakończ partię produktów</h1>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="workTimingId" name="workTimingId" readonly>
                <div class="mb-3">
                    <label for="message-text" class="col-form-label">Ile detali udało się wykonać?</label>
                    <input type="number" class="form-control" id="detailsDone" name="detailsDone" required />
                    <span class="text-danger mt-2">Uwaga! Przed zatwierdzeniem sprawdź ponownie liczbę detali, ponieważ tej operacji nie można cofnąć!</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Zapisz</button>
            </div>
        </form>
    </div>
</div>
<script>
    const doneWorkTimeModal = document.getElementById('doneWorkTimeModal')
    doneWorkTimeModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const workTimingId = button.getAttribute('data-bs-workTimingId')
    const detailName = button.getAttribute('data-bs-detailName')
    const maxDetails = button.getAttribute('data-bs-maxDetails')
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    const modalTitle = doneWorkTimeModal.querySelector('.modal-title')
    const modalBodyInput = doneWorkTimeModal.querySelector('.modal-body input#workTimingId')
    $("#detailsDone").attr("max", maxDetails);

    modalTitle.textContent = `Detal: ${detailName}`
    modalBodyInput.value = workTimingId;
    })

</script>