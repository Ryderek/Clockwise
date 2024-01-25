@extends('adminlte::page')

@section('title', 'Powiadomienia')

@section('content_header')
    <h1> Powiadomienia </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    <table class="table table-hover table-noborder bg-white">
        <tr class="bg-gradient">
            <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
            <th class="bg-primary">Zgłaszający</th>
            <th class="bg-primary">Treść</th>
            <th class="bg-primary" style="border-top-right-radius: 0.25rem;">Data zgłoszenia</th>
        </tr>
        @if(!isset($notifications ) || $notifications  == null || count($notifications) == 0)
            <tr>
                <td colspan="100%" class="text-center">
                    <span>Brak powiadomień na ten moment</span>
                </td>
            </tr>
        @else
            @foreach($notifications as $notifis)
                <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#dismissNotificationModal" data-bs-dismissId="{{ $notifis->notificationId }}"  data-bs-claimant="{{ $notifis->name }}" data-bs-date="{{ $notifis->created_at }}" data-bs-content="{{ html_entity_decode($notifis->notificationContent, ENT_QUOTES, "UTF-8") }}">
                    <td>{{ $notifis->notificationId }}</td>
                    <td>{{ $notifis->name }}</td>
                    <td>{{ $notifis->notificationContent }}</td>
                    <td>{{ $notifis->created_at }}</td>
                </tr>
            @endforeach
        @endif
    </table>
    @include("layouts.partials.pagination")
    @include("admin.partials.dismiss-notification")
@stop

@section('css')

@stop

@section('js')
    <script>
       
    </script>
@stop




