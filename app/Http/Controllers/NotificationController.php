<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Notification;
use App\Http\Controllers\AuthCardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PermissionsController as PC;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($page = 0){ 
        if(!PC::cp('receive_notifications')){
            abort(403);
        }
        $getPageObject = $this->getPage($page);
        return view("admin.notifications.index", [
            "notifications" => $getPageObject["objects"],
            "currentPage" => ($page+1),
            "totalPages" => $getPageObject['totalPages'],
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".notifications"
        ]);
    }

    /**
     * Display paged listing of the resource.
     */
    public function getPage($page = 0){ 
        if(!PC::cp('receive_notifications')){
            abort(403);
        }
        // Get count of all items meeting the condition:
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $Notifications = DB::table('notifications')->select("*")->take($itemsPerPage)->join('users', 'notifications.notificationSenderId', '=', 'users.id');
        $TotalNotifications = DB::table('notifications')->selectRaw("count(*) as counter")->join('users', 'notifications.notificationSenderId', '=', 'users.id');

        if($page != 0){
            $page--;
            $Notifications = $Notifications->skip($page*$itemsPerPage);
        }
        $Notifications = $Notifications->where("notificationIsDismissed", 0);
        $TotalNotifications = $TotalNotifications->where("notificationIsDismissed", 0);

        $Notifications = $Notifications->get();
        $TotalNotifications = $TotalNotifications->first()->counter;

        $returnObject = [
            "objects" => $Notifications,
            "currentPage" => $page,
            "totalPages" => (int) ceil($TotalNotifications/$itemsPerPage),
        ];
        return $returnObject;
    }


    /**
     * Get list of notifications (all)
     */
    public function getNotificationsList(){
        if(!PC::cp('receive_notifications')){
            return null;
        }
        $Notifications = DB::table('notifications')->where('notificationIsDismissed', 0)->join('users', 'notifications.notificationSenderId', '=', 'users.id')->get();
        return $Notifications;
    }

    /**
     * Dismiss a notification
     */
    public function dismissNotification(Request $request){
        $notiId = $request->input('dismissNotificationId');
        $success = true;
        try{
            $Notification = Notification::find($notiId);
            $Notification->notificationIsDismissed = 1;
            $Notification->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("notifications", ["successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("notifications", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Create a notifications
     */
    public function createNotification(Request $request){
        $ACC = new AuthCardController();
        $userId = $ACC->getUserIdByAuthcardId($request->input('authCardId'));
        if($userId == $request->input('notificationSenderId')){
            $success = true;
            try{
                $Notification = new Notification($request->all());
                if($Notification->notificationContent == null){
                    $Notification->notificationContent = "";
                }
                $Notification->save();
            }catch(\Throwable $e){
                $success = false;
                $errorMessage = $e;
                dd($errorMessage);
            }
            if($success){
                // Logout (client's wish) (logout on notification send)
                // $ProCon = new ProductionController();
                // return $ProCon->logProductionOut();
                return redirect(route("production", ["successMessage" => "Pomyślnie wysłano zgłoszenie!"])); // Previously: redirect
            }else{
                return redirect(route("production", ["errorMessage" => $errorMessage]));
            }
        }else{
            return redirect(route("production", ["errorMessage" => "Użytkownik jest inny niż karta!"]));
        }
    }
}
