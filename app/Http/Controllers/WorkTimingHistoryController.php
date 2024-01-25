<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkTimingHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionsController as PC;
use DB;

class WorkTimingHistoryController extends Controller
{   
    /**
     * Create estimated time for specified detail
     */
    public static function store($detailId, $noteContent, $userId, $detailsDone){
        $WTH = new WorkTimingHistory();
        $WTH->workTimingHistoryDetailId = $detailId;
        $WTH->workTimingHistoryDescriptor = $noteContent;
        $WTH->workTimingHistoryDetailsDone = $detailsDone;
        $WTH->workTimingHistoryUserId = $userId;
        $WTH->save();
    }

    public static function showHistoryOfDetail($detailId){
        if(!PC::cp('view_orders')){
            return null;
        }
        return DB::table('work_timings_history')->select("workTimingHistoryId", "workTimingHistoryDescriptor", "created_at")->where('workTimingHistoryDetailId', $detailId)->orderBy("created_at", "DESC")->get();
    }

    public static function showHistoryOfDetailsInOrder($orderId){
        if(!PC::cp('view_orders')){
            return null;
        }
        $OC = new OrderController();
        $Details = $OC->getAllDetailsByOrderId($orderId);
        $query = DB::table('work_timings_history')->select("*");
        $whereRaw = "";
        $availableDetails = $Details->count();
        for($a=0; $a<$availableDetails; $a++){
            $whereRaw .= "`workTimingHistoryDetailId` = '".$Details[$a]->orderDetailId."'";
            if($a != ($availableDetails - 1)){
                $whereRaw .= " OR ";
            }
        }
        if($whereRaw != ""){
            return $query->whereRaw($whereRaw)->orderBy("workTimingHistoryId", "DESC")->join('order_details', 'workTimingHistoryDetailId', '=', 'orderDetailId')->get();
        }else{
            return null;
        }
    }

    public function getLastFiveWorkTimes(){
        if(!PC::cp('view_orders')){
            return null;
        }
        //SELECT * FROM `work_timings_history` ORDER BY `work_timings_history`.`updated_at` DESC 
        return DB::table('work_timings_history')->select(["workTimingHistoryDescriptor", "workTimingHistoryId", "updated_at"])->orderBy("updated_at", "DESC")->limit(5)->get();
    }
}
