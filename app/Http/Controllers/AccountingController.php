<?php

namespace App\Http\Controllers;

use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\User;
use App\Models\WorkTiming;
use App\Http\Controllers\HumanController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\PermissionsController as PC;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    /**
     *  Define class variables, but don't assign.
     */
    private $currentDate;
    private array $currentMonthRange;
    public array $currentMonthBusinessDays; // Only if calculateBusinessDays() has been executed
    public int $businessDays;
    private $previousMonthDate;
    private $nextMonthDate;
    private $holidayDays;

    public function __construct() {
        $this->holidayDays = [ // Define fixed holidays

            // Dynamic:
            '*-01-01', // New year's eve
            '*-01-06', // 3 króli
            '*-05-01', // Święto pracy
            '*-05-03', // Święto konstytucji 3 maja
            '*-08-15', // Święto Wojska Polskiego
            '*-11-01', // Wszystkich świętych
            '*-11-11', // Święto niepodległości
            '*-12-25', // Christmas (day 1)
            '*-12-26', // Christmas (day 2)

            // Static:
            '2023-04-09', // Wielkanoc
            '2023-04-10', // Poniedziałek Wielkanocny
            '2023-05-28', // Zielone świątki
            '2023-06-08', // Boże Ciało
        ];
    }

    /**
     * Display a listing of the orders.
     */
    public function accountingIndex($page = 1)
    {
        if(!PC::cp('manage_accounting')){
            abort(403);
        }
        if(isset($_GET['date'])){ $this->preloadAccountingVariables($_GET['date']); } else {$this->preloadAccountingVariables();} // Load variables and store them into class variables.

        $ODC = new OrderDetailController();

        $orders = DB::table('orders')->select("*")->where('orderStatus', 'done')->whereRaw('orderDoneTime BETWEEN "'.date("Y-m-d H:i:s", $this->currentMonthRange[0]).'" AND "'.date('Y-m-d H:i:s', $this->currentMonthRange[1]).'"')->join('customers', 'customers.customerId', '=', 'orders.orderCustomer')->get(); // Get all active users.
        foreach($orders as $order){
            $order->detailsCount = $ODC->getDetailsCountBasedOnOrderId($order->orderId);
        }

        $month_name = date('F Y', strtotime($this->currentDate));

        return view("admin.accounting.accounting", [
            "orders" => $orders,
            "previousMonthDate" => $this->previousMonthDate,
            "nextMonthDate" => $this->nextMonthDate,
            "currentDateHuman" => $this->translateEnglishMonthsName($month_name),
            "currentDate" => $this->currentDate,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".accounting.".$page,
            "orderRoute" => "admin".env('APP_ADMIN_POSTFIX').".accounting.order",
        ]);
    }

    /**
     * Display a listing of the orders.
     */
    public function salariesIndex(Request $request, $date = null)
    {
        if(!PC::cp('manage_settlement')){
            abort(403);
        }

        if($request !== null && $request->input('salariesPass') == env('APP_SALARIES_PASS')){
            // Auth view
            if($date !== null){
                $this->currentDate = strtotime($date);
            }
            $employees = DB::table('users')->select("*")->where('isActive', 1)->get(); // Get all active users.
            $this->preloadAccountingVariables($date); // Load variables and store them into class variables.
    
            foreach($employees as $employee){
                $employee = $this->fillEmployeeWithCurrentMonthWorktimingsSummary($employee);
                $employee = $this->fillEmployeeWithAmIAtWork($employee);
            }
    
            $month_name = date('F Y', strtotime($this->currentDate));
            //dd($employees);
    
            return view("admin.accounting.salaries", [
                "employees" => $employees, 
                "previousMonthDate" => $this->previousMonthDate,
                "nextMonthDate" => $this->nextMonthDate,
                "currentDateHuman" => $this->translateEnglishMonthsName($month_name),
                "currentDate" => $this->currentDate,
                "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".salaries",
                "userRoute" => "admin".env('APP_ADMIN_POSTFIX').".settlement.user",
                "salariesPass" => $request->input('salariesPass'), 
            ]);
            
        }else{

            return view("admin.accounting.salaries-auth", [

            ]);

        }

    }

    /**
     * Display a listing of the employees.
     */
    public function settlementIndex($page = 1)
    {
        if(!PC::cp('manage_settlement')){
            abort(403);
        }
        $employees = DB::table('users')->select("*")->where('isActive', 1)->get(); // Get all active users.
        if(isset($_GET['date'])){ $this->preloadAccountingVariables($_GET['date']); } else {$this->preloadAccountingVariables();} // Load variables and store them into class variables.

        foreach($employees as $employee){
            $employee = $this->fillEmployeeWithCurrentMonthSettlement($employee);
            $employee = $this->fillEmployeeWithAmIAtWork($employee);
        }

        $month_name = date('F Y', strtotime($this->currentDate));

        return view("admin.accounting.settlement", [
            "employees" => $employees,
            "previousMonthDate" => $this->previousMonthDate,
            "nextMonthDate" => $this->nextMonthDate,
            "currentDateHuman" => $this->translateEnglishMonthsName($month_name),
            "currentDate" => $this->currentDate,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".settlement.".$page,
            "userRoute" => "admin".env('APP_ADMIN_POSTFIX').".settlement.user",
        ]);
    }

    /**
     * Display a listing settlements of specified user
     */
    public function settlementUser($userId)
    {
        if(!PC::cp('manage_settlement')){
            abort(403);
        }
        $employee = DB::table('users')->select("*")->where('id', $userId)->first(); // Get all active users.
        if(isset($_GET['date'])){ $this->preloadAccountingVariables($_GET['date']); } else {$this->preloadAccountingVariables();} // Load variables and store them into class variables.

        $employee = $this->fillEmployeeWithCurrentMonthSettlement($employee, true);

        $month_name = date('F Y', strtotime($this->currentDate));

        return view("admin.accounting.settlement-person", [
            "employee" => $employee,
            "previousMonthDate" => $this->previousMonthDate,
            "nextMonthDate" => $this->nextMonthDate,
            "currentDateHuman" => $this->translateEnglishMonthsName($month_name),
            "currentDate" => $this->currentDate,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".settlement.user.".$userId,
        ]);
    }

    /**
     *  Update salaries table
     */
    public function salariesUpdate(Request $request){
        foreach($request->employeeWage as $employeeId => $wageTable){
            $currentEmployee = User::where('id', $employeeId)->first();
            $currentEmployee->employeeDefaultWage = (double) str_replace(",", ".", $wageTable['employeeDefaultWage']);
            $currentEmployee->employeeOvertimeWage = (double) str_replace(",", ".", $wageTable['employeeOvertimeWage']);
            $currentEmployee->employeeSpecialWage = (double) str_replace(",", ".", $wageTable['employeeSpecialWage']);
            $currentEmployee->save();
        }
        return redirect(route('salaries-auth', [
            'successMessage' => 'Pomyślnie zaktualizowano płace. Ze względów bezpieczeństwa nastąpiło wylogowanie.'
        ]));
    }

    /**
     * Get timestamp of first day of the month (00:00) specified month and last day (23:59)
     */
    private function getMonthStartAndEnd($query_date){
        return array(
            strtotime(date('Y-m-01 00:00:01', strtotime($query_date))),
            strtotime(date('Y-m-t 23:59:59', strtotime($query_date))),
        );
    }

    /**
     * Calculate business days
     */
    public function calculateBusinessDays($from, $to) {
        // Empty array of working days:
        $this->currentMonthBusinessDays = array();
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)

        $from_time = $from;
        $to_time = $to;

        $from = new DateTime();
        $from->setTimestamp($from_time);
        
        $to = new DateTime();
        $to->setTimestamp($to_time);

        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);
    
        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $this->holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $this->holidayDays)) continue;
            $days++;
            array_push($this->currentMonthBusinessDays, $period->format('Y-m-d')); // Add current day to business days
        }
        return $days;
    }

    /**
     * Preload date variables and save them into the class variables (to avoid redundancy)
     */
    private function preloadAccountingVariables($date = null){
        // If is specified date, get the month. Otherwise get the current date.
        if($date !== null){
            $this->currentDate = $date;
        }else{
            $this->currentDate = date("Y-m-d");
        }

        $this->currentMonthRange = $this->getMonthStartAndEnd($this->currentDate);
        $this->businessDays = $this->calculateBusinessDays($this->currentMonthRange[0], $this->currentMonthRange[1]);

        $this->previousMonthDate = date("Y-n-j", strtotime ('first day of previous month' , strtotime($this->currentDate)));
        $this->nextMonthDate = date("Y-n-j", strtotime ('first day of next month' , strtotime($this->currentDate)));

    }

    /**
     * Fill employee with time
     */
    private function fillEmployeeWithCurrentMonthSettlement($employee, $requireSettlements = false, $includeWorktimings = false){
        $employeeId = $employee->id;
        $from = $this->currentMonthRange[0];
        $to = $this->currentMonthRange[1];

        $settlements = DB::table('work_timings')->select("*")->where('workTimingType', 'worktime')->where('workTimingUserId', $employeeId)->orderBy('workTimingId', 'ASC')->whereRaw('workTimingStart BETWEEN '.$from.' AND '.$to)->get();
        
        $totalWorkedSeconds = 0;
        if($includeWorktimings){
            // Include worktimings if required
            $employee->worktimings = array();
        }
        foreach($settlements as $settlement){
            if($settlement->workTimingEnd != null){
                $totalWorkedSeconds += $settlement->workTimingFinal;
                $settlement->workTimingFinalHuman = [floor($settlement->workTimingFinal/ 3600), floor($settlement->workTimingFinal / 60 % 60), floor($settlement->workTimingFinal % 60)];
                if(strlen($settlement->workTimingMeta) != 0){
                    $settlement->workTimingMeta = json_decode($settlement->workTimingMeta);
                }
            }else{
                $difference = (time() - $settlement->workTimingStart);
                $totalWorkedSeconds += $difference;
                $settlement->workTimingFinalHuman = [floor($difference/ 3600), floor($difference / 60 % 60), floor($difference % 60)];
            }
            if($includeWorktimings){
                // Include worktimings if required
                array_push($employee->worktimings, $settlement);
            }
        }
        $employee->totalWorkedTime = [floor($totalWorkedSeconds/ 3600), floor($totalWorkedSeconds / 60 % 60), floor($totalWorkedSeconds % 60)];
        $employee->requiredWorkedTime = 8*$employee->partTimeJob*$this->businessDays;
        if($employee->totalWorkedTime[0] > $employee->requiredWorkedTime){
            $employee->overWorkedTime = $employee->totalWorkedTime;
            $employee->overWorkedTime[0] = $employee->overWorkedTime[0]-$employee->requiredWorkedTime;
        }else{
            $employee->overWorkedTime = [0, 0, 0];
        }
        if($requireSettlements){
            $employee->settlements = $settlements;
        }
        $employee->partTimeJobHuman = HumanController::doubleToFrac($employee->partTimeJob);
        return $employee;
    }

    private function calculateEmployeeWorktimes($employee, $totalSpecialTimeInSeconds, $totalOverTimeInSeconds, $totalDefaultTimeInSeconds){

        $employee->employeeDefaultTime = [
            "hours" => floor($totalDefaultTimeInSeconds/ 3600), 
            "minutes" => floor($totalDefaultTimeInSeconds / 60 % 60), 
            "seconds" => floor($totalDefaultTimeInSeconds % 60)
        ];

        $employee->employeeOverTime = [
            "hours" => floor($totalOverTimeInSeconds/ 3600), 
            "minutes" => floor($totalOverTimeInSeconds / 60 % 60), 
            "seconds" => floor($totalOverTimeInSeconds % 60)
        ];

        $employee->employeeSpecialTime = [
            "hours" => floor($totalSpecialTimeInSeconds/ 3600), 
            "minutes" => floor($totalSpecialTimeInSeconds / 60 % 60), 
            "seconds" => floor($totalSpecialTimeInSeconds % 60)
        ];

        return $employee;
    }

    private function fillEmployeeWithCurrentMonthWorktimingsSummary($employee, $requireSettlements = false, $includeWorktimings = false){
        // This function is familiar with fillEmployeeWithCurrentMonthSettlement, but updated for customers wish.
        $employeeId = $employee->id;
        $from = $this->currentMonthRange[0];
        $to = $this->currentMonthRange[1];

        $settlements = DB::table('work_timings')->select("*")->where('workTimingType', 'worktime')->where('workTimingUserId', $employeeId)->orderBy('workTimingId', 'ASC')->whereRaw('workTimingStart BETWEEN '.$from.' AND '.$to)->get();
        
        $employee->requiredWorkedTime = 8*$employee->partTimeJob*$this->businessDays;
        $employee->requiredWorkedTimeDaily = 8*$employee->partTimeJob; // As above, but only for one day.
        $employee->partTimeJobHuman = HumanController::doubleToFrac($employee->partTimeJob);

        $totalWorkedSeconds = 0;
        if($includeWorktimings){
            // Include worktimings if required
            $employee->worktimings = array();
        }

        $totalDefaultTimeInSeconds = 0;
        $totalOverTimeInSeconds = 0;
        $totalSpecialTimeInSeconds = 0;

        $calculatedPartTimeDailyInSeconds = $employee->requiredWorkedTimeDaily * 60 * 60;


        foreach($settlements as $settlement){

            if($settlement->workTimingEnd !== null){

                $differenceInSeconds = $settlement->workTimingEnd - $settlement->workTimingStart;

                if(strlen($settlement->workTimingMeta) != 0){ // Some modifiers are applied, check it
                    
                    // Check whether it's partially paid (holiday or sickleave)
                    $workTimingMeta = json_decode($settlement->workTimingMeta);
                    if(isset($workTimingMeta->paidPercentCalculated)){
                        // Calculate only part time (partially paid)
                        $differenceInSeconds = $differenceInSeconds * ($workTimingMeta->paidPercentCalculated / 100);
                    }
    
                }
        
                // Check whether start time is holiday or weekend. If so, sum up time as SpecialWageSalary
                if($this->isHolidayTimestamp($settlement->workTimingStart)){
                    $totalSpecialTimeInSeconds += ($differenceInSeconds);
                }else{ // It's casual day. Check whether time includes overtime:
                    if(($settlement->workTimingEnd - $settlement->workTimingStart) > $calculatedPartTimeDailyInSeconds){ // Check which Worktiming is longer than 28 800 seconds (over 8 hours to calculate overtime)
                        // Time has overtime included. Calculate the difference to extract overtime from total time.
                        $totalOverTimeInSeconds += ( ( $differenceInSeconds ) - $calculatedPartTimeDailyInSeconds );
                        // also, sum up default time:
                        $totalDefaultTimeInSeconds += $calculatedPartTimeDailyInSeconds;
                    }else{
                        // No overtime.
                        $totalDefaultTimeInSeconds += ($differenceInSeconds);
                    }
                }
               
                if($includeWorktimings){
                    // Include worktimings if required
                    array_push($employee->worktimings, $settlement);
                }

            }
            
        }


        $employee = $this->calculateEmployeeWorktimes($employee, $totalSpecialTimeInSeconds, $totalOverTimeInSeconds, $totalDefaultTimeInSeconds);

        // Check whether default work time hits minimal month's workhours. If not, get the time from overtime.
        if( (double) $employee->employeeDefaultTime["hours"] < (double) $employee->requiredWorkedTime ){
            $defaultTimeTotal = $employee->employeeDefaultTime["hours"]*60*60 + $employee->employeeDefaultTime["minutes"]*60 + $employee->employeeDefaultTime["seconds"];
            $overTimeTotal = $employee->employeeOverTime["hours"]*60*60 + $employee->employeeOverTime["minutes"]*60 + $employee->employeeOverTime["seconds"];
            
            $minimalWorktimeTotal = $employee->requiredWorkedTime*60*60;

            $deficit = $minimalWorktimeTotal - $defaultTimeTotal;
            // Check whether overTime is able to cover normal time's deficit
            if($overTimeTotal > $deficit){
                // Yes, it is. Calculate...
                $overTimeTotal = $overTimeTotal - $deficit;
                $defaultTimeTotal = $defaultTimeTotal + $deficit;
            }else{
                // No, it isn't. Transfer all overtime to normaltime.
                $defaultTimeTotal = $defaultTimeTotal + $overTimeTotal;
                $overTimeTotal = 0;
            }

            $totalDefaultTimeInSeconds = $defaultTimeTotal;
            $totalOverTimeInSeconds = $overTimeTotal;

            // Recalculate employee worktimes
            $employee = $this->calculateEmployeeWorktimes($employee, $totalSpecialTimeInSeconds, $totalOverTimeInSeconds, $totalDefaultTimeInSeconds);
        }


        // Check whether still default work time hits minimal month's workhours. If not, get the time from weekendTime (specialTime).
        if( (double) $employee->employeeDefaultTime["hours"] < (double) $employee->requiredWorkedTime ){
            $defaultTimeTotal = $employee->employeeDefaultTime["hours"]*60*60 + $employee->employeeDefaultTime["minutes"]*60 + $employee->employeeDefaultTime["seconds"];
            $specialTimeTotal = $employee->employeeSpecialTime["hours"]*60*60 + $employee->employeeSpecialTime["minutes"]*60 + $employee->employeeSpecialTime["seconds"];
            
            $minimalWorktimeTotal = $employee->requiredWorkedTime*60*60;

            $deficit = $minimalWorktimeTotal - $defaultTimeTotal;
            // Check whether specialTime is able to cover normal time's deficit
            if($specialTimeTotal > $deficit){
                // Yes, it is. Calculate...
                $specialTimeTotal = $specialTimeTotal - $deficit;
                $defaultTimeTotal = $defaultTimeTotal + $deficit;
            }else{
                // No, it isn't. Transfer all overtime to normaltime.
                $defaultTimeTotal = $defaultTimeTotal + $specialTimeTotal;
                $specialTimeTotal = 0;
            }

            $totalDefaultTimeInSeconds = $defaultTimeTotal;
            $totalSpecialTimeInSeconds = $specialTimeTotal;

            // Recalculate employee worktimes
            $employee = $this->calculateEmployeeWorktimes($employee, $totalSpecialTimeInSeconds, $totalOverTimeInSeconds, $totalDefaultTimeInSeconds);
        }


        // Sum up full hours and each 15 minutes above the limit.
        $employee->employeeDefaultSalary = round( ($employee->employeeDefaultTime['hours'] * $employee->employeeDefaultWage) + floor( $employee->employeeDefaultTime['minutes'] / 15) * ($employee->employeeDefaultWage / 4), 2);
        $employee->employeeOverTimeSalary = round( ($employee->employeeOverTime['hours'] * $employee->employeeOvertimeWage) + floor( $employee->employeeOverTime['minutes'] / 15) * ($employee->employeeOvertimeWage / 4), 2);
        $employee->employeeSpecialSalary = round( ($employee->employeeSpecialTime['hours'] * $employee->employeeSpecialWage) + floor( $employee->employeeSpecialTime['minutes'] / 15) * ($employee->employeeSpecialWage / 4), 2);
       
        $employee->employeeFinalSalary = $employee->employeeDefaultSalary + $employee->employeeOverTimeSalary + $employee->employeeSpecialSalary;

        return $employee;
    }

    public function fillEmployeeWithAmIAtWork($employee){
        $WTC = new WorkTimingController();
        $amIAtWork = $amIAtWorkObject = $WTC->isEmployeeAtWork($employee->id, "userId");
        if(gettype($amIAtWork) == "object"){
            $amIAtWork = true;
        }else{
            $amIAtWork = false;
        }
        $employee->currentWorkTiming = $amIAtWorkObject;
        $employee->amIAtWork = $amIAtWork;
        return $employee;
    }

    public function forceStopTime(Request $request){
        $worktimingId = $request->input("forceStopMe");
        $Worktiming = WorkTiming::where("worktimingId", $worktimingId)->first();
        $Worktiming->workTimingEnd = time();
        $Worktiming->workTimingFinal = (time() - $Worktiming->workTimingStart);
        $Worktiming->save();

        return redirect()->back();
    }

    public function modifyWorktime(Request $request){
        $WorkTiming = WorkTiming::where("workTimingId", $request->input("workTimingId"))->first();
        $WorkTiming->workTimingStart = strtotime($request->input("workTimingStart"));
        $WorkTiming->workTimingEnd = strtotime($request->input("workTimingEnd"));
        $WorkTiming->workTimingFinal = $WorkTiming->workTimingEnd-$WorkTiming->workTimingStart;
        $WorkTiming->save();

        return redirect()->back();

    }

    public function translateEnglishMonthsName($toBeTranslated, $lang = "pl"){
        if($lang == "pl"){
            $toBeTranslated = str_replace("January", "Styczeń", $toBeTranslated);
            $toBeTranslated = str_replace("February", "Luty", $toBeTranslated);
            $toBeTranslated = str_replace("March", "Marzec", $toBeTranslated);
            $toBeTranslated = str_replace("April", "Kwiecień", $toBeTranslated);
            $toBeTranslated = str_replace("May", "Maj", $toBeTranslated);
            $toBeTranslated = str_replace("June", "Czerwiec", $toBeTranslated);
            $toBeTranslated = str_replace("July", "Lipiec", $toBeTranslated);
            $toBeTranslated = str_replace("August", "Sierpień", $toBeTranslated);
            $toBeTranslated = str_replace("September", "Wrzesień", $toBeTranslated);
            $toBeTranslated = str_replace("October", "Październik", $toBeTranslated);
            $toBeTranslated = str_replace("November", "Listopad", $toBeTranslated);
            $toBeTranslated = str_replace("December", "Grudzień", $toBeTranslated);
        }
        return $toBeTranslated;
    }

    public function settlementCreateBreak(Request $request){

        $paidPercent = $request->input("paidPercent");

        if($request->input("holidayType") == "holiday"){
            $paidPercent = 100;  // Paid 100 if holiday
        }

        // Check whether employee's job is full time or part time.
        $fullWorkHours = 8; // Default = full time job
        $User = User::where("id", $request->input("workTimingUserId"))->first(); // Get user data
        $fullWorkHours = ($fullWorkHours * $User->partTimeJob); // Calculate work time based on partTimeJob
        $fullHours = ($fullWorkHours * ($paidPercent / 100)); // Calculate really paid time (percent)

        $calculatedWorkminutes = "00";
        if($fullHours != floor($fullHours)){
            $partOfHour = $fullHours-floor($fullHours);
            $calculatedWorkminutes = ceil(60 * $partOfHour);
            $fullHours = floor($fullHours);
            if($calculatedWorkminutes < 10){
                $calculatedWorkminutes = "0".$calculatedWorkminutes;
            }
        }
        $calculatedWorkhours = (7+$fullHours); // Starts at 7:00 so end at 

        // Read start and end date
        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate");
        
        // Convert date to DateTime
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        $breakTable = [];
        
        // Loop through
        $currentDate = clone $startDateTime;
        while ($currentDate <= $endDateTime) {
            $dataset = [
                "workTimingStart" => 0,
                "workTimingEnd" => 0,
                "workTimingFinal" => 0,
                "workTimingHuman" => 0,
            ];
            // Is business day? (monday - friday)
            if ($currentDate->format("N") >= 1 && $currentDate->format("N") <= 5) {

                //  Is business day for sure? (not at holiday's list?)
                $currentDateStr = $currentDate->format("Y-m-d");
                $currentDateWildcard = "*-".$currentDate->format("m-d");
                if (!in_array($currentDateStr, $this->holidayDays) && !in_array($currentDateWildcard, $this->holidayDays)) {

                    // If not, insert start and end date (in timestamp)
                    $dataset["workTimingStart"] = strtotime($currentDate->format("Y-m-d") . " 07:00:00");
                    $dataset["workTimingEnd"] = strtotime($currentDate->format("Y-m-d") . " ".$calculatedWorkhours.":$calculatedWorkminutes:00");
                    $dataset["workTimingFinal"] = $dataset["workTimingEnd"] - $dataset["workTimingStart"];
                    $dataset["workTimingHuman"] = date("Y-m-d H:i:s", $dataset["workTimingStart"])." - ".date("Y-m-d H:i:s", $dataset["workTimingEnd"])." (".($dataset["workTimingFinal"]/3600)." h, fill ".$paidPercent."%, PartTimed: $fullWorkHours, Filled: $fullHours, Calculated time: $fullHours h $calculatedWorkminutes m)";

                }

            }

            // Save date if changed:
            if($dataset['workTimingFinal'] != 0){
                $breakTable[] = $dataset;
            }
            // Przejdź do następnego dnia
            $currentDate->modify("+1 day");
        }


        // Next thing is to write these data into database:
        foreach($breakTable as $breakItem){
            $WT = new WorkTiming();
            $WT->workTimingUserId = $request->input("workTimingUserId");
            $WT->workTimingType = "worktime";
            $WT->workTimingStart = $breakItem['workTimingStart'];
            $WT->workTimingEnd = $breakItem['workTimingEnd'];
            $WT->workTimingFinal = $breakItem['workTimingFinal'];
            $WT->workTimingMeta = json_encode([
                "breakType" => $request->input("holidayType"),
                "paidPercentCalculated" => (int) $paidPercent,
                "calculatedTime" => $fullHours.":".$calculatedWorkminutes.":00",
            ]); // Save additional info (not to loose format)
            $WT->save();
        }
        
        
        return redirect()->back();
    }

    private function isHolidayTimestamp($timestamp) {

        // Check whether is weekend:
        $dayOfWeek = date('N', $timestamp);
        if ($dayOfWeek == 6 || $dayOfWeek == 7) { return true; } // If the day of the week is 6 (Saturday) or 7 (Sunday), return true
        if (in_array(date('Y-m-d', $timestamp), $this->holidayDays)) return true; // If static day
        if (in_array(date('*-m-d', $timestamp), $this->holidayDays)) return true; // If anniversary day

        return false;
    }
    
}
