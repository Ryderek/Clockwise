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
    .dashboardButton{
        padding: 20 28px;
        font-size: 32px;
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        margin-bottom: 20px;
        max-width: 400px;
    }
    .productionTable{
        width: 100%; 
        max-width: 1200px; 
        margin-left: auto;
        margin-right: auto;
        font-size: 1.25rem;
    }
    .productionTable td{
        padding-top: 18px;
        padding-bottom: 18px;
        cursor: pointer;
    }  
    .headerTable{
        width: 100%; 
        max-width: 1200px; 
        margin-left: auto;
        margin-right: auto;
    }
    .form-control{
        text-align: center;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    </style>
@stop

@section('js')
    
@stop

@section('content')
    <div class="h-100" style="display: table; text-align: center; width: 100%;">
        <form style="display: table-cell; vertical-align: middle;" class="disableChildAnchor" method="POST" action="{{ route('production.detailing.save') }}">
            <div class="input-group-lg" style="max-width: 900px; margin-left: auto; margin-right: auto;">
                @csrf
                <input type="hidden" name="orderId" value="{{ $orderId }}" />
                <input type="hidden" name="workTimingRelatorId" value="{{ $orderDetailId }}" required /> 
                <table class="headerTable">
                    <tr>
                        <td style="text-align: left;">
                            <h1>Co chcesz zrobić z tym detalem?</h1>
                            <h5 class="mb-3">Wybierz opcję obróbki, aby rozpocząć pracę nad detalem:</h5>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route("production.dashboard") }}" class="btn btn-dark btn-lg">
                                Powrót
                            </a>
                        </td>
                    </tr>
                </table>
                
                <select class="form-control mt-2" name="workTimingId" required>
                    <?php 
                        $totalOptions = 0; 
                        $procededWorkTimings = array();
                    ?>
                    @foreach($workTimings as $workTiming)
                        @foreach($userRoles as $userRole)
                            @if($workTiming->workTimingRoleSlug == $userRole->roleSlug && !in_array($userRole->workTimingId, $procededWorkTimings))
                                <?php
                                    array_push($procededWorkTimings, $userRole->workTimingId)
                                ?>
                                <option value="{{ $workTiming->workTimingId }}"
                                    @if($userRole->roleDemanding <= 0)
                                    disabled
                                    @endif
                                >
                                    {{ $workTiming->roleProcess }} (Zapotrzebowanie: {{ $userRole->roleDemanding }}) 
                                    <?php $currentCIP = 0; ?>
                                    @foreach($userRole->currentInProduction as $cIP) 
                                        @if($cIP->workTimingRelatorParentId == $workTiming->workTimingId)
                                            <?php $currentCIP++; ?>
                                        @endif
                                    @endforeach
                                    
                                    @if($currentCIP != 0) 
                                        [To zlecenie już wykonuje {{$currentCIP}} osób] 
                                    @endif
                                </option>
                                <?php $totalOptions++; ?>
                            @endif
                        @endforeach
                    @endforeach
                    @if($totalOptions == 0)
                        <option disabled>Administrator nie przewidział obróbki dla Twoich uprawnień!</option>
                    @endif
                </select>
                <input type="submit" class="btn btn-primary mt-4" value="Rozpocznij pracę!" />
            </div>
        </form>
    </div>
    
@stop