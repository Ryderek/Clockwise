<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gantt Diagram - {{ env("APP_NAME") }}</title>
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">
    <style type="text/css">
        html, body{
            height:100%;
            padding:0px;
            margin:0px;
            overflow: hidden;
        }
        #ganttBody{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="ganttBody"></div>
    <script type="text/javascript">
        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
        gantt.init("ganttBody");
        gantt.load("/admin{{ env('APP_ADMIN_POSTFIX') }}/gantt/data/{{$id}}");
    </script>
</body>
</html>