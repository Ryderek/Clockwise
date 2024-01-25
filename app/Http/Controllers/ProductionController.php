<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Order;
use App\Models\WorkTiming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WorkTimingController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\WorkTimingHistoryController;
use App\Http\Controllers\PermissionsController as PC;

class ProductionController extends Controller
{   
    private $authCardId = null;

    /**
     *  Construck auth check
     */
    public function __construct(){
        session_start();
        if(isset($_SESSION['authCardId'])){
            $sessionAuthCardId = $_SESSION['authCardId'];
        }else{
            $sessionAuthCardId = "";
        }
        session_write_close();
        if($sessionAuthCardId == ""){
            return redirect(route('production'));
        }else{
            $this->authCardId = $sessionAuthCardId;
        }
        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
        $this->relogMeToPrivateWorker($userId);
    }
    
    /**
    * Show production dashboard.
    */
    public function index(){
        if($this->authCardId == null){
            return redirect(route('production'));
        }
        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
        $User = Auth::user();
        $WTC = new WorkTimingController();
        $amIAtWork = $WTC->isEmployeeAtWork($this->authCardId);
        $canDeployOrders = PC::cp('deploy_orders');
        return view('production.dashboard',[
            "user" => $User,
            "authCardId" => $this->authCardId,
            "amIAtWork" => $amIAtWork,
            "canDeployOrders" => $canDeployOrders,
        ]);
    }

    /**
    * Show production dynamic dashboard
    */
    public function dynamicDashboard(Request $request){
        if(isset($_POST['processing'])){
            return redirect(route('production.processing'));
        }if(isset($_POST['detailing'])){
            return redirect(route('production.detailing'));
        }if(isset($_POST['deployment'])){
            // Clear user data to log in as specified employee
            $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
            Auth::logout();
            Auth::loginUsingId($userId);
            return redirect(route('admin.deployment'));
        }else if(isset($_POST['adminpanel'])){
            // Clear user data to log in as admin
            $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
            Auth::logout();
            return redirect(route('admin'));
        }else if(isset($_POST['logout'])){
            return $this->logout();
        }
    }
     
    /**
    * Show started worktimings.
    */
    public function processing(){
        if($this->authCardId == null){
            return redirect(route('production'));
        }
        $WTC = new WorkTimingController();
        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
        $Details = $WTC->getUnclosedRealWorkTimesByUserId($userId);
        $filledWithGroupOrdersInProduction = $this->fillWithGroupedDetails($Details);

        return view('production.processing.index', [
            "details" => $Details
        ]);
    }
     /**
    * Show started worktimings.
    */
    public function processingDone(Request $request){
        $WT = WorkTiming::find($request->input("workTimingId"));
        $WT->workTimingEnd = time();
        $WT->workTimingFinal = $request->input("detailsDone");
        $success = true;
        try{
            $WT->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
            $User = User::find($userId);
            $Role = new RoleController();
            $RoleDetails = $Role->getRoleBySlug($WT->workTimingRoleSlug);
            $detailId = $WT->workTimingRelatorId;
            $noteContent = "Pracownik ".$User->name." (ID: ".$User->id.") zakończył pracę nad procesem: ".$RoleDetails->roleProcess.". Detale obrobione: ".$WT->workTimingFinal;
            WorkTimingHistoryController::store($detailId, $noteContent, $User->id, $WT->workTimingFinal);
        }
        $this->checkWhetherProcessingIsTotallyDone($WT->workTimingRelatorId);

        return redirect(route('production.processing')); // Redirect to processing page
        //return $this->logout(); // Logout (client's wish)
    }

    /**
    * Show available works, to be done.
    */
    public function detailing(){
        $OC = new OrderController();
        $ordersInProduction = $OC->getOrdersInProduction();
        return view('production.detailing.index', [
            "orders" => $ordersInProduction
        ]);
    }

    /**
    * Show available details to be done in specified order.
    */
    public function detailingOrder($orderId){
        $OC = new OrderController();
        $Order = Order::find($orderId);
        return view('production.detailing.order', [
            "details" => $OC->getDetailsInProductionByOrderId($orderId),
            "order" => $Order,
            "orderId" => $orderId
        ]);
    }
    /**
    * Show available manufacturing options to be done in specified detail.
    */
    public function detailingDetail($orderId, $orderDetailId){
        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
        $WTC = new WorkTimingController();
        $RC = RoleController::getUserRoles($userId);
        $RC = $WTC->fillRolesWithDemand($RC, $orderDetailId);
        $RC = $WTC->fillRolesInProduction($RC, $orderDetailId);
        $manufacturingTypes = $WTC->getEstimatedWorkTimesByDetailId($orderDetailId);
        return view('production.detailing.detail', [
            "orderId" => $orderId,
            "orderDetailId" => $orderDetailId,
            "workTimings" => $manufacturingTypes,
            "userRoles" => $RC
        ]);
    } 
    /**
    * Show available manufacturing options to be done in specified detail.
    */
    public function detailingSave(Request $request){

        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);

