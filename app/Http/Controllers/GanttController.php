<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\WorkTiming;
use App\Models\GanttDynamicTasks;
use App\Models\GanttDynamicLinks;
use App\Http\Controllers\WorkTimingController;
use App\Http\Controllers\PermissionsController as PC;
use DB;

class Builder{}
class GanttController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(!PC::cp('view_orders')){
            abort(404);
        }
        $Order = Order::find($id);
        return view("admin.gantt.show", [
            "id" => $id,
            "order" => $Order,
            "pageHeader" => "Diagram Gantta"
        ]);
    }

    /**
     * Load the data of specified resource.
     */
    public function loadData(string $orderId)
    { 
        // Step 0: Clear workspace

        $this->clearWorkspace();
        $success = true;
        $errorMessage = "";
        $Builder = new Builder();

        // Step 1: Get order info
        try{
            $Builder->Order = Order::find($orderId);
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#1:".$e;
        }

        // Step 2: Get details info
        if($success) try{
            $Builder->Details = DB::table("order_details")->select("*")->where("orderDetailOrderId", $orderId)->get();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#2:".$e;
        }

        // Step 3: Get work timing info
        if($success){
            $WTC = new WorkTimingController();
            foreach($Builder->Details as $detail){
                try{
                    $detail->WorkTimings = $WTC->getPivotTableOfDetailId($detail->orderDetailId, false);
                }catch(\Throwable $e){
                    $success = false;
                    $errorMessage = "Step#3:".$e;
                }
            }
        }

        // Step 4: Insert order name as first task:
        $duration = $this->calculateDaysBetweenDates($Builder->Order->orderCreatedTime, $Builder->Order->orderDeadline);
        $progress = $this->calculateDaysBetweenDates(time(), $Builder->Order->orderDeadline) / 86400;
        $ProjectGDT = new GanttDynamicTasks([
            "text" => $Builder->Order->orderName,
            "duration" => $duration,
            "progress" => $progress,
            "start_date" => $Builder->Order->orderCreatedTime,
            "parent" => 0,
        ]);
        $ProjectGDT->save();
        $ProjectParent = $ProjectGDT->id;

        // Step 5: Insert worktimings to details and details to order

        foreach($Builder->Details as $detail){
            $DetailGDT = new GanttDynamicTasks([
                "text" => $detail->orderDetailName,
                "duration" => $this->calculateDaysBetweenDates($Builder->Order->orderCreatedTime, $Builder->Order->orderDeadline),
                "progress" => ($detail->orderDetailItemsDone / $detail->orderDetailItemsTotal),
                "start_date" => $Builder->Order->orderCreatedTime,
                "parent" => $ProjectParent,
            ]);
            $DetailGDT->save();
            $DetailParent = $DetailGDT->id;
            $previousSkipDays = 0;
            foreach($detail->WorkTimings as $worktiming){
                // add 7 days to the date above
                $start_date = date('Y-m-d H:i:s', strtotime($Builder->Order->orderCreatedTime . " +$previousSkipDays days"));
                $totalDetailsItemsDone = 0;
                foreach($worktiming->realTimesArray as $wt_rta){
                    if($wt_rta->workTimingEnd != null && $worktiming->workTimingId == $wt_rta->workTimingRelatorParentId){
                        $totalDetailsItemsDone += $wt_rta->workTimingFinal;
                    }
                }
                //$start_date = date('Y-m-d H:i:s', strtotime($Builder->Order->orderCreatedTime));
                $WorkTimingGDT = new GanttDynamicTasks([
                    "text" => $worktiming->roleProcess." (".$this->minutesToWorkhours($worktiming->workTimingFinal)." h)",
                    "duration" => $this->minutesToWorkdays($worktiming->workTimingFinal),
                    "progress" => ($totalDetailsItemsDone / $detail->orderDetailItemsTotal),
                    "start_date" => $start_date,
                    "parent" => $DetailParent,
                ]);
                 $previousSkipDays += $this->minutesToWorkdays($worktiming->workTimingFinal*$detail->orderDetailItemsTotal);
                $WorkTimingGDT->save();
            }
        }

        if($success){
            $data = GanttDynamicTasks::all();
            $links = GanttDynamicLinks::all();
            return response()->json([
                "data" => $data,
                "links" => $links,
            ]);
        }else{
            return response()->json([
                "success" => false,
                "errorMessage" => $errorMessage,
            ]);
        }
        /*return response()->json([
            "data" => WorkTiming::all() 
        ]);*/
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Truncate datas from previous charts.
     */
    public function clearWorkspace(){
        DB::statement("TRUNCATE `gantt_dynamic_tasks`;");
        DB::statement("TRUNCATE `gantt_dynamic_links`;");
    }

    /**
     * Calculate days between two dates:
     */
    private function calculateDaysBetweenDates($start, $end){
        if(strtotime($end) > strtotime($start)){
            $datediff = strtotime($end) - strtotime($start);
        }else{
            $datediff = strtotime($start) - strtotime($end);
        }
        return round($datediff / (60 * 60 * 24));
    }

    private function minutesToWorkdays($minutes){
        $manufacturingWorkHours = env("MANUFACTURING_WORKHOURS");
        $totalHours = ceil($minutes/60);
        $totalDays = ceil($totalHours/$manufacturingWorkHours);
        return $totalDays;
    }
    private function minutesToWorkhours($minutes){
        $totalHours = ceil($minutes/60);
        return $totalHours;
    }
    private function calculateOrderProgress($stratDate, $deadlineDate){
        
    }
}
