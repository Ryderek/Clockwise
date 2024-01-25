<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Clockwise</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="/vendor/jquery/jquery.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- Scripts -->
    @yield('css')
    @yield('js')

    
    <style>
        /* Custom colors made for customer */
        .bg-primary{
            background: rgb(0,0,100);
            background: linear-gradient(159deg, rgba(0,0,100,1) 0%, rgba(90,16,115,1) 90%);
        }
        .btn-primary{
            background: rgb(0,0,100);
            --bs-btn-border-color: rgb(0,0,100);
        }
        table.bg-primary,
        td.bg-primary,
        th.bg-primary{
            background: rgb(0,0,100)!important;
        }
        .page-item.active .page-link {
            background-color: rgb(0,0,100);
            border-color: rgb(0,0,100);
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active, .sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: rgb(0,0,100);
            color: #fff;
        }
        a{
            color: rgba(90,16,115,1);
            text-decoration: none;
            background-color: transparent;
        }
    </style>
</head>
<body>

    <div id="app">
        <main class="py-4">
             {{-- Error wrapper --}}
            @hasSection('error_content')
                <div class="content pt-3 px-3">
                    <div class="alert alert-danger mb-0" role="alert">
                        Podczas przetwarzania żądania wystąpił błąd.<br />
                        <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            Zobacz szczegóły
                        </a>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body">
                                <pre>@yield('error_content')</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Success wrapper --}}
            @hasSection('success_content')
                <div style="position: fixed; bottom: 10px; right: 10px; width: max-content; z-index: 999;">
                    <div id="successToast" class="toast align-items-center text-bg-primary border-0" role="alert" style="opacity: 1; background: #468b00!important; color: #fff;" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                @yield('success_content')
                            </div>
                        </div>
                    </div>
                    <script>
                        setTimeout(function(){
                            $("#successToast").fadeOut(400, function(){
                                $("#successToast").remove();
                            })
                        }, 3000)
                    </script>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
