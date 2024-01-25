@php 
    $currentDate = date("Y-m-d");
@endphp

@extends('adminlte::page')

@section('title', 'Tworzenie notatki')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    
<form action="{{ route('note.create-new') }}" method="POST" autocomplete="off">
    <div class="row px-2 py-2">
        <div class="col-12 pl-0 pr-0">
            <input type="text" class="rounded px-3 py-2" name="noteTitle" style="width: calc(100% - 128px); border: 1px solid #ccc; transform: translateY(2px);" value="Notatka z dnia {{ $currentDate }} - {{ $relatorName }} o numerze {{ $relatorId }}" required />
            <input type="submit" style="width: 124px;" class="btn btn-primary py-2" value="Zapisz notatkę" />
        </div>
    </div>
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    <input style="display: none;" type="hidden" value="{{ $relatorSlug }}" name="noteRelatorSlug" required readonly />
    <input style="display: none;" type="hidden" value="{{ $relatorName }}" name="noteRelatorName" required readonly />
    <input style="display: none;" type="hidden" value="{{ $relatorId }}" name="noteRelatorId" required readonly />
    @csrf
    <textarea id="tinyMCEBox" name="noteContent" required>Wpisz tutaj treść notatki...</textarea>
</form>
@stop

@section('css')
    <style>
        .tox-tinymce{
            min-height: 400px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.tiny.cloud/1/{{ $tinyMCEKey }}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
          selector: '#tinyMCEBox'
        });
    </script>
@stop


