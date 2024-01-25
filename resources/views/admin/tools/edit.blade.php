@extends('adminlte::page')

@section('title', 'Narzędzia')

@section('content_header')
    <h1> Edytowanie narzędzia </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    
<form action="{{ route('tool.update') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    <input style="display: none;" type="hidden" value="{{ $tool->toolId }}" readonly name="editToolId" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                    <h4 class="m-0">Szczegóły narzędzia</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-8 col-xl-8">
                            <label for="toolName" class="form-label">Nazwa narzędzia</label>
                            <input type="text" class="form-control" id="toolName" name="toolName" value="{{ $tool->toolName }}" placeholder="Podaj nazwę narzędzia" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3 col-xl-2 mt-md-0 mt-3">
                            <label for="toolStatus" class="form-label">Status</label>
                            <select class="form-control" name="toolStatus" id="toolStatus">
                                @foreach ($toolStatuses as $status)
                                    <option value="{{ $status['slug'] }}" @if($status['slug'] == $tool['toolStatus']) selected @endif>{{ $status['translation'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')

@stop

@section('js')
@stop


