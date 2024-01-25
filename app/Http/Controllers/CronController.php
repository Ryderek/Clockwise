<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkTiming;
use Illuminate\Http\Request;

class CronController extends Controller
{
    // Kick persons that forget to log out leaving company
    public function kickForgetfulLefters(){
        $CurrentWorkings = WorkTiming::where('workTimingType', 'worktime')->where('workTimingStart', '!=', 0)->whereNull('workTimingFinal')->get();

        $kickAfterHours = (int) env('APP_KICK_FORGETFUL_LEFTERS_AFTER_HOURS'); // Default: 13 hours
        $kickAfterSeconds = $kickAfterHours * 60 * 60; // Check whether worktime is longer than 60 seconds * 60 minutes * 13 hours = 46 800 = $kickAfterSeconds.

        $kickCount = 0;

        foreach($CurrentWorkings as $CW){
            $difference = time() - $CW->workTimingStart;
            
            // Kick forgetful lefters after specified amount of time and set their working time to max, defined by part/fulltime (etat)
            if($difference > $kickAfterSeconds){
                
                $User = User::where('id', $CW->workTimingUserId)->first();
                $UserPartTimeInSeconds = 8 * 60 * 60 * $User->partTimeJob; // 8 hours * 60 minutes * 60 seconds * partTime

                $CW->workTimingEnd = $CW->workTimingStart + $UserPartTimeInSeconds;
                $CW->workTimingFinal = $UserPartTimeInSeconds;
                $CW->save();

                
                $kickCount++;

            }
        }

        return response()->json([
            'kickCount' => $kickCount,
        ]);
    }
}
