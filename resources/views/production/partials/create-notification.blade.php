<div class="modal fade" id="createNotificationModal" tabindex="-1" aria-labelledby="createNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('notification-create') }}">
            @csrf
            <input type="hidden" class="form-control" style="border: none; background: rgba(0,0,0,0);" name="notificationSenderId" value="{{ $user->id }}" id="notificationSenderId" readonly />
            <input type="hidden" class="form-control" style="border: none; background: rgba(0,0,0,0);" name="authCardId" value="{{ $authCardId }}" id="authCardId" readonly />
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createNotificationModalLabel">Zgłaszanie zapotrzebowania</h1>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="notificationContent" class="col-form-label">Wiadomość:</label>
                    <input type="text" class="form-control" id="notificationContent" name="notificationContent" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Anuluj</button>
                <button type="submit" class="btn btn-primary">Wyślij</button>
            </div>
        </form>
    </div>
</div>
<script>
    const createNotificationModal = document.getElementById('createNotificationModal')
        createNotificationModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget

        const claimant = button.getAttribute('data-bs-claimant')
        const content = button.getAttribute('data-bs-content')
        const createId = button.getAttribute('data-bs-createId')

        const claimantBox = createNotificationModal.querySelector('#claimant')
        const contentBox = createNotificationModal.querySelector('#message-text')
        const createBox = createNotificationModal.querySelector('#createNotificationId')

        claimantBox.value = claimant;
        contentBox.value = content;
        createBox.value = createId;
    })

</script>