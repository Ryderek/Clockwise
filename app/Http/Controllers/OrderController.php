<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\WorkTiming;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkTimingController;
use App\Http\Controllers\WorkTimingHistoryController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\PermissionsController as PC;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($page = 0, $errorMessage = ""){ 
        if(!PC::cp('view_orders')){
            abort(403);
        }
        // Handle GET main page
        $sortOrder = array(
            /* array("-orderConfirmedTime", "DESC"),
            array("-orderPublishedTime", "DESC"),
            array("-orderDoneTime", "DESC"), */
            array("orderDeadline", "ASC")
        );
        $getPageObject = $this->getPage($page, '*', "", $sortOrder);
        $orders = $getPageObject["objects"];
        $orders = $this->fillOrdersWithDeadlineDaysLeft($orders);
        $orders = $this->fillOrdersWithCustomers($orders);
        $orders = $this->fillOrdersWithOperatorsNames($orders);
        $orders = $this->fillOrdersWithWTs($orders);
        $orders = $this->fillOrdersWithTotalWorkTimings($orders); // Warning: Takes a lof of time to calculate!
        $roles = RoleController::getActiveRoles(true);
        return view("admin.orders.index", [
            "orders" => $orders,
            "pageHeader" => "Zlecenia",
            "currentPage" => ($page+1),
            "totalPages" => $getPageObject['totalPages'],
            "roles" => $roles,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".orders"
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        if(!PC::cp('create_orders')){
            abort(403);
        }
        $RC = new RoleController();
        $customers = DB::table('customers')->select("customerName", "customerId")->groupBy('customerTaxIdentityNumber')->orderBy('customerId', 'DESC')->get();
        $activeRoles = $RC->getActiveRoles(true);
        return view("admin.orders.create",[
            'contractors' => $customers,
            'roles' => $activeRoles,
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
        $Customer = new Customer($request->all());
        $insertCustomerSuccess = true;
        try{
            $Customer->save();
        }catch(\Throwable $e){
            $insertCustomerSuccess = false;
            $errorMessage = $e;
        }
        if($insertCustomerSuccess == true){
            $insertOrderSuccess = true;
            $request->merge(['orderCustomer' => $Customer->customerId]);
            $request->merge(['orderCreatedBy' => Auth::id()]);
            $request->merge(['orderCreatedTime' => date('Y-m-d H:i:s')]);
            $Order = new Order($request->all());
            try{
                $Order->save();
            }catch(\Throwable $e){
                $insertOrderSuccess = false;
                $errorMessage = $e;
            }
            if($insertOrderSuccess){
                foreach($_POST['complexTime'] as $roleSlug => $timesInHours){
                    DB::table('work_timings')->insert(
                        array(
                            'workTimingUserId' => Auth::id(),
                            'workTimingType' => 'complex',
                            'workTimingRelatorId' => $Order->orderId, // in 'complex' is equal to order id, not detail id
                            'workTimingRelatorParentId' => $Order->orderId,
                            'workTimingRoleSlug' => $roleSlug,
                            'workTimingFinal' => abs($timesInHours),
                        )
                    );
                }
                return redirect(route("order.edit", ["id" => $Order->orderId, "successMessage" => "Pomyślnie utworzono zamówienie!"]));
            }else{
                return redirect(route("order.create", ["errorMessage" => $errorMessage]));
            }
        }else{
            return redirect(route("order.create", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        $WTC = new WorkTimingController();
        $ODC = new OrderDetailController();
        $Order = Order::find($id);
        $Customer = Customer::find($Order->orderCustomer);
        $Notes = $this->getNotesByOrderId($id);
        $Attachments = $this->getAttachmentsByOrderId($id);
        $Details = $this->getDetailsByOrderId($id);
        $Roles = RoleController::getActiveRoles(true);
        $Order = $this->fillSingleOrderWithWorkTimings($Order); // Warning: Takes a lof of time to calculate!
        foreach($Details as $Detail){ // This takes a lof of time aswell
            $Detail->eWT = $ODC->getWorkTimingPivotTable($Detail, false, true);
        }
        $complexWTs = $WTC->getComplexWorkTimings($id);
        return view("admin.orders.edit", [
            "order" => $Order,
            "customer" => $Customer,
            "notes" => $Notes,
            "attachments" => $Attachments,
            "details" => $Details,
            "roles" => $Roles,
            "complexWTs" => $complexWTs
        ]);
    }

    
    /**
     * Update order info
     */
    public function update(Request $request){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        $WTC = new WorkTimingController();
        if(isset($_POST['orderStatusLight'])){
            $request->merge(['orderStatusLight' => 1]);
        }else{
            $request->merge(['orderStatusLight' => 0]);
        }
        
        if(isset($_POST['orderCooperated'])){
            $request->merge(['orderCooperated' => 1]);
        }else{
            $request->merge(['orderCooperated' => 0]);
        }
        $errorMessage = "";
        try{        
            $Order = Order::find($request->input("editOrderId"));
            $Order->fill($request->all())->save();
            $Customer = Customer::find($request->input("editOrderId"));
            $Customer->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            foreach($_POST['complexTime'] as $workTimingId => $timesInHours){
                $WorkTiming = WorkTiming::find($workTimingId);
                $WorkTiming->workTimingFinal = $timesInHours;
                $WorkTiming->save();
                $complexArray = $WTC->getComplexArray($Order->orderId);
                $estimatedCount = DB::table('work_timings')->selectRaw("count(*) as cnt")->where("workTimingType", 'estimated')->where('workTimingRelatorParentId', $WorkTiming->workTimingRelatorId)->where('workTimingRoleSlug', $WorkTiming->workTimingRoleSlug)->first();
                if($estimatedCount->cnt != 0){
                    DB::table('work_timings')->where("workTimingType", 'estimated')->where('workTimingRelatorParentId', $WorkTiming->workTimingRelatorId)->where('workTimingRoleSlug', $WorkTiming->workTimingRoleSlug)->update(
                        array(
                            'workTimingFinal' => ceil( ((int) $complexArray[$WorkTiming->workTimingRoleSlug] * 60) / ( (int) $estimatedCount->cnt) ),
                        )
                    );
                }else{
                    DB::table('work_timings')->where("workTimingType", 'estimated')->where('workTimingRelatorParentId', $WorkTiming->workTimingRelatorId)->where('workTimingRoleSlug', $WorkTiming->workTimingRoleSlug)->update(
                        array(
                            'workTimingFinal' => $complexArray[$WorkTiming->workTimingRoleSlug],
                        )
                    );
                }
            }
            return redirect(route("order.edit", ["id" => $request->input("editOrderId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("order.edit", ["id" => $request->input("editOrderId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Display paged listing of the resource.
     */
    public function getPage($page = 0, $columns = '*', $where = "", $orders = null){ 
        if(!PC::cp('view_orders')){
            abort(403);
        }
        // Get count of all items meeting the condition:
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $Orders = DB::table('orders')->select($columns)->take($itemsPerPage);
        $TotalOrders = DB::table('orders')->selectRaw("count(*) as counter");

        if($page != 0){
            $page--;
            $Orders = $Orders->skip($page*$itemsPerPage);
        }
        if(strlen($where) != 0){
            $Orders = $Orders->whereRaw($where);
            $TotalOrders = $TotalOrders->whereRaw($where);
        }
        if($orders != null){
            foreach($orders as $order){
                if($order[1] != "ASC" && $order[1] != "DESC"){
                    $order[1] = "ASC";
                }
                if(substr($order[0], 0, 1) == '-'){      
                    $withoutMinus = ltrim($order[0], '-');
                    $orderByRaw = '-`'.$withoutMinus.'`'." ".$order[1];
                    $Orders = $Orders->orderByRaw($orderByRaw); 
                }else{
                    $Orders = $Orders->orderBy($order[0], $order[1]);
                }   
            }
        }
        $Orders = $Orders->get();
        $TotalOrders = $TotalOrders->first()->counter;

        $returnObject = [
            "objects" => $Orders,
            "currentPage" => $page,
            "totalPages" => (int) ceil($TotalOrders/$itemsPerPage),
        ];
        return $returnObject;
    }

    /**
     * Display paged listing of the resource by status
     */
    public function indexByStatus(string $status, $page = 0){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        $avaliableStatuses = [
            "created" => [
                "status" => "created",
                "header" => "Utworzone zlecenia",
            ],
            "confirmed" => [
                "status" => "confirmed",
                "header" => "Zatwierdzone zlecenia"
            ],
            "in-production" => [
                "status" => "in-production",
                "header" => "Zlecenia w produkcji"
            ],
            "done" => [
                "status" => "done",
                "header" => "Zlecenia zakończone"
            ],
        ];
        if(isset($avaliableStatuses[$status]["status"])){
            $sortOrder = array(  
                /* array("-orderConfirmedTime", "DESC"),
                array("-orderPublishedTime", "DESC"),
                array("-orderDoneTime", "DESC"), */
                array("orderDeadline", "ASC")
            );
            $getPageObject = $this->getPage($page, "*", "orderStatus = '".$avaliableStatuses[$status]["status"]."'", $sortOrder);
            $orders = $getPageObject["objects"];
            $orders = $this->fillOrdersWithDeadlineDaysLeft($orders);
            $orders = $this->fillOrdersWithCustomers($orders);
            $orders = $this->fillOrdersWithOperatorsNames($orders);
            $orders = $this->fillOrdersWithWTs($orders);
            $orders = $this->fillOrdersWithTotalWorkTimings($orders); // Warning: Takes a lof of time to calculate!
            $roles = RoleController::getActiveRoles(true);
            if($page == 0){
                $page++;
            }
            if($avaliableStatuses[$status]["status"] == "done"){
                $ignoreColors = 1;
            }else{
                $ignoreColors = 0;
            }
            if($status == "in-production"){
                $displayGanttChart = true;
            }else{
                $displayGanttChart = false;
            }
            return view("admin.orders.index", [
                "orders" => $orders,
                "pageHeader" => $avaliableStatuses[$status]['header'],
                "currentPage" => $page,
                "totalPages" => $getPageObject['totalPages'],
                "roles" => $roles,
                "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".orders.".$status,
                "ignoreColors" => $ignoreColors,
                "displayGanttChart" => $displayGanttChart,
            ]); 
        }else{
            return abort(403);
        }
    }

    /**
     * Display paged listing of the resource by page
     */
    public function indexByPage(int $page){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        $sortOrder = array(
            array("orderDeadline", "ASC")
        );
        $getPageObject = $this->getPage($page, '*', "", $sortOrder);
        $orders = $getPageObject["objects"];
        $orders = $this->fillOrdersWithDeadlineDaysLeft($orders);
        $orders = $this->fillOrdersWithCustomers($orders);
        $orders = $this->fillOrdersWithOperatorsNames($orders);
        $orders = $this->fillOrdersWithTotalWorkTimings($orders); // Warning: Takes a lof of time to calculate!
        $roles = RoleController::getActiveRoles(true);
        return view("admin.orders.index", [
            "orders" => $orders,
            "pageHeader" => "Zlecenia - strona ".$page,
            "currentPage" => $page,
            "totalPages" => $getPageObject['totalPages'],
            "roles" => $roles,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".orders"
        ]);
    }
    
    
    /**
     * Push status forward
     */
    public function pushStatusForward(Request $request){
        if(!PC::cp('edit_orders')){
            abort(403);
        }
        $Order = Order::find($request->input('pushOrderWithId'));
        $nextStatuses = [
            // "created" => "confirmed", // Usunięto na życzenie klienta, zamiast tego użyto:
            "created" => "in-production",
            //"confirmed" => "in-production",
            "in-production" => "done",
            "done" => "done",
        ];
        $updatePushStatuses = [
            "confirmed" => ["pusher" => "orderConfirmedBy", "time" => "orderConfirmedTime"],
            "in-production" => ["pusher" => "orderPublishedBy", "time" => "orderPublishedTime"],
            "done" => ["pusher" => "orderDoneBy", "time" => "orderDoneTime"],
        ];
        $goBackStatus = $Order->orderStatus;
        $Order->orderStatus = $nextStatuses[$Order->orderStatus];
        $pusherStatus = $updatePushStatuses[$Order->orderStatus]["pusher"];
        $pushTimeStatus = $updatePushStatuses[$Order->orderStatus]["time"];

        // Dodatkowy kod, potrzebny na zlecenie klienta:
        if($Order->orderStatus == "in-production"){    
            $skipConfirmedPusherStatus = $updatePushStatuses["confirmed"]["pusher"];
            $skipConfirmedPushTimeStatus = $updatePushStatuses["confirmed"]["time"];    
            $Order->$skipConfirmedPusherStatus = Auth::id();
            $Order->$skipConfirmedPushTimeStatus = date("Y-m-d H:i:s");
        }
        //Koniec dodatkowego kodu

        $Order->$pusherStatus = Auth::id();
        $Order->$pushTimeStatus = date("Y-m-d H:i:s");
        $errorMessage = "";
        try{        
            $Order->save();  
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            //$goBackStatus = $Order->orderStatus; // Redirect to previous status
            return redirect(route("orders.status", ["status" => $goBackStatus, "successMessage" => "Pomyślnie zmieniono status zlecenia."]));
        }else{
            return redirect(route("orders.status", ["status" => $goBackStatus, "errorMessage" => $errorMessage]));
        }
    }

    /**
     *  ADVANCED FUNCTIONS BELOW
     * 
     */

    /**
     * Soft delete order
     */
    function softDelete($orderId){
        // Alias for hard delete (not recommended)
        return $this->hardDelete($orderId);
    }

    /**
     * Hard delete order
     */
    function hardDelete($orderId){
        $Order = Order::find($orderId);
        if($Order->orderStatus == 'created'){ // Only when not published
            $Order->delete();
        }
        return redirect(route("orders.status", ["status" => "created", "successMessage" => "Pomyślnie usunięto zlecenie."]));
    }

    /**
     * Fill getted orders with days left to deadline
     */
    private function fillOrdersWithDeadlineDaysLeft($orders){
        foreach($orders as $order){
            $order->orderDateDiff = $this->calculateDatesDifference(now(), $order->orderDeadline);
            if(!isset($order->orderClasses)){
                $order->orderClasses = "";
            }
            if($order->orderDateDiff < 0){
                $order->orderClasses .= " bg-dark"; // Adding space is before, not behind
            }else if($order->orderDateDiff <= 2){
                $order->orderClasses .= " bg-danger";
            }else if($order->orderDateDiff <= 5){
                $order->orderClasses .= " bg-warning";
            }else if($order->orderDateDiff <= 10){
                $order->orderClasses .= " bg-success";
            }
        }
        return $orders;
    }


    /**
     * Calculate date difference
     */
    private function calculateDatesDifference($firstDate, $secondDate, $outputType="days"){
        $firstDate = strtotime($firstDate);
        $secondDate = strtotime($secondDate);
        $dateDiff = $secondDate - $firstDate;
        if($outputType == "days"){
            return ceil($dateDiff / (60 * 60 * 24));
        }
    }
    
    /**
     * Fill orders with customers details
     */
    private function fillOrdersWithCustomers($orders){
        foreach($orders as $order){
            $order->customer = Customer::find($order->orderCustomer);
        }
        return $orders;
    }
    
    /**
     * Get order notes
     */
    private function getNotesByOrderId($id){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        return DB::table("notes")->select("noteId", "noteTitle", "updated_at")->where("noteRelatorId", $id)->where("noteRelatorSlug", "order")->get();
    }

    /**
     * Get order attachmentss
     */
    private function getAttachmentsByOrderId($id){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        return DB::table("attachments")->select("attachmentId", "attachmentTitle", "attachmentPath", "updated_at")->where("attachmentRelatorId", $id)->where("attachmentRelatorSlug", "order")->get();
    }

    /**
     * Get details by id
     */
    private function getDetailsByOrderId($id){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        return DB::table("order_details")->select("*")->where("orderDetailOrderId", $id)->orderBy("orderDetailOrderNumber", "ASC")->get();
    }

    /**
     * Get all orders that are in production
     */
    public function getOrdersInProduction(){
        $orders = Order::where("orderStatus","in-production")->get();
        $orders = $this->fillOrdersWithDeadlineDaysLeft($orders);
        return $orders;
    } 
    
    /**
     * Get all details that are in production by order identifier
     */
    public function getDetailsInProductionByOrderId($orderId){
        if(!PC::cp('view_orders')){
            abort(403);
        }
        $details = OrderDetail::where("orderDetailOrderId", $orderId)->get();
        return $details;
    }

    /**
     * Get last five orders till deadline
     */
    public function getLastFiveOrdersTillDeadline(){
        if(!PC::cp('view_orders')){
            return null;
        }
        $Orders = DB::table("orders")->select("*")->whereRaw("`orderStatus` != 'done'")->orderBy("orderDeadline", "ASC")->limit(5)->get();
        $Orders = $this->fillOrdersWithDeadlineDaysLeft($Orders);
        $Orders = $this->fillOrdersWithCustomers($Orders);
        return $Orders;
    }

    public function fillOrdersWithOperatorsNames($orders){
        foreach($orders as $order){
            if($order->orderDoneBy != null){
                $order->currentOperator = User::find($order->orderDoneBy)->name;
                $order->currentOperatorOperation = "Zakończono";
            }else if($order->orderPublishedBy != null){
                $order->currentOperator = User::find($order->orderPublishedBy)->name;
                $order->currentOperatorOperation = "Opublikowano";
            }else if($order->orderConfirmedBy != null){
                $order->currentOperator = User::find($order->orderConfirmedBy)->name;
                $order->currentOperatorOperation = "Zatwierdzono";
            }else{
                $order->currentOperator = User::find($order->orderCreatedBy)->name;
                $order->currentOperatorOperation = "Utworzono";
            }
        }
        return $orders;
    }

    public function getAllDetailsByOrderId($orderId){
        $AllDetails = OrderDetail::where("orderDetailOrderId", $orderId)->get();
        return $AllDetails;
    }

    /**
     * Watch live log view
     */
    public function watchLiveLog($orderId){
        $WTHC = new WorkTimingHistoryController();
        $historyItems = $WTHC::showHistoryOfDetailsInOrder($orderId);
        return view("admin.orders.live", [
            "historyItems" => $historyItems,
            "orderId" => $orderId
        ]);
    }

    private function fillOrdersWithTotalWorkTimings($orders){
        $WTC = new WorkTimingController();
        $roles = RoleController::getActiveRoles(true);
        foreach($orders as $order){
            $detailsInThisOrder = $this->getAllDetailsByOrderId($order->orderId);
            foreach($roles as $role){
                $role->totalRealTimeOfThisRole = 0;
                $role->totalEstimatedTimeOfThisRole = 0;
                $slugReal = $role->roleSlug."Real";
                $slugEstimated = $role->roleSlug."Estimated";
                $order->$slugReal = 0;
                $order->$slugEstimated = 0;
            }
            foreach($detailsInThisOrder as $detail){
                $totalRealTimes = $WTC->getSumOfRealTimeForDetail($detail->orderDetailId);
                foreach($totalRealTimes as $realTime){
                    foreach($roles as $role){
                        if($role->roleSlug == $realTime->workTimingRoleSlug){
                            $role->totalRealTimeOfThisRole += $realTime->totalSeconds;
                        }
                    }
                }
                $totalEstimatedTimes = $WTC->getSumOfEstimatedTimeForDetail($detail->orderDetailId);
                foreach($totalEstimatedTimes as $estimatedTime){
                    foreach($roles as $role){
                        if($role->roleSlug == $estimatedTime->workTimingRoleSlug){
                            $role->totalEstimatedTimeOfThisRole += ($detail->orderDetailItemsTotal*$estimatedTime->totalSeconds);
                        }
                    }
                }
            }
            foreach($roles as $role){
                $slugReal = $role->roleSlug."Real";
                $slugEstimated = $role->roleSlug."Estimated";
                $order->$slugReal += $role->totalRealTimeOfThisRole;
                $order->$slugEstimated += $role->totalEstimatedTimeOfThisRole;
            }
        }
        return $orders;
    }
    private function fillSingleOrderWithWorkTimings($order){
        $WTC = new WorkTimingController();
        $roles = RoleController::getActiveRoles(true);
        $detailsInThisOrder = $this->getAllDetailsByOrderId($order->orderId);
        foreach($roles as $role){
            $role->totalRealTimeOfThisRole = 0;
            $role->totalEstimatedTimeOfThisRole = 0;
            $slugReal = $role->roleSlug."Real";
            $slugEstimated = $role->roleSlug."Estimated";
            $order->$slugReal = 0;
            $order->$slugEstimated = 0;
        }
        foreach($detailsInThisOrder as $detail){
            $totalRealTimes = $WTC->getSumOfRealTimeForDetail($detail->orderDetailId);
            foreach($totalRealTimes as $realTime){
                foreach($roles as $role){
                    if($role->roleSlug == $realTime->workTimingRoleSlug){
                        $role->totalRealTimeOfThisRole += $realTime->totalSeconds;
                    }
                }
            }
            $totalEstimatedTimes = $WTC->getSumOfEstimatedTimeForDetail($detail->orderDetailId);
            foreach($totalEstimatedTimes as $estimatedTime){
                foreach($roles as $role){
                    if($role->roleSlug == $estimatedTime->workTimingRoleSlug){
                        $role->totalEstimatedTimeOfThisRole += ($detail->orderDetailItemsTotal*$estimatedTime->totalSeconds);
                    }
                }
            }
            foreach($roles as $role){
                $slugReal = $role->roleSlug."Real";
                $slugEstimated = $role->roleSlug."Estimated";
                $detail->$slugReal += $role->totalRealTimeOfThisRole;
                $detail->$slugEstimated += $role->totalEstimatedTimeOfThisRole;
            }
            $detail->roles = $roles;
        }
        $order->details = $detailsInThisOrder;
        return $order;
    }

    private function fillOrdersWithWTs($orders){
        $WTC = new WorkTimingController();
        foreach($orders as $order){
            $order->complexWTs = $WTC->getComplexWorkTimings($order->orderId);
        }
        return $orders;
    }

    public function repairDataIntegrity($orderSlug){
        // Get all order ID's:
        $orderIds = DB::table("orders")->select("orderId")->where("orderIsDeleted", 0)->get();
        foreach($orderIds as $oid){
            DB::table('work_timings')->insert(
                array(
                    'workTimingUserId' => Auth::id(),
                    'workTimingRelatorId' => $oid->orderId,
                    'workTimingRelatorParentId' => $oid->orderId,
                    'workTimingRoleSlug' => $orderSlug,
                    'workTimingType' => 'complex',
                    'workTimingFinal' => 0,
                )
            );
        }
        return true;
    }
}
