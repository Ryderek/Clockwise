<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Order;
use Milon\Barcode\DNS1D;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HumanController;
use App\Http\Controllers\WorkTimingController;
use App\Http\Controllers\PermissionsController as PC;
use App\Http\Controllers\WorkTimingHistoryController;

class OrderDetailController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create($orderId){
        if(!PC::cp('create_orders')){
            abort(403);
        }
        return view("admin.details.partials.create-modal", [
            "orderId" => $orderId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        if(!PC::cp('create_orders')){
            abort(403);
        }
        $request->request->remove('fakeUsernameAutofill');
        $request->merge(['orderDetailUniqueId' => $this->createDetailId()]);
        $request->merge(['orderDetailOrderNumber' => $this->getLastFreeOrderNumberOfCurrentOrder($request->input("orderDetailOrderId"))]);
        
        $OrderDetail = new OrderDetail($request->all());

        // Replace "on" with value 0 or 1 (to fit database)
        if($OrderDetail->orderDetailCooperation == "on"){
            $OrderDetail->orderDetailCooperation = 1;
        }else{
            $OrderDetail->orderDetailCooperation = 0;
        }
        
        $success = true;
        try{
            $OrderDetail->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success == true){
            if(isset($_POST['newWorkTiming'])){
                $WTC = new WorkTimingController();
                $complexArray = $WTC->getComplexArray($OrderDetail->orderDetailOrderId);
                foreach($_POST['newWorkTiming'] as $newWorkTiming){
                    if(strlen($newWorkTiming) != 0){
                        $estimatedCount = DB::table('work_timings')->selectRaw("count(*) as cnt")->where("workTimingType", 'estimated')->where('workTimingRelatorId', $OrderDetail->orderDetailId)->where('workTimingRoleSlug', $newWorkTiming)->first();
                        DB::table('work_timings')->insert(
                            array(
                                'workTimingUserId' => Auth::id(),
                                'workTimingRelatorId' => $OrderDetail->orderDetailId,
                                'workTimingRelatorParentId' => $OrderDetail->orderDetailOrderId,
                                'workTimingType' => 'estimated',
                                'workTimingRoleSlug' => $newWorkTiming,
                                'workTimingFinal' => $complexArray[$newWorkTiming],
                            )
                        );
                        if($estimatedCount->cnt != 0){
                            DB::table('work_timings')->where("workTimingType", 'estimated')->where('workTimingRelatorId', $OrderDetail->orderDetailId)->where('workTimingRoleSlug', $newWorkTiming)->update(
                                array(
                                    'workTimingFinal' => ceil( ((int) $complexArray[$newWorkTiming] * 60) / ( (int) $estimatedCount->cnt + 1) ),
                                )
                            );
                        }
                    }
                }
            }
            return redirect(route("order.edit", ["id" => $request->input("orderDetailOrderId"), "successMessage" => "Pomyślnie utworzono detal!"]));
            ////return redirect(route("detail.edit", ["id" => $OrderDetail->orderDetailId, "successMessage" => "Pomyślnie utworzono detal!"]));
        }else{
            return redirect(route("order.edit", ["id" => $request->input("orderDetailOrderId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        $OrderDetail = OrderDetail::find($id);
        $Notes = $this->getNotesByDetailId($id);
        $Attachments = $this->getAttachmentsByDetailId($id);
        $BarCode = $this->generateBarCode($OrderDetail->orderDetailUniqueId);
        $Roles = DB::table("roles")->select("*")->where("roleIsActive", "1")->get();
        $EstimatedWorkTimings = $this->getWorkTimingPivotTable($OrderDetail, false, false, true);
        $Order = Order::find($OrderDetail->orderDetailOrderId);
        
        return view("admin.details.edit", [
            "detail" => $OrderDetail,
            "notes" => $Notes,
            "attachments" => $Attachments,
            "barcode" => $BarCode,
            "roles" => $Roles,
            "estimatedWorkTimings" => $EstimatedWorkTimings,
            "WorkTimeHistory" => $this->getHistoryInOrder($id),
            "order" => $Order
        ]);
    }
    
    /**
     * Update specified resource.
     */
    public function update(Request $request){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        $errorMessage = "";
        $success = true;
        try{
            $OrderDetail = OrderDetail::find($request->input("orderDetailId"));
            $OrderDetail->fill($request->all());

            // Replace "on" with value 0 or 1 (to fit database)
            if($OrderDetail->orderDetailCooperation == "on"){
                $OrderDetail->orderDetailCooperation = 1;
            }else{
                $OrderDetail->orderDetailCooperation = 0;
            }
            
            $OrderDetail->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("detail.edit", ["id" => $request->input("orderDetailId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("detail.edit", ["id" => $request->input("orderDetailId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Get history in order
     */
    private function getHistoryInOrder($detailId){
        return WorkTimingHistoryController::showHistoryOfDetail($detailId);
    }

    /**
     * Create unique identifier for detail
     */
    private function createDetailId(){
        $polishBase = 589 + (int) substr(time(), 0, 1);
        $digits = substr(time(), 1);
        $digits = $polishBase.$digits; // 12 digits, count checksum:
        $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9] + $digits[11];
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum / 10)) * 10;
        $check_digit = $next_ten - $total_sum;
        $key = $digits.$check_digit;
        return $key;
    }

    /**
     * Create unique identifier for detail
     */
    public function createEanEightId($Detail, $orderEanPostfix){
        $orderEanPrefix = substr($Detail->orderDetailUniqueId, -3); // 2 digits$orderEanPostfix
        $orderEanLastDigit = (int) substr($orderEanPostfix, -1);

        // Fix digit positions ex. "7" => "007" // Fix variable lenght
        if(strlen($orderEanPostfix) == 1){
            $orderEanPostfix = "00".$orderEanPostfix;
        }else if(strlen($orderEanPostfix) == 2){
            $orderEanPostfix = "0".$orderEanPostfix;
        }

        $forceLoopBreak = false;
        $currentEanSeven = null;
        $currentEanEight = null;
        for($b=0; $b<10; $b++){
            if($forceLoopBreak == false){
                for($a=0; $a<10; $a++){
                    $middleDigits = (int) $a.$b;
                    $currentEanSeven = substr($orderEanPrefix.$middleDigits.$orderEanPostfix, 0, 7);
                    if($this->calculateEAN8Checksum($currentEanSeven) == $orderEanLastDigit){
                        $forceLoopBreak = true;
                        $currentEanEight = (int) $currentEanSeven.$orderEanLastDigit;
                        break;
                    }
                }
            }else{
                break;
            }
        }
            

        return $currentEanEight;
    }

    /**
     * Calculate ean8 checksum
     */
    function calculateEAN8Checksum($ean8) {
        $code = str_split($ean8);
    
        foreach ($code as $digit) {
            if ($digit < 0) {
                return false;
            }
        }
    
        $sum1 = $code[1] + $code[3] + $code[5];
        $sum2 = 3 * ($code[0] + $code[2] + $code[4] + $code[6]);
    
        $checksum_digit = (10 - ($sum1 + $sum2) % 10) % 10;
        if ($checksum_digit == 10) {
            $checksum_digit = 0;
        }
    
        return $checksum_digit;
    }

    /**
     * Get detail notes
     */
    private function getNotesByDetailId($id){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        return DB::table("notes")->select("noteId", "noteTitle", "updated_at")->where("noteRelatorId", $id)->where("noteRelatorSlug", "detail")->get();
    }

    /**
     * Get detail attachments
     */
    private function getAttachmentsByDetailId($id){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        return DB::table("attachments")->select("attachmentId", "attachmentTitle", "attachmentPath", "updated_at")->where("attachmentRelatorId", $id)->where("attachmentRelatorSlug", "detail")->get();
    }

    /**
     * Generate bar code
     */
    private function generateBarCode($code, $standard = "EAN13"){
        return html_entity_decode(DNS1D::getBarcodeHTML($code, $standard), ENT_QUOTES, "UTF-8");
    }

    /**
     * Get works count based on work times
     */
    public function getWorkTimingPivotTable($OrderDetail, $onlyDone = false, $agregateRedundancy = false, $uncollapseParents = false){
        $WTC = new WorkTimingController();
        $EstimatedWorkTimings = $WTC->getPivotTableOfDetailId($OrderDetail->orderDetailId, $onlyDone, $agregateRedundancy, $uncollapseParents);
        foreach($EstimatedWorkTimings as $EstimatedWorkTiming){
            $EstimatedWorkTiming->totalTimeInHours = floor(($EstimatedWorkTiming->workTimingFinal * $OrderDetail->orderDetailItemsTotal)/60);
            $EstimatedWorkTiming->totalTimeInMinutes = (($EstimatedWorkTiming->workTimingFinal * $OrderDetail->orderDetailItemsTotal)/60 - floor(($EstimatedWorkTiming->workTimingFinal * $OrderDetail->orderDetailItemsTotal)/60)) * 60;
            $EstimatedWorkTiming->totalTimeInHuman = HumanController::convertMinutesToHours($EstimatedWorkTiming->workTimingFinal * $OrderDetail->orderDetailItemsTotal);
        }
        return $EstimatedWorkTimings;
    }

    public function getDetailInfoBasedOnEanCode($eanCode){
        return DB::table('order_details')->select("*")->where('orderDetailUniqueId', $eanCode);
    }

    /**
     * Count details by order id
     */
    public function getDetailsCountBasedOnOrderId($orderId){
        return DB::table('order_details')->selectRaw("count(*) as detailsCount")->where('orderDetailOrderId', $orderId)->first()->detailsCount;
    }

    /**
     * Soft delete order
     */
    function softDelete($orderDetailId){
        // Alias for hard delete (not recommended)
        return $this->hardDelete($orderDetailId);
    }

    /**
     * Hard delete order
     */
    function hardDelete($orderDetailId){
        $orderDetailId = (int) $orderDetailId;
        $OrderDetail = OrderDetail::where('orderDetailId', $orderDetailId)->first();
        //dd($orderDetailId, $OrderDetail);
        $OrderId = $OrderDetail->orderDetailOrderId;
        $Order = Order::find($OrderId);
        if($Order->orderStatus == 'created'){ // Only when not published
            $currentOrderNumber = $OrderDetail->orderDetailOrderNumber;
            $OrderDetail->delete();
            DB::statement("UPDATE `order_details` SET orderDetailOrderNumber = orderDetailOrderNumber-1 WHERE `orderDetailOrderId` = $OrderId && `orderDetailOrderNumber` > $currentOrderNumber");
        }
        return redirect(route("order.edit", ["id" => $OrderId, "successMessage" => "Pomyślnie usunięto detal $orderDetailId ".$OrderDetail->orderDetailId." ze zlecenia $OrderId."]));
    }

    /**
     * Get all current active worktimings (active manufacturing)
     */
    public function getDetailsInProgress(){
        return DB::table('work_timings')->select("*")->where("workTimingType", "real")->whereRaw("workTimingStart IS NOT NULL")->whereRaw("workTimingEnd IS NULL")->whereRaw("workTimingFinal IS NULL")->join("roles", "roleSlug", "=", "workTimingRoleSlug")->join("order_details", "workTimingRelatorId", "=", "orderDetailId")->join("users", "workTimingUserId", "=", "id")->get();
    }

    public function getLastFreeOrderNumberOfCurrentOrder($orderId){
        $getTheNum = DB::table('order_details')->select("orderDetailOrderNumber")->where('orderDetailOrderId', $orderId)->orderBy('orderDetailOrderNumber','DESC')->first();
        if(isset($getTheNum->orderDetailOrderNumber)){
            return ( (int) $getTheNum->orderDetailOrderNumber + 1 );
        }else{
            return 1;
        }
    }

    public function generateEanEightCodes($Detail, $duplicateDetail = true){
        $CompleteDetails = array();
        for($a=1; $a<=$Detail->orderDetailItemsTotal; $a++){
            $PartialDetails = array();
            if($duplicateDetail){
                $PartialDetails["Detail"] = $Detail;
            }
            $ean8Id = $this->createEanEightId($Detail, $a);
            $PartialDetails["EanEightId"] = $ean8Id;
            $PartialDetails["EanEightIdHtml"] = DNS1D::getBarcodeHTML($ean8Id, "EAN8");
            $PartialDetails["LP"] = $a;
            array_push($CompleteDetails, $PartialDetails);
        }
        return $CompleteDetails;
    }
}
