<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $appName }} - {{ $Order->orderId }}</title>
    <style>
        @page { margin: 0px; }
        html{
            font-family: DejaVu Sans !important;
        }
        body {
            text-align: center;
            font-family: DejaVu Sans !important;
            font-size: 12px;
            margin: 0px; 
        }
        #appName{
            margin: 0;
            padding-top: 10px; 
            padding-bottom: 10px;
        }
        #barCodeHolder{
            margin-left: auto; 
            margin-right: auto;
            margin-bottom: 10px;
        }
        #barCodeHolder > span{
            font-size: 12px;
        }
        #barCodeHolder > div{
            margin-left: auto;
            margin-right: auto;
            height: 30px!important;
        }
        #barCodeHolder > div > div{
            height: 30px!important;
        }
        #name{
            font-weight: bold;
            font-size: 14px;
        }
        #amount{
            font-weight: bold;
            font-size: 14px;
        }
        #shortTable{
            width: 100%;
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        #shortTable td{
            width: 50%;
        }
        #tech{
            margin-top: 5px;
            margin-left: 5px;
            margin-bottom: 0;
        }
        #ul{
            text-align: left;
            font-size: 12px;
        }
        *{
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    @foreach($CompleteDetails as $CompleteDetail)
        @php
            $Detail = $CompleteDetail["Detail"];
            $printedInThisRound = 0;
        @endphp
        @if($Detail->orderDetailItemsDone != 0)
            <div style="border: 2px solid #fff; box-sizing: border-box; width: {{ $pageWidth }}cm; height: {{ $pageHeight }}cm;">
                <h5 id="appName">{{ $Detail->orderDetailName }}</h5>
                <div id="barCodeHolder">
                    @php
                        echo(html_entity_decode($CompleteDetail['EanEightIdHtml'], ENT_QUOTES, "UTF-8"));
                    @endphp    
                    <span>{{ $CompleteDetail['EanEightId'] }}</span>
                </div>
                <div id="amount">
                    {{ $CompleteDetail['LP'] }} / {{ $Detail->orderDetailItemsDone }} szt
                </div>
            </div>
            @php
                $printedInThisRound++;
            @endphp
        @endif
        @if($CompleteDetail['LP'] == $Detail->orderDetailItemsTotal && $printedInThisRound != 0)
            <div style="border: 2px solid #fff; box-sizing: border-box; width: {{ $pageWidth }}cm; height: {{ $pageHeight }}cm;">
                <div id="barCodeHolder" style="margin-top: 10px;">
                    Etykieta zamykająca serię
                    <h5 id="appName">{{ $Detail->orderDetailName }}</h5>
                </div>
                <div id="amount">
                    Łącznie {{ $Detail->orderDetailItemsTotal }} szt
                </div>
                <h5 id="appName">Wykonano w przy pomocy systemu {{ $appName }}</h5>
            </div>
        @endif
    @endforeach
</body>
</html>