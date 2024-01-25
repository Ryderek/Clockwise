@extends('layouts.production')

@section('title', 'Produkcja')

@section('css')
    <style>
        html,
        body,
        #app,
        #app > main{
            height: 100%
        }
        body {
            background: #eee;
        }

        #PINform input:focus,
        #PINform select:focus,
        #PINform textarea:focus,
        #PINform button:focus {
            outline: none;
        }
        #PINform {
            background: #ededed;
            position: absolute;
            width: 350px; 
            height: 410px;
            left: 50%;
            margin-left: -180px;
            top: 50%;
            margin-top: -215px;
            padding: 30px;
            -webkit-box-shadow: 0px 0px 5px -0px rgba(0,0,0,0.3);
                -moz-box-shadow: 0px 0px 5px -0px rgba(0,0,0,0.3);
                    box-shadow: 0px 0px 5px -0px rgba(0,0,0,0.3);
        }
        #PINbox {
            background: #ededed;
            margin: 3.5%;
            width: 92%;
            font-size: 2em;
            text-align: center;
            border: 1px solid #d5d5d5;
        }
        .PINbutton {
            background: #ededed;
            color: #7e7e7e;
            border: none;
            /*background: linear-gradient(to bottom, #fafafa, #eaeaea);
            -webkit-box-shadow: 0px 2px 2px -0px rgba(0,0,0,0.3);
                -moz-box-shadow: 0px 2px 2px -0px rgba(0,0,0,0.3);
                    box-shadow: 0px 2px 2px -0px rgba(0,0,0,0.3);*/
            border-radius: 50%;
            font-size: 1.5em;
            text-align: center;
            width: 60px;
            height: 60px;
            margin: 4px 18px;
            padding: 0;
        }
        .clear, .enter {
            font-size: 1em;
        }
        .PINbutton:hover {
            box-shadow: #506CE8 0 0 1px 1px;
        }
        .PINbutton:active {
            background: #506CE8;
            color: #fff;
        }
        .clear:hover {
            box-shadow: #ff3c41 0 0 1px 1px;
        }
        .clear:active {
            background: #ff3c41;
            color: #fff;
        }
        .enter:hover {
            box-shadow: #47cf73 0 0 1px 1px;
        }
        .enter:active {
            background: #47cf73;
            color: #fff;
        }
        .shadow{
            -webkit-box-shadow: 0px 5px 5px -0px rgba(0,0,0,0.3);
                -moz-box-shadow: 0px 5px 5px -0px rgba(0,0,0,0.3);
                    box-shadow: 0px 5px 5px -0px rgba(0,0,0,0.3);
        }
    </style>
@stop

@section('js')
    <script>
        
        function addNumber(e){
            //document.getElementById('PINbox').value = document.getElementById('PINbox').value+element.value;
            var v = $( "#PINbox" ).val();
            $( "#PINbox" ).val( v + e );
        }
        function clearForm(e){
            //document.getElementById('PINbox').value = "";
            $( "#PINbox" ).val( "" );
        }
        function submitForm() {
            return true;
            /*
            if (e.value == "") {
                alert("Enter a PIN");
            } else {
                alert( "Your PIN has been sent! - " + e.value );
                data = {
                    pin: e.value
                }
                apiCall( data, function( r ) {
                    $( "#logo" ).attr( "src", r.site_logo );
                    $( ".title-msg" ).text( r.site_msg );
                    accent = r.accent;
                    $( ".accent-bg" ).css( "background-color", accent );
                });
                
                //document.getElementById('PINbox').value = "";
                $( "#PINbox" ).val( "" );
            };
            
            */
        };
        function apiCall( post, callback ) {	
            $.ajax({
                type: "POST",
                contentType: "application/json",
                url: "admin/api.php",
                data: JSON.stringify( post ),
                dataType: "json",
                success: function ( r ) {
                    callback( r );
                },
                error: function ( response ) {
                    console.log( response )
                },
            });
        }
    </script>
@stop

@section('content')
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div style="background:red">
            <form action='{{ route('time-tracking.post') }}' method='POST' name='PINform' id='PINform' autocomplete='off' draggable='true'> @csrf <input id='PINboxFaker' type='password' style="display: none;" value='' name='passwordFakeBox' /><input id='PINbox' type='password' value='' name='authCodeId' autofocus required minlength="6" maxlength="127" /><br/><?php if(isset($_GET['error'])){ echo("<div class='w-100 text-center text-danger'>".htmlspecialchars_decode($_GET['error'])."</div>");} ?><input type='button' class='PINbutton' name='1' value='1' id='1' onclick='addNumber(1);' /><input type='button' class='PINbutton' name='2' value='2' id='2' onclick='addNumber(2);' /><input type='button' class='PINbutton' name='3' value='3' id='3' onclick='addNumber(3);' /><br><input type='button' class='PINbutton' name='4' value='4' id='4' onclick='addNumber(4);' /><input type='button' class='PINbutton' name='5' value='5' id='5' onclick='addNumber(5);' /><input type='button' class='PINbutton' name='6' value='6' id='6' onclick='addNumber(6);' /><br><input type='button' class='PINbutton' name='7' value='7' id='7' onclick='addNumber(7);' /><input type='button' class='PINbutton' name='8' value='8' id='8' onclick='addNumber(8);' /><input type='button' class='PINbutton' name='9' value='9' id='9' onclick='addNumber(9);' /><br><input type='button' class='PINbutton clear' name='-' value='clear' id='-' onclick="clearForm();" /><input type='button' class='PINbutton' name='0' value='0' id='0' onclick='addNumber(0);' /><input type='submit' class='PINbutton enter' name='+' value='enter' id='+' onclick='submitForm();' /></form>
        </div>
    </div>
    
@stop
<?php

/*
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

*/ 
?>