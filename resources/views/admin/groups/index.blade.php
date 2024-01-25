@extends('adminlte::page')

@section('title', 'Pracownicy')

@section('content_header')
    <h1>Pracownicy</h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    <table class="table table-hover table-noborder bg-white">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary">Nazwa grupy</th>
                <th scope="col" class="bg-primary">Guard grupy</th>
                <th scope="col" class="bg-primary">Utworzono</th>
                <th scope="col" class="bg-primary">Zaktualizowano</th>
                <th scope="col" class="text-end bg-primary text-right" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $group)
                <tr>
                    <td scope="col" style="border-top-left-radius: 0.25rem;">{{ $group->id }}</td>
                    <td scope="col">{{ $group->name }}</td>
                    <td scope="col">{{ $group->guard_name }}</td>
                    <td scope="col">{{ $group->created_at }}</td>
                    <td scope="col">{{ $group->updated_at }}</td>
                    <td scope="col" class="text-right">
                        <a style="color: inherit;" title="Usuń wybrane narzędzie" data-bs-toggle="modal" data-bs-target="#removeGroupModal" data-bs-deleteGroupId="{{ $group->id }}" data-bs-removeGroupName="{{ $group->name }}">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a style="color: inherit; padding-left: 15px;" title="Edytuj wybranego pracownika" href="{{ route('group', ['id' => $group->id]) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('layouts.partials.pagination')
@stop

@include('admin.groups.partials.delete-group-modal')

@section('css')
    <style>
    </style>
@stop

@section('js')<script>
    var removeGroupModal = document.getElementById('removeGroupModal')
    removeGroupModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var deleteGroupId = button.getAttribute('data-bs-deleteGroupId')
        var removeGroupName = button.getAttribute('data-bs-removeGroupName')
        var removeGroupSpan = removeGroupModal.querySelector('#removeGroupName')
        var removeGroupIdInput = removeGroupModal.querySelector('#deleteGroupId')

        removeGroupSpan.textContent = removeGroupName
        removeGroupIdInput.value = deleteGroupId
    })

</script>
@stop