<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WorkTimingHistoryController;
use File;
use Response;        

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $OrderController = new OrderController();
        $WorkTimingHistory = new WorkTimingHistoryController();
        $Tools = new ToolController();
        $Tools = $Tools->getLastFiveDamagedTools();
        $NotificationController = new NotificationController();
        return view('admin', [
            "orders" => $OrderController->getLastFiveOrdersTillDeadline(),
            "WorkTimeHistory" => $WorkTimingHistory->getLastFiveWorkTimes(),
            "tools" => $Tools,
            "Notifications" => $NotificationController->getNotificationsList()
        ]);
    }

    /**
     * Show production pin screen.
     */
    public function production(){
        session_start();
        if(isset($_SESSION['authCardId'])){
            $sessionAuthCardId = $_SESSION['authCardId'];
        }else{
            $sessionAuthCardId = "";
        }
        session_write_close();
        if($sessionAuthCardId != ""){
            return redirect(route("production.dashboard"));
        }else{
            // Get production in progress
            $ODC = new OrderDetailController();
            $detailsInProgress = $ODC->getDetailsInProgress();
            return view("production.index", [
                "detailsInProgress" => $detailsInProgress
            ]);
        }
    }
    /**
     * Verify provided pin
     */
    public function productionVerify(Request $request){
        if(ProductionController::verify($request->input("authCodeId"))){

            $WTC = new WorkTimingController();
            $amIAtWork = $WTC->isEmployeeAtWork($request->input('authCodeId'));

            if($amIAtWork){
                session_start();
                $_SESSION['authCardId'] = $request->input("authCodeId");
                session_write_close();
                return redirect(route("production"));
            }else{
                return redirect(route("production", ["error" => "Błąd: Nie odnotowano wejścia do pracy!"]));
            }
           
        }else{
            return redirect(route("production", ["error" => "Podany kod jest niepoprawny"]));
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function uploaded(Request $request)
    {
        $path = rawurldecode(storage_path().$request->getPathInfo()); 
        if(!File::exists($path)) {
            return response()->json(['message' => 'Image not found.', 'path' => $path], 404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
}
