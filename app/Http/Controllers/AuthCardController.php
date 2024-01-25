<?php

namespace App\Http\Controllers;

use App\Models\AuthCard;
use DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionsController as PC;
use Illuminate\Http\Request;

class AuthCardController extends Controller
{
    public function getPage($page = 0){ 
        if(!PC::cp('manage_employees_authcards')){
            abort(403);
        }
        // Return paged card's list
        $itemsPerPage = config("APP_ITEMS_PER_PAGE");
        $AuthCards = DB::table('auth_cards')->select('*')->join('users', 'auth_cards.authCardUserId', '=', 'users.id')->get();
        return $AuthCards;
    }

    public function index($getPage = 0, $errorMessage = ""){ 
        if(!PC::cp('manage_employees_authcards')){
            abort(403);
        }
        // Handle GET main page
        $UserController = new UserController();
        return view("admin.identity-cards.index", [
            "joints" => $this->getPage($getPage),
            "users" => $UserController->getActiveUsernames(),
            "errorMessage" => $errorMessage,
        ]);
    }

    public function remove(Request $request){ 
        if(!PC::cp('manage_employees_authcards')){
            abort(403);
        }
        // Handle GET main page
        $AuthCard = AuthCard::where("authCardId", $request->input("deleteIdentityCardId"))->delete();

        return redirect(route("identity-cards"));
    }

    public function store(Request $request){
        if(!PC::cp('manage_employees_authcards')){
            abort(403);
        }
        // Save new auth card
        $success = true;
        $errorMessage = "";
        $AuthCard = new AuthCard($request->all());
        try{
            $AuthCard->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        return redirect(route("identity-cards", ["errorMessage" => $errorMessage]));
    }
    
    /**
     * Get user info by authcard id
     */
    public static function getUserIdByAuthcardId($authCardId){
        $success = true;
        try{
            $authCardUserId = DB::table("auth_cards")->select('authCardUserId')->where("authCardCode", $authCardId)->first();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            $getObj = DB::table("auth_cards")->select('authCardUserId')->where("authCardCode", $authCardId)->first();
            if(isset($getObj->authCardUserId)){
                return $getObj->authCardUserId;
            }else{
                return NULL;
            }
        }else{
            return NULL;
        }
    }
}
