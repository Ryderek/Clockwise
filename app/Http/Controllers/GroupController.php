<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\PermissionsController as PC;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($page = 0){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        $getPageObject = $this->getPage($page);
        return view("admin.groups.index", [
            "groups" => $getPageObject["objects"],
            "currentPage" => ($page+1),
            "totalPages" => $getPageObject['totalPages'],
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".groups",
        ]);
    }

    /**
     * Display paged listing of the resource.
     */
    public function getPage($page = 0, $columns = '*', $where = ""){ 
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        // Get count of all items meeting the condition:
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $Groups = DB::table('admin_groups')->select("*")->whereRaw("groupIsActive = 1")->take($itemsPerPage);
        $TotalGroups = DB::table('admin_groups')->selectRaw("count(*) as counter")->whereRaw("groupisActive = 1");

        if($page != 0){
            $page--;
            $Groups = $Groups->skip($page*$itemsPerPage);
        }
        if(strlen($where) != 0){
            $Groups = $Groups->whereRaw($where);
            $TotalGroups = $TotalGroups->whereRaw($where);
        }
        $Groups = $Groups->get();
        $TotalGroups = $TotalGroups->first()->counter;

        $returnObject = [
            "objects" => $Groups,
            "currentPage" => $page,
            "totalPages" => (int) ceil($TotalGroups/$itemsPerPage),
        ];
        return $returnObject;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        return view("admin.groups.create", [
            "groups" => DB::table('admin_groups')->select("*")->where("groupIsActive", 1)->get(),
            "randomPassword" => $this->randomPassword()
        ]);
    }

    /**
     * Show the form for editing a resource.
     */
    public function edit($id){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        $groupPermissions = $this->getGroupPermissionsList($id);
        return view("admin.groups.edit", [
            "group" => Group::find($id),
            "permissions" => $groupPermissions
        ]);
    }

    /**
     * Update group info
     */
    public function update(Request $request){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        $request->request->remove('fakeUsernameAutofill');

        $errorMessage = "";
        try{        
            $Group = Group::find($request->input("groupId"));
            $Group->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("group", ["id" => $request->input("groupId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("group", ["id" => $request->input("groupId"), "errorMessage" => $errorMessage]));
        }
    }
    /**
     * Update privileges
     */
    public function updatePermissions(Request $request){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        $groupId = $request->input("groupId");
        $allowedPermissions = $this->getAllPermissions();
        $this->removeAllPrivilegesFromGroup($groupId);
        
        $errorMessage = "";
        try{
            foreach($allowedPermissions as $allowedPermission){
                if(isset($_POST[$allowedPermission->name])){
                    DB::table('admin_group_permissions_associations')->insert([
                        ['permissionId' => $allowedPermission->id, 'groupId' => $groupId]
                    ]);
                }
            }
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("group", ["id" => $request->input("groupId"), "successMessage" => "Pomyślnie zaktualizowano uprawnienia!"]));
        }else{
            return redirect(route("group", ["id" => $request->input("groupId"), "errorMessage" => $errorMessage]));
        }
        /*
        $errorMessage = "";
        try{        
            $Group = Group::find($request->input("groupId"));
            $Group->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("group", ["id" => $request->input("groupId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("group", ["id" => $request->input("groupId"), "errorMessage" => $errorMessage]));
        }*/
    }

    /**
     * Soft delete a resource.
     */
    public function remove(Request $request){
        if(!PC::cp('manage_groups')){
            abort(403);
        }
        $success = true;
        $errorMessage = "";
        try{
            $Group = Group::find($request->input('deleteGroupId'));
            $Group->groupIsActive = 0;
            $Group->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route('groups', ["successMessage" => "Pomyślnie usunięto grupę."]));
        }else{
            return redirect(route('groups', ["errorMessage" => $errorMessage]));
        }
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        if(!PC::cp('manage_groups')){
            abort(403);
        }

        $success = true;

        try{
            $role = Role::create([
                'guard_name' => $request->input('groupPrefix'),
                'name' => $request->input('groupName')
            ]);
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        /*
        $request->request->remove('fakeUsernameAutofill');
        $Group = new Group($request->all());
        $insertGroupSuccess = true;
        try{
            $Group->save();
        }catch(\Throwable $e){
            $insertGroupSuccess = false;
            $errorMessage = $e;
        }
        */
        if($success == true){
            return redirect(route("groups", ["successMessage" => "Pomyślnie utworzono grupę!"]));
        }else{
            return redirect(route("group.create", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Generate random password for "create" page
     */
    private function randomPassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
    
    /**
     * Get all permissions assigned to group
     */
    private function getGroupPermissionsList($groupId){
        $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
        return $perms;
    }

    public function getAllGroups(){
        $perms = DB::table('admin_groups')->select("*")->get();
        return $perms;
    }

    private function getAllPermissions(){
        $perms = DB::table('admin_group_permissions')->select("*")->get();
        return $perms;
    }

    private function removeAllPrivilegesFromGroup($groupId){
        DB::statement('DELETE FROM `admin_group_permissions_associations` WHERE `admin_group_permissions_associations`.`groupId` = '.$groupId);
    }
}
