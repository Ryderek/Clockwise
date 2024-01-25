<form method="POST" action="{{ route('role.delete') }}" class="modal fade" id="removeRoleModal" tabindex="-1" aria-labelledby="removeRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeRoleModalLabel">Usuwanie roli</h5>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="deleteRoleId" id="deleteRoleId" value="0" />
                <div class="mb-3">
                    Czy na pewno chcesz usunąć rolę <b id="removeRoleName"></b>?<br />
                    <span class="text-danger">Tej operacji nie można cofnąć.<br /><u>Ponowne użycie nazwy systemowej zostanie zablokowane!</u></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary">Usuń</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </div>
    </div>
</form>