<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\WorkTimingController;

class TimeTrackingController extends Controller
{
    private $authCardId = null;

    /**
     *  Construct auth check
     */
    public function __construct(){
        /*
        session_start();
        if(isset($_SESSION['authCardId'])){
            $sessionId = $_SESSION['authCardId'];
        }else{
            $sessionId = "";
        }
        session_write_close();
        if($sessionId == ""){
            return redirect(route('time-tracking'));
        }else{
            $this->authCardId = $sessionId;
        }
        */
    }

    /**
     * Verify provided pin
     */
    public function timeTrackingVerify(Request $request){
        if($this->verify($request->input("authCodeId"))){
            session_start();
            $_SESSION['authCardId'] = $request->input("authCodeId");
            session_write_close();
            return redirect(route("time-tracking"));
        }else{
            return redirect(route("time-tracking", ["error" => "Podany kod jest niepoprawny"]));
        }
    }

    /**
     * Statically verify whether authcode is valid
     */
    public function verify($authCardId){
        $authcardExist = DB::table("auth_cards")->select("authCardUserId")->where("authCardCode", $authCardId)->first();
        if($authcardExist != null){
            $_SESSION['authCardId'] = $authcardExist;
            return true;
        }else{
            return false;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index($page = 0){ 
        return view("time-tracking.index-alter");
        // Make it public
        /*
        if($this->authCardId == null){
            return view("time-tracking.guest");
        }else{
            $userId = AuthCardController::getUserIdByAuthcardId($this->authCardId);
            $User = User::find($userId);
            $WTC = new WorkTimingController();
            $amIAtWork = $WTC->isEmployeeAtWork($this->authCardId);
            return view("time-tracking.index", [
                "user" => $User,
                "authCardId" => $this->authCardId,
                "amIAtWork" => $amIAtWork
            ]);
        }
        */

    }

    /**
     * Switch time tracking
     */
    public function switchWorktime(Request $request){
        //if($request->input('authCardCode') == $this->authCardId){
            $WTC = new WorkTimingController();
            $switchStatus = $WTC->switchEmployeeWorktime($request->input('authCardCode'));
            $amIAtWork = $WTC->isEmployeeAtWork($request->input('authCardCode'));
            if(gettype($amIAtWork) == "object"){
                $amIAtWork = 1;
            }
        //}
        return redirect(route('time-tracking', ["amIAtWork" => $amIAtWork]));
    }

    /**
     * Logout
     */
    public function logout(){
        session_start();
        unset($_SESSION['authCardId']);
        session_write_close();
        return redirect(route('time-tracking'));
    }
}
