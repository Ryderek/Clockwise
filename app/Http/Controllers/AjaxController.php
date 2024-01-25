<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\PermissionsController as PC;

class AjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function handler($functionName, Request $request){
        switch ($functionName) {
            case "getContractorDataByName":
                return $this->getContractorDataByName($request);
                break;
            default:
                null;
                break;
        }
    }

    private function getContractorDataByName(Request $request){
        if(!PC::cp('create_orders')){
            return null;
        }

        $customerName = $request->input('customerName');

        $customerDetail = DB::table('customers')->select("*")->where('customerName', $customerName)->orderBy('customerId', 'DESC')->distinct()->first();
        return $customerDetail;

    }
}
