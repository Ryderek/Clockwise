<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkTiming;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthCardController;
use App\Http\Controllers\PermissionsController as PC;
use DB;

class WorkTimingController extends Controller
{   
    /**
     * Create estimated time for specified detail
     */
    public function defineEstimated(Request $request){
        if(!PC::cp('create_orders')){
            abort(403);
        }
        $request->merge(['workTimingUserId' => Auth::id()]);
        $request->merge(['workTimingType' => "estimated"]);
        //$request->merge(['workTimingFinal' => $request->workTimingEstimatedTime]);
        $request->merge(['workTimingFinal' => 0]);
        $request->request->remove('workTimingEstimatedTime');
        return $this->defineWorkTime($request);
    }

    /**
     * Create workTiming for specified detail
     */
    public function defineWorkTime($request){
        $WorkTiming = new WorkTiming($request->all());
        $WorkTiming->workTimingRelatorParentId = $request->input("orderId");
        $success = true;
        try{
            $WorkTiming->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success == true){
            // Update work timings (divide per each matching record)
            $complexArray = $this->getComplexArray($request->input("orderId"));
            $estimatedCount = DB::table('work_timings')->selectRaw("count(*) as cnt")->where("workTimingType", 'estimated')->where('workTimingRelatorId', $request->input("workTimingRelatorId"))->where('workTimingRoleSlug', $request->input("workTimingRoleSlug"))->first();
            if($estimatedCount->cnt != 0){
                DB::table('work_timings')->where("workTimingType", 'estimated')->where('workTimingRelatorId', $request->input("workTimingRelatorId"))->where('workTimingRoleSlug', $request->input("workTimingRoleSlug"))->update(
                    array(
                        'workTimingFinal' => ceil( ((int) $complexArray[$request->input("workTimingRoleSlug")] * 60) / ( (int) $estimatedCount->cnt) ),
                    )
                );
            }
            return redirect(route("detail.edit", ["id" => $request->input("workTimingRelatorId"), "successMessage" => "Pomyślnie dodano proces obróbki!"]));
        }else{
            return redirect(route("detail.edit", ["id" => $request->input("workTimingRelatorId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Get estimated times specified
     */
    public function getEstimatedWorkTimesByDetailId($detailId, $agregateRedundancy = false){
        if($agregateRedundancy == false){
            return DB::table('work_timings')->select("*")->where("workTimingType", "estimated")->whereRaw("work_timings.workTimingRelatorId = $detailId")->join("roles", "workTimingRoleSlug", "=", "roles.roleSlug")->whereRaw("roles.roleIsActive = 1")->get();
        }else{
            return DB::table('work_timings')->selectRaw("*, SUM(workTimingFinal) as workTimingFinal")->where("workTimingType", "estimated")->whereRaw("work_timings.workTimingRelatorId = $detailId")->groupBy("workTimingRoleSlug")->join("roles", "workTimingRoleSlug", "=", "roles.roleSlug")->whereRaw("roles.roleIsActive = 1")->get();
        }
    }

    /**
     * Get real times by user
     */
    public function getUnclosedRealWorkTimesByUserId($userId){
        return DB::table('work_timings')->select("*")->where("workTimingType", "real")->whereRaw("workTimingEnd IS NULL")->whereRaw("work_timings.workTimingUserId = $userId")->join("roles", "workTimingRoleSlug", "=", "roles.roleSlug")->whereRaw("roles.roleIsActive = 1")->join("order_details", "work_timings.workTimingRelatorId", "=", "order_details.orderDetailId")->get();
    }

    public function getSumOfDoneRealWorkTimesByDetailAndRoleSlug($detailId, $roleSlug, $uncollapseParent = false){
        if($uncollapseParent == false){
            $objCount = DB::table('work_timings')->selectRaw("SUM(workTimingFinal) AS workTimingGrouped")->whereRaw("workTimingType = 'real'")->where("workTimingRelatorId", $detailId)->whereRaw("workTimingRoleSlug = '$roleSlug'")->first()->workTimingGrouped;
            if($objCount == NULL){
                $objCount = 0;
            }
        }else{
            $objCount = DB::table('work_timings')->selectRaw("SUM(workTimingFinal) AS workTimingGrouped")->whereRaw("workTimingType = 'real'")->where("workTimingRelatorId", $detailId)->whereRaw("workTimingRoleSlug = '$roleSlug'")->where('workTimingRelatorParentId', $uncollapseParent)->get();
            if(
                $objCount == NULL ||
                $objCount[0] == NULL ||
                $objCount[0]->workTimingGrouped == NULL
            ){
                $objCount = 0;
            }else{
                $objCount = $objCount[0]->workTimingGrouped;
            }
        }
        return $objCount;
    }

    /**
     * Fill roles with time demand
     */
    public function fillRolesWithDemand($roles, $orderDetailId, $invertedLogic = false){
        if($invertedLogic === false){
            $OD = OrderDetail::find($orderDetailId);
            $total = $OD->orderDetailItemsTotal;
            foreach($roles as $role){
                $role->roleDemanding =  ($total - $this->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($orderDetailId, $role->roleSlug));
            }
        }elseif($invertedLogic === true){
            $OD = OrderDetail::find($orderDetailId);
            $total = $OD->orderDetailItemsTotal;
            $EWT = $this->getEstimatedWorkTimesByDetailId($orderDetailId, false);
            $newRoles = collect();
            foreach($roles as $role){
                foreach($EWT as $ewtItem){
                    if($role->roleSlug == $ewtItem->workTimingRoleSlug){
                        $role->roleDemanding =  ($total - $this->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($orderDetailId, $role->roleSlug, $ewtItem->workTimingId));
                        foreach($role as $key => $value){
                            $ewtItem->$key = $value;
                        }
                        $newRoles->add($ewtItem);
                    }
                }
            }
            $roles = $newRoles;
        }else{
            $OD = OrderDetail::find($orderDetailId);
            $total = $OD->orderDetailItemsTotal;
            foreach($roles as $role){
                $role->roleDemanding =  ($total - $this->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($orderDetailId, $role->roleSlug, $invertedLogic));
            }
        }
        //dd($roles);
       
        return $roles;
    }
    /**
     * Fill workTimings with three descriptors; detailId, workTiminType = real and workTimingSlug
     */
    private function getRealWorkTimingsWithTwoDescriptors($detailId, $roleSlug, $onlyDone = false){
        if($onlyDone){
            $output = DB::table('work_timings')->select("*")->whereRaw("workTimingType = 'real'")->whereRaw("workTimingRelatorId = $detailId")->whereRaw("workTimingRoleSlug = '$roleSlug'")->whereRaw("workTimingEnd is NOT NULL")->join('users', 'users.id', '=', 'work_timings.workTimingUserId')->get();
        }else{
            $output = DB::table('work_timings')->select("*")->whereRaw("workTimingType = 'real'")->whereRaw("workTimingRelatorId = $detailId")->whereRaw("workTimingRoleSlug = '$roleSlug'")->join('users', 'users.id', '=', 'work_timings.workTimingUserId')->get();
        }
        return $output;
    }
    /**
     * Get estimated times,  filled with 
     */
    public function getPivotTableOfDetailId($detailId, $onlyDone = false, $agregateRedundancy = false, $uncollapseParents = false){
        $EWT = $this->getEstimatedWorkTimesByDetailId($detailId, $agregateRedundancy);
        foreach($EWT as $ewtItem){ // For each estimated time, count real time and display outputs
            if($uncollapseParents == false){
                $ewtItem->realTime = $this->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($detailId, $ewtItem->workTimingRoleSlug, false);
            }else{
                $ewtItem->realTime = $this->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($detailId, $ewtItem->workTimingRoleSlug, $ewtItem->workTimingId);
            }
            $ewtItem->realTimesArray = $this->getRealWorkTimingsWithTwoDescriptors($detailId, $ewtItem->workTimingRoleSlug, $onlyDone);
        }
        return $EWT;
    }

    public function getLowestItemValuesFromPivotTable($pivotTable){
        $lowest = 999999999;
        foreach($pivotTable as $item){
            if($item->realTime < $lowest){
                $lowest = $item->realTime;
            }
        }
        return $lowest;
    }

    /**
     * Save value of total done (function to be executed only if details are "finally" (100%) ready.)
     */
    public function saveTotalDetailsDone($detailId, $numberOfDetails){
        $OD = OrderDetail::find($detailId);
        $OD->orderDetailItemsDone = $numberOfDetails;
        return $OD->save();
    }

    public function fillRolesInProduction($Roles, $orderDetailId){
        foreach($Roles as $role){
            $role->currentInProduction = DB::table('work_timings')->selectRaw("*")->whereRaw("workTimingType = 'real'")->where("workTimingRelatorId", $orderDetailId)->whereRaw("workTimingEnd IS NULL")->where("workTimingRoleSlug", $role->roleSlug)->get();
        }
        return $Roles;
    }

    public function getSumOfRealTimeForDetail($detailId){
        return DB::table('work_timings')->selectRaw("SUM(workTimingEnd - workTimingStart) as `totalSeconds`, `workTimingRoleSlug`")->where("workTimingType", "real")->where("workTimingRelatorId", $detailId)->groupBy("workTimingRoleSlug")->get();
    }
    public function getSumOfEstimatedTimeForDetail($detailId){
        return DB::table('work_timings')->selectRaw("SUM(workTimingFinal*60) as `totalSeconds`, `workTimingRoleSlug`")->where("workTimingType", "estimated")->where("workTimingRelatorId", $detailId)->groupBy("workTimingRoleSlug")->get();
    }

    public function switchEmployeeWorktime($authCardId){ // Switch employee worktime
        $ACC = new AuthCardController();
        $userId = $ACC->getUserIdByAuthcardId($authCardId);
        $isEmployeeAtWork = $this->isEmployeeAtWork($authCardId);
        // Check whether user is logged or not:
        if($isEmployeeAtWork != false && $isEmployeeAtWork != null){
            $workTimingId = $isEmployeeAtWork->workTimingId;
            $currentTime = time();
            $successFinal = true;
            try{
                $WorkTiming = WorkTiming::where("worktimingId", $workTimingId)->first();
                $WorkTiming->workTimingEnd = $currentTime;
                $WorkTiming->workTimingFinal = $currentTime - $WorkTiming->workTimingStart;
                $WorkTiming->save();
            }catch(\Throwable $e){
                $successFinal = false;
                $errorMessage = $e;
            }
            return $successFinal;
        }else if($isEmployeeAtWork == false){
            $successFinal = true;
            try{
                $loaded = DB::table('work_timings')->insert([
                    'workTimingUserId' => $userId,
                    'workTimingType' => 'worktime',
                    'workTimingStart' => time()
                ]);
            }catch(\Throwable $e){
                $successFinal = false;
                $errorMessage = $e;
            }
            return $successFinal;
        }
    }

    public function isEmployeeAtWork($identifier, $identifierType = "authcard"){ // Switch employee worktime
        $ACC = new AuthCardController();
        if($identifierType == "authcard"){
            $userId = $ACC->getUserIdByAuthcardId($identifier);
        }else if($identifierType == "userId"){
            $userId = $identifier;
        }

        // Check whether user is logged or not:
        $getWorktimeQuery = null;
        $success = true;
        try{
            $getWorktimeQuery = DB::table("work_timings")->select('*')->where("workTimingUserId", $userId)->whereRaw("workTimingType ='worktime'")->orderBy("workTimingId", 'DESC')->first();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            if($getWorktimeQuery == null || ($getWorktimeQuery != null && $getWorktimeQuery->workTimingEnd != null)){
                // Employee isn't at the job.
                return false;
            }else{
                // Employee is at the job now!
                return $getWorktimeQuery;
            }
        }else{
            return null;
        }
    }
    /**
     * Get complex worktimings by order id
     */
    private function getComplexByOrderId($orderId){
        return DB::table("work_timings")->select('*')->where("workTimingType", "complex")->where("workTimingRelatorId", $orderId)->get();
    }

    /**
     * Function that fills roles with complex 
     */
    private function combineRolesAndComplex($Roles, $Complex){
        $combinedObjects = array();
        foreach($Complex as $Compl){
            foreach($Roles as $Role){
                $currentCombined = array();
                if($Compl->workTimingRoleSlug == $Role->roleSlug){
                    foreach($Compl as $key => $value){
                        $currentCombined[$key] = $value;
                    }
                    foreach($Role as $key => $value){
                        $currentCombined[$key] = $value;
                    }
                    array_push($combinedObjects, $currentCombined);
                }
            }
        }
        return $combinedObjects;
    }

    /**
     * Get complex worktimings (complex = defined per order)
     */
    public function getComplexWorkTimings($orderId){
        $Roles = RoleController::getActiveRoles(true);
        $Complex = $this->getComplexByOrderId($orderId);
        $combinedRoleComplex = $this->combineRolesAndComplex($Roles, $Complex);
        return $combinedRoleComplex;
    }

    public function getComplexArray($orderId){
        $complexTimes = DB::table('work_timings')->selectRaw("workTimingFinal, workTimingRoleSlug")->where("workTimingType", 'complex')->where('workTimingRelatorId', $orderId)->get();
        $complexArray = array();
        foreach($complexTimes as $complexTime){
            $complexArray[$complexTime->workTimingRoleSlug] = $complexTime->workTimingFinal;
        }
        return $complexArray;
    }
    
}
