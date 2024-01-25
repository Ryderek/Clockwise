<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GusApiController;

class ApiController extends Controller
{
    public function handleApiRequest($apiName, $apiFunction, Request $request){
        switch($apiName){
            case "gus":
                $GUS = new GusApiController();
                return $GUS->$apiFunction($request);
                break;

            default:
                return false;
                break;
        }
    }
}
