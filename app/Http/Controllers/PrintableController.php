<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Milon\Barcode\DNS1D;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WorkTimingController;

class PrintableController extends Controller
{
    /**
     * Print demanded informations
     */
    public function print($refererSlug, $refererId){
        $referer = $this->getValidReferers($refererSlug);
        if($referer != false){
            $refererFunctionName = $referer['function'];
            return $this->$refererFunctionName($refererSlug, $refererId);
        }else{
            return "Invalid referer slug";
        }
    }
    
    /**
     * Check whether referers is valid or not
     */
    private function getValidReferers($refererSlug){
        $validReferers = [
            "detail-barcode" => [
                "function" => "printDetailBarcode"
            ],
            "order-details-barcodes" => [
                "function" => "printOrderDetailsBarcodes"
            ]
        ];
        if(isset($validReferers[$refererSlug])){
            return $validReferers[$refererSlug];
        }
        return false;
    }
    
    /**
     * Generate bar code
     */
    private function generateBarCode($code, $standard = "EAN13"){
        return DNS1D::getBarcodeHTML($code, $standard);
    }

    /**
     * Generate barcode for single detail
     */
    private function printDetailBarcode($refererSlug, $refererId){
        $WTC = new WorkTimingController();
        $Detail = OrderDetail::find($refererId);
        $Order = Order::find($Detail->orderDetailOrderId);

        // Paper configuration
        $widthInCm = 6.5;
        $heightInCm = 8;
        $dpi = 72;
        $widthInDpi = round(($widthInCm/2.54)*$dpi,2);
        $heightInDpi = round(($heightInCm/2.54)*$dpi,2);
        $customPaper = array(0,0,$heightInDpi,$widthInDpi);
        $barcode = $this->generateBarCode($Detail->orderDetailUniqueId);

        // View settings
        $viewSettings = [
            "order" => $Order,
            "detail" => $Detail,
            "ewt" => $WTC->getEstimatedWorkTimesByDetailId($refererId),
            "barcodeImg" => $barcode,
            "refererSlug" => $refererSlug,
            "refererId" => $refererId,
            "appName" => env("APP_NAME")
        ];

        //return view('print.'.$refererSlug, $viewSettings); // Test only
        $pdf = PDF::loadView('print.'.$refererSlug, $viewSettings)->setPaper($customPaper, 'landscape');
        return $pdf->stream($refererSlug.'-'.$refererId.'.pdf');
    }

    /**
     * Generate barcodes for all details inside an order
     */
    function printOrderDetailsBarcodes($refererSlug, $refererId){
        $WTC = new WorkTimingController();
        $OC = new OrderController();
        $Order = Order::find($refererId);
        $Details = $OC->getAllDetailsByOrderId($refererId);

        // Paper configuration
        $widthInCm = 6.5;
        $heightInCm = 8;
        $dpi = 72;
        $marginerWidth = 0.11;
        $marginerHeight = 0.11;
        $widthInDpi = round(($widthInCm/2.54)*$dpi,2);
        $heightInDpi = round(($heightInCm/2.54)*$dpi,2);
        $customPaper = array(0,0,$heightInDpi,$widthInDpi);
        $totalCounter = 0;
        foreach($Details as $Detail){
            $totalCounter++;
            $Detail->eWT = $WTC->getEstimatedWorkTimesByDetailId($Detail->orderDetailId);
            $Detail->barcodeImg =  $this->generateBarCode($Detail->orderDetailUniqueId);
        }

        // View configuration
        $viewSettings = [
            "order" => $Order,
            "details" => $Details,
            "refererSlug" => $refererSlug,
            "refererId" => $refererId,
            "appName" => env("APP_NAME"),
            "pageWidth" => $widthInCm-$marginerWidth,
            "pageHeight" => $heightInCm-$marginerHeight,
        ];
        $pdf = PDF::loadView('print.'.$refererSlug, $viewSettings)->setPaper($customPaper, 'landscape');
        
        //return view('print.'.$refererSlug, $viewSettings); // Test only
        return $pdf->stream($refererSlug.'-'.$refererId.'.pdf');
    }

    /**
     * Generate "WZ" file for order with specified ID
     */
    public function orderSummary($orderId, $selectedDetails = null){

        $Order = Order::where("orderId", $orderId)->first();
        $Customer = Customer::where("customerId", $Order->orderCustomer)->first();

        $OC = new OrderController();
        if($selectedDetails === null){

            // Detail Ids are not specified, get all:
            $Details = $OC->getAllDetailsByOrderId($orderId);

        }else{

            // Load specified
            $Details = OrderDetail::whereIn('orderDetailId', $selectedDetails)->get();

        }

        $viewSettings = [
            "order" => $Order, 
            "customer" => $Customer,
            "details" => $Details
        ];

        $pdf = PDF::loadView("print.order-summary", $viewSettings)->setPaper('A4', 'portrait');

        return $pdf->stream('wz-'.$orderId.'.pdf');
    }

    /**
     * Generate barcodes for deployment
     */
    function printDeploymentBarcodes($orderId, $selectedDetails){
        $ODC = new OrderDetailController();
        $Order = Order::where("orderId", $orderId)->first();
        $Details =  $Details = OrderDetail::whereIn('orderDetailId', $selectedDetails)->get();

        // Paper configuration
        $widthInCm = 6.5;
        $heightInCm = 4;
        $dpi = 72;
        $marginerWidth = 0.11;
        $marginerHeight = 0.11;
        $widthInDpi = round(($widthInCm/2.54)*$dpi,2);
        $heightInDpi = round(($heightInCm/2.54)*$dpi,2);
        $customPaper = array(0,0,$heightInDpi,$widthInDpi);
        $totalCounter = 0;

        $CompleteDetails = array();

        // Details proceeding
        foreach($Details as $Detail){
            $PartialDetails = $ODC->generateEanEightCodes($Detail);
            $CompleteDetails = array_merge($CompleteDetails, $PartialDetails);
        }

        // View configuration
        $viewSettings = [
            "Order" => $Order,
            "CompleteDetails" => $CompleteDetails,
            "appName" => env("APP_NAME"),
            "pageWidth" => $widthInCm-$marginerWidth,
            "pageHeight" => $heightInCm-$marginerHeight,
        ];
        $pdf = PDF::loadView('print.printDeploymentBarcodes', $viewSettings)->setPaper($customPaper, 'landscape');
        
        //return view('print.'.$refererSlug, $viewSettings); // Test only
        return $pdf->stream('printDeploymentBarcodes.pdf');
    }
}