        $parentWorkTimingId = $request->input('workTimingId');
        $request->request->remove('workTimingId');
        $parentWorkTiming = WorkTiming::find($parentWorkTimingId);
        $workTimingRoleSlug = $parentWorkTiming->workTimingRoleSlug;

        $request->merge(['workTimingUserId' => $userId]);
        $request->merge(['workTimingType' => 'real']);
        $request->merge(['workTimingStart' => time()]);
        $request->merge(['workTimingRoleSlug' => $workTimingRoleSlug]);
        $request->merge(['workTimingRelatorParentId' => $parentWorkTimingId]);
        $WorkTiming = new WorkTiming($request->all());
        $success = true;
        try{
            $WorkTiming->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            $User = User::find($userId);
            $Role = new RoleController();
            
            $RoleDetails = $Role->getRoleBySlug($workTimingRoleSlug);
            $detailId = $request->input('workTimingRelatorId');
            $noteContent = "Pracownik ".$User->name." (ID: ".$User->id.") ropoczął pracę nad procesem: ".$RoleDetails->roleProcess;
            WorkTimingHistoryController::store($detailId, $noteContent, $User->id, 0);
            return redirect(route("production.processing", ["successMessage" => "Pomyślnie utworzono zamówienie!"])); // Redirect to processing page
            // return $this->logout(); // Logout (client's wish)
        }else{
            return "<pre>".$errorMessage."</pre>";
        }
        
    }

    public function detailingByEAN(Request $request){

        $detailEan = $request->input('detailUniqueCode');
        $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
        $RC = RoleController::getUserRoles($userId);
        $WTC = new WorkTimingController();
        $ODC = new OrderDetailController();
        $Detail = $ODC->getDetailInfoBasedOnEanCode($detailEan)->first();
        if(isset($Detail->orderDetailId)){
            $RC = $WTC->fillRolesInProduction($RC, $Detail->orderDetailId);
            $RC = $WTC->fillRolesWithDemand($RC, $Detail->orderDetailId, true);
            $manufacturingTypes = $WTC->getEstimatedWorkTimesByDetailId($Detail->orderDetailId);
            return view('production.detailing.detail', [
                "orderId" => $Detail->orderDetailOrderId,
                "orderDetailId" => $Detail->orderDetailId,
                "workTimings" => $manufacturingTypes,
                "userRoles" => $RC
            ]);
        }else{
            return redirect(route("production.dashboard", ["errorMessage" => "Podany kod '".$detailEan."' jest niepoprawny!"]));
        }
    }

    public function fillWithGroupedDetails($orderDetails){
        $WTC = new WorkTimingController();
        foreach($orderDetails as $orderDetail){
            $detailId = $orderDetail->workTimingRelatorId;
            $roleSlug = $orderDetail->roleSlug;
            $orderDetail->orderDetailItemsGrouped = $WTC->getSumOfDoneRealWorkTimesByDetailAndRoleSlug($detailId, $roleSlug, $orderDetail->workTimingRelatorParentId);
        }
        return $orderDetails;
    }

    public function switchWorktime(Request $request){
        if($request->input('authCardCode') == $this->authCardId){
            $WTC = new WorkTimingController();
            $switchStatus = $WTC->switchEmployeeWorktime($this->authCardId);
        }
        return redirect(route('production'));
    }


    /**
    * Logout 
    */
    private function logout(){
        session_start();
        unset($_SESSION['authCardId']);
        session_write_close();
        $this->relogMeToPrivateWorker(1);
        return redirect(route('production'));
    }
    public function logProductionOut(){
        return $this->logout();
    }

    private function checkWhetherProcessingIsTotallyDone($detailId){
        $WTC = new WorkTimingController();
        $pivotTable = $WTC->getPivotTableOfDetailId($detailId);
        $lowestValue = $WTC->getLowestItemValuesFromPivotTable($pivotTable);
        $WTC->saveTotalDetailsDone($detailId, $lowestValue);
    }

    /**
     * Statically verify whether authcode is valid
     */
    public static function verify($authCardId){
        $authcardExist = DB::table("auth_cards")->select("authCardUserId")->where("authCardCode", $authCardId)->first();
        if($authcardExist != null){
            $_SESSION['authCardId'] = $authcardExist;
            return true;
        }else{
            return false;
        }
    }

    public function relogMeToPrivateWorker($workerId){
        Auth::logout();
        Auth::loginUsingId($workerId);
    }
    
}
