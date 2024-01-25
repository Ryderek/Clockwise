<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionsController extends Controller
{
    //
    public static function cp($permissions){ // Check (mine) permissions
        $User = User::find(Auth::id());
        $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $User->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
        if(is_array($permissions)){
            foreach($permissions as $permission){
                foreach($perms as $perm){
                    if ($perm->name == $permission){
                        return true;
                    }
                }
            }
        }else{
            $permission = $permissions;
            foreach($perms as $perm){
                if ($perm->name == $permission){
                    return true;
                }
            }
        }
        return false;
    }
   
}
