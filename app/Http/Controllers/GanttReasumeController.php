<?php

namespace App\Http\Controllers;

use DB;
use DateTime;
use App\Models\Role;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\HumanController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\PermissionsController as PC;

class Builder{}
class GanttReasumeController extends Controller
{
    private $processingCapacityHours;

    public function __construct(){
        $this->processingCapacityHours = 7.5;
    }
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
    public function show($date = null)
    {

        // Check permissions:
        if(!PC::cp('view_orders')){
            abort(404);
        }

        // Extract date:
        if(is_null($date)){
            $date = date("Y-m");
        }else{
            $date = date("Y-m", strtotime($date));
        }
        $date = explode("-", $date);
        $dateComplex = array(
            "year" => $date[0],
            "month" => $date[1],
            "firstDay" => date("Y-m-d", strtotime($date[0]."-".$date[1]."-01")),
            "lastDay" => date("Y-m-t", strtotime($date[0]."-".$date[1]."-01")),
            "monthMnemonic" => HumanController::getMnemonicMonthName($date[1]),
            "previousMonth" => date("Y-m", strtotime("-1 month", strtotime("{$date[0]}-{$date[1]}-01"))),
            "nextMonth" => date("Y-m", strtotime("+1 month", strtotime("{$date[0]}-{$date[1]}-01"))),
        );

        // Get business days:
        $AccountingController = new AccountingController();
        $AccountingController->calculateBusinessDays(strtotime($dateComplex["firstDay"]), strtotime($dateComplex["lastDay"]));

        // Get all roles:
        $Roles = Role::where('roleIsActive', 1)->get();
        foreach($Roles as $Role){
            $Role->dailyProcessingCapacity = $Role->roleStations * $this->processingCapacityHours;
            $Role->currentMonthProcessingCapacity = $Role->dailyProcessingCapacity * count($AccountingController->currentMonthBusinessDays);
        }

        //dd($dateComplex, $Roles);

        return view("admin.gantt.reasume", [
            "DateComplex" => $dateComplex,
            "Roles" => $Roles,
            "pageHeader" => "Diagram Gantta"
        ]);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

    /**
     * Load the data of specified resource.
     */
    public function loadData(Request $request, $date)
    { 
        // Step 0: Preparation
        
        // Proceed the data:
        if(is_null($date)){
            $date = date("Y-m");
        }
        $dateExploded = explode("-", $date);
        $dateComplex = array(
            "year" => (int) $dateExploded[0],
            "month" => (int) $dateExploded[1],
            "firstDay" => date("Y-m-d", strtotime($dateExploded[0]."-".$dateExploded[1]."-01")),
            "lastDay" => date("Y-m-t", strtotime($dateExploded[0]."-".$dateExploded[1]."-01")),
            "monthMnemonic" => HumanController::getMnemonicMonthName($dateExploded[1]),
            "previousMonth" => date("Y-m", strtotime("-1 month", strtotime("{$dateExploded[0]}-{$dateExploded[1]}-01"))),
            "nextMonth" => date("Y-m", strtotime("+1 month", strtotime("{$dateExploded[0]}-{$dateExploded[1]}-01"))),
        );
        //dd($dateComplex);

        // Calculate progress
        $progress = (strtotime(date("Y-m-d H:i:s")) - strtotime($dateComplex['firstDay']))/(strtotime($dateComplex['lastDay']) - strtotime($dateComplex['firstDay'])); // Calculate progress as the difference between today-start date and stop-start date
        if($progress > 1) $progress = 1; // If progress higher than 1, then it's complete.
        $success = true; // Predict success

        // Clear data arrays
        $data = array();
        $links = array();

        // Get business days:
        $AccountingController = new AccountingController();
        $businessDays = $AccountingController->calculateBusinessDays(strtotime($dateComplex["firstDay"]), strtotime($dateComplex["lastDay"]));


        // Step 1: Load all Roles
        if($success) try{
            $Roles = Role::where('roleIsActive', 1)->get();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#1:".$e;
        }

        // Roles are loaded, do one more configuration (prepare "sum up" complex dictionary in current month)
        $SummedUpComplexTimes = array();
        foreach($Roles as $Role){
            if(!isset($SummedUpComplexTimes[$Role->roleSlug])){
                $SummedUpComplexTimes[$Role->roleSlug]["durationMove"] = 0;
                $SummedUpComplexTimes[$Role->roleSlug]["predicted"] = 0;
                $SummedUpComplexTimes[$Role->roleSlug]["dayWorkPower"] = $Role->roleStations * $this->processingCapacityHours;
                $SummedUpComplexTimes[$Role->roleSlug]["monthlyWorkPower"] = $SummedUpComplexTimes[$Role->roleSlug]["dayWorkPower"] * count($AccountingController->currentMonthBusinessDays);
            }
        }

        // Step 2: Get all orders with in-production date or deadline is between two dates and if
        try{
            $startDate = $dateComplex['firstDay'];
            $stopDate = $dateComplex['lastDay'];
            $Orders = DB::table('orders')
                ->where(function ($query) use ($startDate, $stopDate) {
                    $query->whereBetween('orderConfirmedTime', [$startDate, $stopDate])
                        ->orWhereBetween('orderDeadline', [$startDate, $stopDate])
                        ->orWhere(function ($query) use ($startDate, $stopDate) {
                            $query->where('orderConfirmedTime', '<', $startDate)
                                ->where('orderDeadline', '>', $stopDate);
                        });
                })
                ->whereNull('orderDoneBy') // Only in progress
                ->whereNotNull('orderConfirmedBy') // Only in progress
                ->orderBy('orderDeadline', 'ASC')
                ->get();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#2:".$e;
        }

        // Step 3: Load all complexWorkTimings for Orders
        if($success) try{
            $WTC = new WorkTimingController();
            foreach($Orders as $Order){
                // Here we should add the code that calculate business days but only in current month (exclude previous/next months)
                if($Order->orderDoneBy == NULL && $Order->orderConfirmedBy != NULL){ //
                    $Order->complexWTs = $WTC->getComplexWorkTimings($Order->orderId);
                    foreach($Order->complexWTs as $ComplexWT){
                        $SummedUpComplexTimes[$ComplexWT["workTimingRoleSlug"]]['predicted'] += $ComplexWT["workTimingFinal"];
                    }
                }
            }
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#3:".$e;
        }


        //dd($SummedUpComplexTimes, $Roles);

        // Step 4: Format output response
        
        if($success) try{

            // Insert roles:
            foreach($Roles as $Role){

                // Progress is the result of dividing predicted time (per order) and work-power: 
                $progress = round($SummedUpComplexTimes[$Role->roleSlug]['predicted'] / $SummedUpComplexTimes[$Role->roleSlug]['monthlyWorkPower'], 2);
                $calculatedPercentage = round($progress*100, 0);
                array_push($data, [
                    "id" => $Role->roleId, // Each's role Id is unique
                    "text" => $Role->roleProcess." (".$SummedUpComplexTimes[$Role->roleSlug]['predicted']." roboczogodzin | ".$calculatedPercentage." %)",
                    "duration" => ($this->calculateDaysBetweenDates($dateComplex['firstDay'], $dateComplex['lastDay'])+1)*24,
                    "progress" => $progress,
                    "start_date" => $dateComplex['firstDay']." 00:00:01",
                    "parent" => 0, // Set no parent for role
                    "created_at" => $dateComplex['firstDay']."T00:00:01.000000Z",
                    "updated_at" => $dateComplex['firstDay']."T23:59:59.000000Z",
                    "open" => true
                ]);
            }

            // Insert Orders between roles:
            foreach($Orders as $Order){
                //dd($Order);
                foreach($Order->complexWTs as $cwt){
                    //dd($cwt);
                    if($cwt['workTimingFinal'] != 0){
                        $currentDuration = ($cwt['workTimingFinal']/$SummedUpComplexTimes[$cwt['roleSlug']]['monthlyWorkPower'])*$businessDays;
                        if($currentDuration > 0){
                            //dd($currentDuration, $Order->orderName);
                           
                            // Add durationMove, to make bars move to right chronously
                            $currentWorkPower = ($cwt['workTimingFinal']/$SummedUpComplexTimes[$cwt['roleSlug']]['monthlyWorkPower']);


                            // Konwersja daty na obiekt DateTime
                            $complexDateTime = new DateTime($dateComplex['firstDay']." 00:00:01");
                            $moveDateByDays = $SummedUpComplexTimes[$cwt['roleSlug']]["durationMove"] * 24 * 24;
                            $complexDateTime->modify("+".floor($moveDateByDays)." hours");
                            $newStartDate = $complexDateTime->format("Y-m-d H:i:s");
                            
                            
                            $currentId = (int) "99".$cwt['roleId']."99".$Order->orderId; // Each's order should be unique
                            array_push($data, [
                                "id" => $currentId,
                                "text" => $Order->orderName." ".($currentDuration)." ".($SummedUpComplexTimes[$cwt['roleSlug']]["durationMove"] * 24)." (".$cwt['workTimingFinal']."/".$SummedUpComplexTimes[$cwt['roleSlug']]['monthlyWorkPower'].")",
                                "duration" => $currentDuration*24,
                                "progress" => 0,
                                "start_date" => $newStartDate,
                                "parent" => $cwt['roleId'], // Set no parent for role
                                "created_at" => $dateComplex['firstDay']."T00:00:01.000000Z",
                                "updated_at" => $dateComplex['firstDay']."T23:59:59.000000Z",
                                "workTimingFinal" => $cwt['workTimingFinal'],
                                "open" => true
                            ]);

                            
                            $SummedUpComplexTimes[$cwt['roleSlug']]["durationMove"] += ($currentDuration/24);

                        }
                    }
                }
            }

        }catch(\Throwable $e){
            $success = false;
            $errorMessage = "Step#4:".$e;
        }
        //dd($data);
        //dd($dateComplex['firstDay'], $dateComplex['lastDay'], $request->input("date"), $date, $Orders, $dateExploded, $data);

        if($success){
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

        
    }

}
