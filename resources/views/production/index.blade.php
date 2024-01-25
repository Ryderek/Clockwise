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

        window.serverTime = {{ time() }};
        window.localTime = (new Date())/1000;
        window.timeSyncDifference = window.serverTime-(window.localTime-1);

        function timeSince(date) {

            var seconds = Math.floor((new Date() - date) / 1000);
                seconds = seconds + window.timeSyncDifference;

            sekund = Math.floor(seconds % 60);
            minut = Math.floor((seconds % 3600)/60);
            godzin = Math.floor((seconds % 86400)/3600)
            dni = Math.floor(seconds/86400)
            output = sekund+" sek";
            if (minut > 0 || godzin > 0) {
                output = minut+" min "+output;
            }
            if (godzin > 0) {
                output = godzin+" h "+output;
            }
            if (dni > 0) {
                output = dni+" dni "+output;
            }
            
            return output;
        }
        function calculateTimeProcess(tim, targ){
            difference = timeSince(tim*1000);
            targ.html(difference);
            setTimeout(function(){
                calculateTimeProcess(tim, targ);
            },  1000)
        }
    </script>
@stop

@section('content')
    <div style="height: 100%; width: calc(100% - 20px); margin-left: auto; margin-right: auto; max-width: 1800px;">
        <div class="row" style="height: 100%;">
            <div class="col-12 col-md-6" style="position: relative;">
                <form action='{{ route('production.post') }}' method='POST' name='PINform' id='PINform' autocomplete='off' draggable='true'> @csrf <input id='PINboxFaker' type='password' style="display: none;" value='' name='passwordFakeBox' /><input id='PINbox' type='password' value='' name='authCodeId' autofocus required minlength="6" maxlength="127" /><br/><?php if(isset($_GET['error'])){ echo("<div class='w-100 text-center text-danger'>".htmlspecialchars_decode($_GET['error'])."</div>");} ?><input type='button' class='PINbutton' name='1' value='1' id='1' onclick='addNumber(1);' /><input type='button' class='PINbutton' name='2' value='2' id='2' onclick='addNumber(2);' /><input type='button' class='PINbutton' name='3' value='3' id='3' onclick='addNumber(3);' /><br><input type='button' class='PINbutton' name='4' value='4' id='4' onclick='addNumber(4);' /><input type='button' class='PINbutton' name='5' value='5' id='5' onclick='addNumber(5);' /><input type='button' class='PINbutton' name='6' value='6' id='6' onclick='addNumber(6);' /><br><input type='button' class='PINbutton' name='7' value='7' id='7' onclick='addNumber(7);' /><input type='button' class='PINbutton' name='8' value='8' id='8' onclick='addNumber(8);' /><input type='button' class='PINbutton' name='9' value='9' id='9' onclick='addNumber(9);' /><br><input type='button' class='PINbutton clear' name='-' value='clear' id='-' onclick="clearForm();" /><input type='button' class='PINbutton' name='0' value='0' id='0' onclick='addNumber(0);' /><input type='submit' class='PINbutton enter' name='+' value='enter' id='+' onclick='submitForm();' /></form>
            </div>
            <div class="col-12 col-md-6 d-table">
                <div class="d-table-cell" style="vertical-align: middle;">
                    @if(count($detailsInProgress) != 0)
                        <div class="col-12 mb-0 py-2 pl-4">
                            <h4 class="m-0">Aktywne sesje pracownicze:</h4>
                        </div>
                        <div class="col-12 mb-5 p-0">
                            <table class="table table-hover table-noborder bg-white">
                                <tr class="bg-gradient">
                                    <th class="bg-primary text-white" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                                    <th class="bg-primary text-white">Detal</th>
                                    <th class="bg-primary text-white">Pracownik</th>
                                    <th class="bg-primary text-white">Typ obróbki</th>
                                    <th class="text-right bg-primary text-white" style="border-top-right-radius: 0.25rem;">Aktualny czas pracy</th>
                                </tr>
                                @if(!isset($detailsInProgress) || $detailsInProgress == null)
                                    <tr>
                                        <td colspan="100%" class="text-center">Brak aktualnie wykonywanych obróbek</td>
                                    </tr>
                                @else
                                    @foreach($detailsInProgress as $dIP)
                                        <tr>
                                            <td>{{ $dIP->workTimingId }}</td>
                                            <td>{{ $dIP->orderDetailName }}</td>
                                            <td>
                                                @php
                                                    $UserName = explode(" ", $dIP->name);
                                                    echo($UserName[0]." ".substr($UserName[1], 0, 1));
                                                @endphp
                                            </td>
                                            <td>{{ $dIP->roleProcess }}</td>
                                            <td id="workTiming{{$dIP->workTimingId}}Time" class="text-right">{{ $dIP->workTimingStart }}</td>
                                        </tr>
                                        <script> calculateTimeProcess({{ $dIP->workTimingStart }}, $("#workTiming{{$dIP->workTimingId}}Time")); </script>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
<?php
/*


@if(isset($estimatedWorkTimings) && $estimatedWorkTimings != null)
        @foreach($estimatedWorkTimings as $eWT)
            @foreach($eWT->realTimesArray as $rt)
                @if($rt->workTimingEnd == null)
                    <?php $total_rta += count($eWT->realTimesArray); ?>
                @endif
            @endforeach
        @endforeach
    @endif
   

*/
?>