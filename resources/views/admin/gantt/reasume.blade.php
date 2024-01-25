<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gantt Diagram - {{ env("APP_NAME") }} Reasume</title>
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style type="text/css">
        html, body{
            height:100%;
            padding:0px;
            margin:0px;
        }
        #ganttHeader{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 54px;
        }
        #ganttBody{
            position: absolute;
            top: 54px;
            left: 0;
            width: 100%;
            height: calc(100% - 54px);
        }
    </style>
</head>
<body>
    <div id="ganttHeader">
        <div style=" float: left; width: 360px; padding: 0;">
            <div class="btn-toolbar pt-2 px-2" role="toolbar" aria-label="Toolbar month selector">
                <div class="btn-group mr-2 text-center" style="margin-left: auto; margin-right: auto;" role="group" aria-label="Group">
                    <a href="{{ route("gantt-reasume", ["date" => $DateComplex['previousMonth']]) }}"><button type="button" class="btn px-3" style=" font-weight: bold;">←</button></a>
                    <button type="button" class="btn py-0 m-0" style="font-size: 24px; cursor: pointer; font-weight: bold;">{{ $DateComplex["monthMnemonic"] }}</button>
                    <a href="{{ route("gantt-reasume", ["date" => $DateComplex['nextMonth']]) }}"><button type="button" class="btn px-3" style=" font-weight: bold;">→</button></a>
                </div>
            </div>
        </div>
        <div style="float: left;">
            <div style="float: left; border-left: 1px solid #ccc; height: 60px; overflow: hidden; padding-left: 5px; padding-right: 5px;">
                <span style="font-size: 14px;">Typ obróbki (liczba stanowisk)</span><br />
                <b style="font-size: 14px;">Moc przerobowa <sup>(moc dzienna)</sup></b>
            </div>
            @foreach($Roles as $Role)
                <div style="float: left; border-left: 1px solid #ccc; height: 60px; overflow: hidden; padding-left: 5px; padding-right: 5px;">
                    <span style="font-size: 14px;">{{ $Role->roleProcess }} ({{ $Role->roleStations }} st.)</span><br />
                    <b style="font-size: 14px;">{{ $Role->currentMonthProcessingCapacity }} <sub>rgodz.</sub></b><sup style="opacity: 0.6;">({{ $Role->dailyProcessingCapacity }})</sup> 
                </div>
            @endforeach
        </div>
    </div>
    <div id="ganttBody"></div>
    <script type="text/javascript">
        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
        gantt.config.round_dnd_dates = false;
        gantt.config.duration_unit = 'hour';
        gantt.init("ganttBody");
        gantt.load("/admin{{ env('APP_ADMIN_POSTFIX') }}/gantt-reasume/data/{{$DateComplex['year']}}-{{$DateComplex['month']}}");

        /* Collapse all tasks */
        gantt.attachEvent("onTaskLoading", function(task){
            task.$open = false;
            return true;
        });
    </script>
</body>
</html>