@php
    function forceLatin($str){

        return $str;
    }
@endphp
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WZ do zamówienia</title>
    <style>
        @page { margin: 0px; }
        html{
            font-family: DejaVu Sans !important;
        }
        body { 
            margin: 20px; 
            text-align: center;
            font-family: DejaVu Sans !important;
            font-size: 12px;
        }
        *{
            font-family: DejaVu Sans !important;
        }
        table{
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            max-width: 1200px;
        }
        .table{
            border-spacing: 0;
            border: 1px solid #000;
        }
        .table td,
        .table th{
            border: 1px solid #000;
            text-align: center;
            padding: 4px;
        }
        .table th{
            font-weight: 400;
            background: #dbdbdb;
        }
        .hint{
            vertical-align: top;
            font-size: 12px;
            text-align: left;
        }
        
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="text-align: center; width: 100px;">
                <img src="{{ asset('/build/images/icon.png') }}" alt="Logo" style="width: 64px;" />
            </td>
            <td style="text-align: center;">
                Clockwise sp. z o.o.<br />
                ul. Przykładowa 12, 12-345 Warszawa<br />
                NIP: 123456789<br />
                ZAKŁAD PRODUKCYJNY:<br />
                ul. Przykładowa 98, 12-345 Warszawa <br />
                               
            </td>
            <td style="text-align: center;">
                {{ $customer->customerName }}<br />
                {{ $customer->customerAddress }}<br />
                {{ $customer->customerPostal }} {{ $customer->customerCity }}<br />
                {{ $customer->customerTaxIdentityNumber }}
            </td>
        </tr>
    </table>
    <h1>WYDANIE ZEWNĘTRZNE</h1>
    <h3>z dn. <?php echo(date("d.m.Y")); ?>r.</h3>
    <table class="table">
        <tr>
            <th>LP.</th>
            <th>NAZWA [ID]</th>
            <th>ILOŚĆ SZTUK</th>
            <th style="border-bottom: 2px solid #000;">UWAGI</th>
        </tr>
        <?php $lp = 0; ?>
        @foreach($details as $detail)
            <?php $lp++ ?>
            <tr>
                <td>{{ $lp }}</td>
                <td>{{ forceLatin($detail->orderDetailName); }}</td>
                <td>{{ $detail->orderDetailItemsDone }}</td>
            </tr>
        @endforeach
    </table>
    <table>
        <tr>
            <td style="text-align: center;">
                <h4 style="margin-top: 40px;">Wystawił</h4>
            </td>
            <td style="text-align: center;">
                <h4 style="margin-top: 40px;">Odebrał</h4>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <hr style="max-width: 200px; margin-top: 30px;" />
                (data, podpis)
            </td>
            <td style="text-align: center;">
                <hr style="max-width: 200px; margin-top: 30px;" />
                (data, podpis)
            </td>
        </tr>
    </table>
    <table class="table" style="margin-top: 20px;">
        <tr>
            <th style="width: 300px; font-weight: bold;">OPAKOWANIE</th>
            <th style="font-weight: bold;">ILOŚĆ</th>
        </tr>
        <tr>
            <td style="width: 300px; font-weight: bold;">OBSTAWKA</td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 300px; font-weight: bold;">PALETA</td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 300px; font-weight: bold;">INNY RODZAJ</td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 300px; font-weight: bold;">TRANSPORT</td>
            <td style="text-align: center;  font-weight: bold;">TAK / NIE</td>
        </tr>
    </table>
    <span style="padding-top: 10px; display: block; text-align: center;">W przypadku niezwrócenia opakowania zwrotnego w terminie 90 dni, sprzedawca wystawi fakturę za wydane opakowanie</span>
</body>
</html>