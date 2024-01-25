<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\UserRoleRelation;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use DB;

class RoleController extends Controller
{
    private $rolesList;
    private $rolesAll;
    private int $currentPage;
    private int $totalPages;
    private int $totalItems;
    /**
     * Display a listing of the resource.
     */
    public function index($getPage = 0){ 
        $roles = $this->getPage($getPage);
        if($getPage == 0){
            $this->currentPage = 1;
        }
        return view("admin.roles.index", [
            "roles" => $this->rolesList,
            "pageHeader" => "Narzędzia",
            "currentPage" => $this->currentPage,
            "totalPages" => $this->totalPages,
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".roles",
        ]);
    }

     /**
     * Get specified page of roles.
     */
    public function getPage($page = 0, $columns = '*', $where = ""){ 
        
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $this->rolesList = DB::table('roles')->select($columns)->take($itemsPerPage);
        $this->rolesAll = DB::table('roles')->selectRaw("count(*) as counter"); // Get count of all items
        $this->currentPage = $page;

        if($page != 0){ // Load specified page
            $page--;
            $this->rolesList->skip((int) $page * (int) $itemsPerPage);
        }

        // Exclude inactive roles
        $this->rolesList->whereRaw("roleIsActive = 1");
        $this->rolesAll->whereRaw("roleIsActive = 1");

        if(strlen($where) != 0){ // Specify "WHERE" condition
            $this->rolesList->whereRaw($where);
            $this->rolesAll->whereRaw($where);
        }
        $this->rolesList = $this->rolesList->get();
        $this->totalItems = $this->rolesAll->first()->counter;
        $this->totalPages = (int) ceil($this->totalItems/$itemsPerPage);
        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        return view("admin.roles.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $request->request->remove('fakeUsernameAutofill');
        $Role = new Role($request->all());
        $request->merge(['roleUpdatedBy' => Auth::id()]);
        $insertRoleSuccess = true;
        try{
            $Role->save();
        }catch(\Throwable $e){
            $insertRoleSuccess = false;
            $errorMessage = $e;
        }
        if($insertRoleSuccess){
            // Role stored. Insert estimated times to orders, to keep the data integrity
            $OC = new OrderController();
            $OC->repairDataIntegrity($Role->roleSlug);
            
            return redirect(route("role.edit", ["id" => $Role->roleId, "successMessage" => "Pomyślnie utworzono narzędzie!"]));
        }else{
            return redirect(route("role.create", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        $role = Role::find($id);
        return view("admin.roles.edit", [
            "role" => $role,
        ]);
    }

    /**
     * Update role info
     */
    public function update(Request $request){
        $request->request->remove('fakeUsernameAutofill');
        $request->request->remove('roleSlug'); // prohibit changing slug
        $errorMessage = "";
        try{        
            $Role = Role::find($request->input("editRoleId"));
            $Role->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("role.edit", ["id" => $request->input("editRoleId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("role.edit", ["id" => $request->input("editRoleId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Remove role
     */
    public function remove(Request $request){
        $request->request->remove('fakeUsernameAutofill');
        $success = true;
        try{        
            $Role = Role::find($request->input("deleteRoleId"));
            $Role->roleIsActive = 0;
            $Role->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("roles", ["successMessage" => "Pomyślnie usunięto!"]));
        }else{
            return redirect(route("roles", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Get role by slug
     */
    public function getRoleBySlug($slug){
        $role = Role::where("roleSlug", $slug)->first();
        return $role;
    }

    /**
     * Assign role to user
     */
    public function assign(Request $request){
        $URR = new UserRoleRelation($request->all());
        $success = true;
        try{        
            $URR->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("employee", ["id" => $request->input('userRoleUserId'),"successMessage" => "Pomyślnie dodano rolę do pracownika!"]));
        }else{
            return redirect(route("employee", ["id" => $request->input('userRoleUserId'), "errorMessage" => $errorMessage]));
        }
    }

    /**
     *  Release user from role
     */
    public function release(Request $request){
        $success = true;
        $errorMessage = "";
        try{
            UserRoleRelation::where('userRoleRelationId', $request->input('deleteRelationId'))->delete();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("employee", ["id" => $request->input('backToUserId'),"successMessage" => "Pomyślnie usunięto rolę pracownika."]));
        }else{
            return redirect(route("employee", ["id" => $request->input('backToUserId'), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Get all active roles
     */
    public static function getActiveRoles($fullInfo = false){
        if($fullInfo){
            return DB::table('roles')->select("*")->where("roleIsActive", 1)->get();
        }else{
            return DB::table('roles')->select("roleId", "roleName")->where("roleIsActive", 1)->get();
        }
    }

    /**
     *  Get user roles by user id
     */
    public static function getUserRoles($id){
        return DB::table('user_role_relations')->select("*")->where("userRoleUserId", $id)->join("roles", "roles.roleId", "=", "user_role_relations.userRoleRoleId")->get();
    }
}
