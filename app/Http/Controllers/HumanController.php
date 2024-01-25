<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HumanController extends Controller
{
    // convert minutes to human
    public static function convertMinutesToHuman($minutes, $type = "estimated"){
        if($type == "estimated"){
            if($minutes > 525600){
                return "Ponad ".floor($minutes / 525600)." lata";
            }else if($minutes > 43200){
                return "Ponad ".floor($minutes / 43200)." miesięcy";
            }else if($minutes > 43200){
                return "Ponad ".floor($minutes / 43200)." miesięcy";
            }else if($minutes > 10080){
                return "Ponad ".floor($minutes / 10080)." tygodni";
            }else if($minutes > 1440){
                return floor($minutes / 1440)." dni";
            }else if($minutes > 60){
                return round($minutes / 60, 2)." godzin";
            }else{
                return $minutes." minut";
            }
        }
    }
    // convert minutes to human
    public static function convertMinutesToHours($minutes, $type = "estimated"){
        if($type == "estimated"){
            if($minutes > 60){
                $fullHours = floor($minutes / 60);
                return $fullHours." godzin ".($minutes - ($fullHours * 60))." minut";
            }else{
                return $minutes." minut";
            }
        }
    }

    // convert double to fraction
    public static function doubleToFrac($n, $tolerance = 1.e-6) {
        $h1=1; $h2=0;
        $k1=0; $k2=1;
        $b = 1/$n;
        do {
            $b = 1/$b;
            $a = floor($b);
            $aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
            $aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
            $b = $b-$a;
        } while (abs($n-$h1/$k1) > $n*$tolerance);
    
        return "$h1/$k1";
    }
    public static function getMnemonicMonthName($monthNumber, $lang = "pl") {
        $monthNames = null;
        switch($lang){
            case "pl":
            default:
                $monthNames = array(
                    "Styczeń",
                    "Luty",
                    "Marzec",
                    "Kwiecień",
                    "Maj",
                    "Czerwiec",
                    "Lipiec",
                    "Sierpień",
                    "Wrzesień",
                    "Październik",
                    "Listopad",
                    "Grudzień",
                );
            break;
        }

        if(is_null($monthNames) || !isset($monthNames[$monthNumber-1])){
            return $monthNumber;
        }else{
            return $monthNames[$monthNumber-1];
        }
    }

}
