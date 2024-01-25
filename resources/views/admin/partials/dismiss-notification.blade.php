<div class="modal fade" id="dismissNotificationModal" tabindex="-1" aria-labelledby="dismissNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('notification-dismiss') }}">
            @csrf
            <input type="hidden" name="dismissNotificationId" id="dismissNotificationId" style="opacity: 0;" />
            <div class="modal-header">
                <h3 class="modal-title fs-5 h5" id="dismissNotificationModalLabel">Powiadomienie</h3>
            </div>
            <div class="modal-body">
                <table class="table" id="notificationTable">
                    <tr>
                        <td class="mb-1">
                            <label for="claimant" class="col-form-label">Zgłaszający:</label>
                            <input type="text" class="form-control" style="border: none; background: rgba(0,0,0,0);" id="claimant" readonly />
                        </td>
                        <td class="mb-1">
                            <label for="claimdate" class="col-form-label">Data:</label>
                            <input type="text" class="form-control" style="border: none; background: rgba(0,0,0,0);" id="claimdate" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td class="mb-2" colspan="2">
                            <label for="message-text" class="col-form-label">Wiadomość:</label>
                            <input type="text" class="form-control" id="message-text" style="border: none; background: rgba(0,0,0,0);" readonly />
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary" >Zatwierdź</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </form>
    </div>
</div>
<style>
    #notificationTable{
        border: none;
    }
    #notificationTable td{
        padding: 0;
        border: none;
    }
    #notificationTable input{
        padding: 0;
    }
</style>
<script>
    const dismissNotificationModal = document.getElementById('dismissNotificationModal')
        dismissNotificationModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget

        const claimant = button.getAttribute('data-bs-claimant');
        const content = button.getAttribute('data-bs-content');
        const dateclaim = button.getAttribute('data-bs-date');
        const dismissId = button.getAttribute('data-bs-dismissId');

        const claimantBox = dismissNotificationModal.querySelector('#claimant')
        const contentBox = dismissNotificationModal.querySelector('#message-text')
        const dismissBox = dismissNotificationModal.querySelector('#dismissNotificationId')
        const claimdate = dismissNotificationModal.querySelector('#claimdate')

        claimantBox.value = claimant;
        contentBox.value = content;
        dismissBox.value = dismissId;
        claimdate.value = dateclaim;
    })

</script>