<form class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true" method="POST" action="{{ route('role.assign') }}">
    @csrf
    <input type="hidden" name="userRoleUserId" value="{{ $user->id }}" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="addRoleModalLabel">Przypisywanie roli dla {{ $user->name }}</h5>
            </div>
            <div class="modal-body">
                <label for="userRoleRoleId" class="form-label identityCardFirstStep" aria-describedby="userHelp">Rola</label>
                <select class="form-select px-3 py-2 border identityCardFirstStep rounded w-100" style="background-color: #fff;" aria-label="" id="userRoleRoleId" name="userRoleRoleId" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->roleId }}">{{ $role->roleName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Przypisz rolę</button>
            </div>
        </div>
    </div>
</form>
  