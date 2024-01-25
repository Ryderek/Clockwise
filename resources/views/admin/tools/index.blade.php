@extends('adminlte::page')

@section('title') {{$pageHeader}} @stop

@section('content_header')
    <h1>{{ $pageHeader }}</h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')
    <table class="table table-hover table-noborder bg-white">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary">Narzędzie</th>
                <th scope="col" class="bg-primary">Status</th>
                <th scope="col" class="bg-primary">Opis</th>
                <th scope="col" class="bg-primary">W obiegu od</th>
                <th scope="col" class="bg-primary">Ostatnia aktualizacja</th>
                <th scope="col" class="text-end bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tools as $tool)
                <tr class="{{ $tool->toolClasses }}">
                    <th scope="row">{{ $tool->toolId }}</th>
                    <td>{{ $tool->toolName }}</td>
                    <td>{{ $tool->toolStatusMnemonic }}</td>
                    <td>
                        @if($tool->toolStatus == "damaged") 
                            Uszkodzony od {{ abs($tool->toolLastRepaired) }} dni 
                        @elseif($tool->toolStatus == "workbench") 
                            W naprawie od {{ abs($tool->toolLastRepaired) }} dni
                        @elseif($tool->toolStatus == "available") 
                            Dni od ostatniej naprawy: {{ abs($tool->toolLastRepaired) }}
                        @endif
                    </td>
                    <td>{{ $tool->created_at }}</td>
                    <td>{{ substr($tool->updated_at, 0, 10) }}</td>
                    <td class="text-right">
                        <a style="color: inherit;" title="Usuń wybrane narzędzie" data-bs-toggle="modal" data-bs-target="#removeToolModal" data-bs-deleteToolId="{{ $tool->toolId }}" data-bs-removeToolName="{{ $tool->toolName }}">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a style="color: inherit;" title="Edytuj wybrane narzędzie" href="{{ route('tool.edit', ["id" => $tool->toolId ]) }}"> &nbsp; &nbsp; 
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('layouts.partials.pagination')
    @include('admin.tools.partials.delete-modal')
@stop

@section('css')
    <style>
        .table-noborder{
            border-radius: 0.25rem;
        }
        .table-noborder th,
        .table-noborder td{
            border: none!important;
        }
    </style>
@stop

@section('js')
    <script>
        var removeToolModal = document.getElementById('removeToolModal')
        removeToolModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var deleteToolId = button.getAttribute('data-bs-deleteToolId')
            var removeToolName = button.getAttribute('data-bs-removeToolName')
            var removeToolSpan = removeToolModal.querySelector('#removeToolName')
            var removeToolIdInput = removeToolModal.querySelector('#deleteToolId')

            removeToolSpan.textContent = removeToolName
            removeToolIdInput.value = deleteToolId
        })

    </script>
@stop