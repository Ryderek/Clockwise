<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $appName }} - {{ $order->orderId }}</title>
    <style>
           @page { margin: 0px; }
        html{
            font-family: DejaVu Sans !important;
        }
        body {
            text-align: center;
            font-family: DejaVu Sans !important;
            margin: 0px; 
        }
        #appName{
            margin-top: 5px; 
            margin-bottom: 5px;
        }
        #barCodeHolder{
            margin-left: auto; 
            margin-right: auto;
            margin-top: 5px;
            margin-bottom: 0px;
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
        #painted{
            font-weight: 400;
            font-size: 14px;
        }
        #shortTable{
            width: 100%;
            text-align: center;
            font-size: 12px;
            margin-top: 5px;
            margin-bottom: 0px;
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
            margin-top: 0;
            padding-top: 0;
        }
        *{
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    @foreach($details as $detail)
        <div style="border: 2px solid #fff; box-sizing: border-box; width: {{ $pageWidth }}cm; height: {{ $pageHeight }}cm;">
            <table id="shortTable">
                <tr>
                    <td>Z#{{ $order->orderId }} / D#{{ $detail->orderDetailOrderNumber }}</td>   
                    <td>DL: {{ substr($order->orderDeadline, 0, 10) }}</td>   
                </tr>
            </table>
            <div id="name">
                {{ $detail->orderDetailName }} ({{ $detail->orderDetailItemsTotal }} szt)
                @if($detail->orderDetailCooperation == 1)
                    <br /><sup style="font-weight: 400; line-height: 0; font-size: 10px;" >(kooperowany)</sup>
                @endif
            </div>
            <div id="barCodeHolder">
                @php
                    echo(html_entity_decode($detail->barcodeImg, ENT_QUOTES, "UTF-8"));
                @endphp    
                <span>{{ $detail->orderDetailUniqueId }}</span>
            </div>
            @if(strlen($detail->orderDetailPainting) != 0)
                <div id="painted">
                    Lakier: {{ $detail->orderDetailPainting }}
                </div>
            @endif
            <h5 id="tech">Technologia obróbki:</h5>
            <ol id="ul">
                @if(isset($detail->eWT))
                    @foreach($detail->eWT as $estwoti)
                        <li>
                            {{--mb_convert_encoding($estwoti->roleProcess, 'HTML-ENTITIES', 'UTF-8') --}}
                            {{ $estwoti->roleProcess }} 
                        </li>
                    @endforeach
                @endif
            </ol>
        </div>
    @endforeach
</body>
</html>