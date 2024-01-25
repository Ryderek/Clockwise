@php 
    $currentDate = date("Y-m-d");
@endphp

@extends('adminlte::page')

@section('title', 'Edytowanie notatki')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')
    
<form action="{{ route('note.update') }}" method="POST" autocomplete="off">
    <div class="row px-2 py-2">
        <div class="col-12 pl-0 pr-0">
            <input type="text" class="rounded px-3 py-2" name="noteTitle" style="width: calc(100% - 256px); border: 1px solid #ccc; transform: translateY(2px);" value="{{ $noteTitle }}" required />
            <input type="submit" style="width: 124px;" class="btn btn-primary py-2" value="Aktualizuj" />
            <a href="{{ $noteBackButton }}"><input type="button" style="width: 124px;" class="btn btn-dark py-2" value="Powrót" /></a>
        </div>
    </div>
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    <input style="display: none;" type="text" name="noteId" value="{{ $noteId }}" />
    @csrf
    <textarea id="tinyMCEBox" name="noteContent" required>{{ $noteContent }}</textarea>
    <div class="text-right text-muted mt-1">
        Utworzono: {{ $noteCreated }}, ostatnia aktualizacja: {{ $noteUpdated }}
    </div>
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


