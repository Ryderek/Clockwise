<form method="POST" action="{{ route('employee.delete') }}" class="modal fade" id="removeEmployeeModal" tabindex="-1" aria-labelledby="removeEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeEmployeeModalLabel">Usuwanie pracownika</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="deleteEmployeeId" id="deleteEmployeeId" value="0" />
                <div class="mb-3">
                    Czy na pewno chcesz usunąć pracownika <b id="removeEmployeeName"></b>?<br />
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