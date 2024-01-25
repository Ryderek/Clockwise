<form method="POST" action="{{ route('worktiming.define-estimated') }}" class="modal fade" id="addWorkTimingModal" tabindex="-1" aria-labelledby="addWorkTimingModalLabel" aria-hidden="true" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkTimingModalLabel">Dodawanie obróbki</h5>
            </div>
            <div class="modal-body">
                @csrf
                <div class="mb-3"> 
                    <input type="hidden" class="form-control" name="workTimingRelatorId" value="{{ $detail->orderDetailId }}" readonly required>
                    <input type="hidden" class="form-control" name="orderId" value="{{ $detail->orderDetailOrderId }}" readonly required>
                    <label for="workTimingRole" class="form-label">Wybierz typ obróbki</label>
                    <select name="workTimingRoleSlug" id="workTimingRole" style="backgorund: #fff;" class="form-select px-3 py-2 border identityCardFirstStep rounded w-100" aria-label="Wybierz typ obróbki" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->roleSlug }}">{{ $role->roleProcess }}</option>
                        @endforeach
                    </select>
                    {{-- <label for="workTimingEstimatedTime" class="form-label mt-3">Przewidywany czas obróbki pojedynczego detalu (minuty)</label>
                    <input type="number" class="form-control" id="workTimingEstimatedTime" name="workTimingEstimatedTime" placeholder="Podaj czas obróbki detalu" aria-describedby="detailNameHelp" required /> --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="sumbit" class="btn btn-primary">Dodaj obróbkę</button>
            </div>
        </div>
    </div>
</form>