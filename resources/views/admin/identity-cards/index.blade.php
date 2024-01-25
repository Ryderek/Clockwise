@extends('adminlte::page')

@section('title', 'Karty dostępowe')

@section('content_header')
    <h1>Karty dostępowe</h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    <!-- Button trigger modal -->
    <button type="button" id="userModalTrigger" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal">
        Dodaj nową kartę
    </button>
  
  
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Dodawanie nowej karty</h5>
            </div>
            <div class="modal-body">
                <form class="row" method="POST" action="{{ route('identity-card.create') }}">
                    <div class="col-12" id="identityCardField">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        @csrf
                        <label for="authCardUserId" class="form-label identityCardFirstStep" aria-describedby="userHelp">Imię i nazwisko</label>
                        <select class="form-select px-3 py-2 border identityCardFirstStep rounded w-100" style="background-color: #fff;" aria-label="" id="authCardUserId" name="authCardUserId" required>
                            <option value="">Wybierz użytkownika z listy</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <div id="userHelp" class="form-text mb-3 identityCardFirstStep">Podaj imie i nazwisko osoby, do której ma zostać przypisana karta</div>
                        <hr style="opacity: 0;" />
                        <span class="identityCardSecondStep form-label" style="display: block; margin-top: 20px;"> Zbliż kartę do czytnika, aby zatwierdzić.</span>
                        <input type="button" id="unhideInputButton" style="display: none;" onclick="replaceInputWithMe(this, 'authCardCode')" class="identityCardSecondStep" />
                        <input type="text" id="authCardCode" name="authCardCode" class="identityCardSecondStep mt-3" placeholder="... lub wprowadź kod ręcznie" required />
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary d-none">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <table class="table table-hover table-striped">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary">Przypisano do</th>
                <th scope="col" class="bg-primary">Ostatnio użyta</th>
                <th scope="col" class="bg-primary">Numer karty</th>
                <th scope="col" class="bg-primary">Utworzono</th>
                <th scope="col" class="bg-primary">Zaktualizowano</th>
                <th scope="col" class="bg-primary text-right" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($joints as $joint)
                <tr>
                    <th scope="row">{{ $joint->authCardId }}</th>
                    <td>{{ $joint->name }}</td>
                    <td>{{ $joint->authCardLastUsed }}</td>
                    <td>{{ $joint->authCardCode }}</td>
                    <td>{{ $joint->created_at }}</td>
                    <td>{{ $joint->updated_at }}</td>
                    <td class="text-right">
                        <a style="color: inherit;" title="Usuń kartę" data-bs-toggle="modal" data-bs-target="#removeIdentityCardModal" data-bs-deleteIdentityCardId="{{ $joint->authCardId }}" data-bs-removeIdentityCardName="{{ $joint->name }}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
@include('admin.identity-cards.partials.delete-modal')

@section('css')
    <style>
        #identityCardField{
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }
        .identityCardSecondStep{
            display: none;
        }
        #authCardCode{
            opacity: 0;
        }
        .swipe-card-animation{
            /*
            background-image: url('/build/images/swipe-card.gif');
            background-position: center bottom;
            background-repeat: no-repeat;
            background-size: 100px;
            */
            text-align: center;
        }
    </style>
    <style>
        #unhideInputButton{
            display: block; 
            margin-left: auto; 
            margin-right: auto; 
            width: 40px; 
            height: 40px;  
            margin-top: 20px;
            background-color: rgba(0,0,0,0); 
            background-image: url('/build/images/displayInputButton.png'); 
            background-size: 40px 40px; 
            background-repeat: no-repeat; 
            background-position: center center;  
            border: none;
        }
    </style>
    <script>
        function replaceInputWithMe(whi, inp){
            $(whi).fadeOut(350, function (){
                $("#"+inp).css("transition", "0.3");
                setTimeout(() => {
                    $("#"+inp).css("opacity", "1");
                    $("#"+inp).fadeIn(300, function(){
                        $("#"+inp).focus();
                    });
                }, 50);
            });
        }
    </script>
@stop

@section('js')
    <?php /* <script src="/build/assets/identity-cards.js"></script> */ ?>
    <script>
$( document ).ready(function() { 
    
    function identityCardNextClick(){
        identityCardFieldHeight = $("#identityCardField").css("height");
        $("#identityCardField").css("height", identityCardFieldHeight);
        $('.identityCardFirstStep').fadeOut(350, function(){
            $('.identityCardSecondStep').fadeIn();
        });
        $("#identityCardField").css("opacity", "0");
        setTimeout(function(){
            $("#authCardCode").val("");
            $("#authCardCode").focus();
            $("#identityCardField").addClass("swipe-card-animation");
            $("#identityCardField").css("opacity", "1");
        }, 400);
    }
    function identityCardPrevClick(){
        $('.identityCardSecondStep').fadeOut(350, function(){
            $('.identityCardFirstStep').fadeIn();
            $("#authCardUserId").val($("#authCardUserId option:first").val());
        });
        setTimeout(function(){
            $("#identityCardField").removeClass("swipe-card-animation");
            $("#identityCardField").css("opacity", "1");
        }, 400);
    }
    
    // Listeners

    var identityCardModal = document.getElementById('userModal')
    identityCardModal.addEventListener('shown.bs.modal', function () {
        document.getElementById('authCardUserId').value = "";
        document.getElementById('authCardUserId').focus()
    })

    document.getElementById("authCardUserId").addEventListener('change', function () {
        identityCardNextClick();
    })

    document.getElementById("userModalTrigger").addEventListener('click', function () {
        identityCardPrevClick();
    })
    
    $("#authCardCode").val("");
});

var removeIdentityCardModal = document.getElementById('removeIdentityCardModal')
        removeIdentityCardModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var deleteIdentityCardId = button.getAttribute('data-bs-deleteIdentityCardId')
            var removeIdentityCardName = button.getAttribute('data-bs-removeIdentityCardName')
            var removeIdentityCardSpan = removeIdentityCardModal.querySelector('#removeIdentityCardName')
            var removeIdentityCardIdInput = removeIdentityCardModal.querySelector('#deleteIdentityCardId')

            removeIdentityCardSpan.textContent = removeIdentityCardName
            removeIdentityCardIdInput.value = deleteIdentityCardId
        })
    </script>
@stop