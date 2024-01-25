<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\DetailDeployed;
use Illuminate\Http\Request;
use App\Http\Controllers\PrintableController;

class DeploymentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index($orderId = 0){
        if(!is_numeric($orderId)){
            abort(400);
        }
        if($orderId == 0){

            // OrderId is not defined, list last 100 and null details list.
            $inProductionOrdersList = Order::where("orderStatus", "in-production")->orderBy('orderId', 'DESC')->limit(100)->get();
            $detailsList = null;

        }else{

            // OrderId is defined, get details list and null orders list.

            $detailsList = OrderDetail::where("orderDetailOrderId", $orderId)->get();
            $inProductionOrdersList = null;

        }
        return view('admin.deployment.index', [
            "Orders" => $inProductionOrdersList,
            "displayDetials" => $orderId,
            "Details" => $detailsList,
            "OrderId" => $orderId,
        ]);
    }

    public function generateOutput(Request $request, $orderId){

        $PrintableController = new PrintableController();

        $detailIdsListArray = $request->input("selectDetail");
        if($detailIdsListArray === null){
            abort(403, "Nie wybrano detali");
        }

        $detailIdsList = array();

        // Refactor data. Convert:
        // 150 => "on"
        // 151 => "on"
        // 152 => "on"
        // into:
        // [150, 151, 152]
        foreach($detailIdsListArray as $key => $value){
            array_push($detailIdsList, $key);
        }

        if($request->input("generateLabels") !== null){

            // Generate labels
            return $PrintableController->printDeploymentBarcodes($orderId, $detailIdsList);

        }else if($request->input("generateForm") !== null){

            // Generate WZ form
            return $PrintableController->orderSummary($orderId, $detailIdsList);

        }

    }

    public function deployDetail($orderId, $detailId){
        $ODC = new OrderDetailController();

        $Order = Order::where("orderId", $orderId)->first();
        $Detail = OrderDetail::where("orderDetailId", $detailId)->first();

        $SingeDetails = $ODC->generateEanEightCodes($Detail, false);

        $DetailsDeployed = DetailDeployed::select("deployedDetailEAN")->where("deployedDetailOrderId", $orderId)->where("deployedDetailDetailId", $detailId)->get()->all();
        $DetailsDeployedArray = [];
        foreach($DetailsDeployed as $DetailDeployed){
            array_push($DetailsDeployedArray, $DetailDeployed->deployedDetailEAN);
        }
        $DetailsDeployed = $DetailsDeployedArray;

        return view('admin.deployment.detail', [
            "Order" => $Order,
            "Detail" => $Detail,
            "SingleDetails" => $SingeDetails,
            "DetailsDeployed" => $DetailsDeployed
        ]);

    }

    public function insertDeploy(Request $request){
        $deployedDetailOrderNumber = array_reverse(explode("0", $request->input("deployedDetailEAN")))[0];

        // Check whether count of done details IS NOT greather than deployed details:

        $DetailsDone = OrderDetail::where('orderDetailId', $request->input('deployedDetailDetailId'))->first()->orderDetailItemsDone;

        $DetailsAlreadyDeployed = DB::table('details_deployed')->selectRaw('COUNT(*) AS count')->where('deployedDetailDetailId', $request->input('deployedDetailDetailId'))->first()->count;

        if($DetailsDone > $DetailsAlreadyDeployed){
            
            // Make a deployment

            $DetailDeployed = new DetailDeployed($request->all());
            $DetailDeployed->deployedDetailIsDeployed = 1;
            $DetailDeployed->deployedDetailOrderNumber = $deployedDetailOrderNumber;
            $output["success"] = false;
            if(strlen($request->input("deployedDetailEAN")) != 8){
                $output["success"] = false;
                $output["errorMsg"] = "EAN should be 8 characters long - Reader Error";
            }else{
                try{
                    $output["success"] = $DetailDeployed->save();
                }
                catch(\Exception $e){
                    $output["success"] = false;
                    $errorMsg = $e->getMessage();
                    if(str_contains($errorMsg, "Duplicate entry")){
                        $errorMsg = "Ten detal został już zeskanowany!";
                    }
                    $output["errorMsg"] = $errorMsg;
                }
            }
        }else{

            // Prohibit not manufactured

            $output["success"] = false;
            $output["errorMsg"] = "You cannot deploy more details than are manufactured.";

        }

       
       
        if($output["success"]){
            $OrderDetail = OrderDetail::where("orderDetailId", $request->input('deployedDetailDetailId'))->first();
            $OrderDetail->orderDetailItemsDeployed = ($OrderDetail->orderDetailItemsDeployed+1);
            $OrderDetail->save();
        }

        // Check whether all items in each detail of order are done. If so, close the order.

        return response()->json($output);

    }

}
