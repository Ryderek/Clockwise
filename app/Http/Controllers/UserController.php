<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoleRelation;
use App\Models\Group;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PermissionsController as PC;
use Illuminate\Http\Request;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($page = 0){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        $getPageObject = $this->getPage($page);
        $GroupController = new GroupController();
        $groups = $GroupController->getAllGroups();
        return view("admin.users.index", [
            "users" => $getPageObject["objects"],
            "groups" => $groups,
            "currentPage" => ($page),
            "totalPages" => $getPageObject['totalPages'],
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".employees",
        ]);
    }
    
    /**
     * Get active usernames (card selector).
     */
    public function getActiveUsernames(){
        return User::select('id', 'name')->where('isActive', 1)->get();
    }

    /**
     * Display paged listing of the resource.
     */
    public function getPage($page = 0, $columns = '*', $where = ""){ 
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        // Get count of all items meeting the condition:
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $Users = DB::table('users')->select("*")->whereRaw("users.isActive = 1")->take($itemsPerPage);
        $TotalUsers = DB::table('users')->selectRaw("count(*) as counter")->whereRaw("users.isActive = 1");

        if($page != 0){
            $page--;
            $Users = $Users->skip($page*$itemsPerPage);
        }
        if(strlen($where) != 0){
            $Users = $Users->whereRaw($where);
            $TotalUsers = $TotalUsers->whereRaw($where);
        }
        $Users = $Users->get();
        $TotalUsers = $TotalUsers->first()->counter;

        $returnObject = [
            "objects" => $Users,
            "currentPage" => $page,
            "totalPages" => (int) ceil($TotalUsers/$itemsPerPage),
        ];
        return $returnObject;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        return view("admin.users.create", [
            "groups" => DB::table('admin_groups')->select("*")->where("groupIsActive", 1)->get(),
            "randomPassword" => $this->randomPassword()
        ]);
    }

    /**
     * Show the form for editing a resource.
     */
    public function edit($id){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        return view("admin.users.edit", [
            "user" => User::find($id), 
            "groups" => DB::table('admin_groups')->select("*")->where("groupIsActive", 1)->get(),
            "roles" => RoleController::getActiveRoles(),
            "userRoles" => RoleController::getUserRoles($id),
            "randomPassword" => $this->randomPassword()
        ]);
    }

    /**
     * Update employee info
     */
    public function update(Request $request){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        $request->request->remove('fakeUsernameAutofill');
        if(strlen($request->input("password")) > 8){
            $password = password_hash($request->input("password"), PASSWORD_DEFAULT);
            $request->request->remove('password');
            $request->merge(['password' => $password]);
        }else{
            $request->request->remove('password');
        }

        $errorMessage = "";
        try{        
            $User = User::find($request->input("id"));
            $User->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("employee", ["id" => $request->input("id"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("employee", ["id" => $request->input("id"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Soft delete a resource.
     */
    public function remove(Request $request){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        $success = true;
        $errorMessage = "";
        try{
            $User = User::find($request->input('deleteEmployeeId'));
            $User->isActive = 0;
            $User->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route('employees', ["successMessage" => "Pomyślnie usunięto pracownika."]));
        }else{
            return redirect(route('employees', ["errorMessage" => $errorMessage]));
        }
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        if(!PC::cp('manage_employees')){
            abort(403);
        }
        $request->request->remove('fakeUsernameAutofill');

        // Hash password
        $password = password_hash($request->input("password"), PASSWORD_DEFAULT);
        $request->request->remove('password');
        $request->merge(['password' => $password]);
        // End of Hash password

        $User = new User($request->all());
        $insertUserSuccess = true;
        try{
            $User->save();
        }catch(\Throwable $e){
            $insertUserSuccess = false;
            $errorMessage = $e;
        }
        if($insertUserSuccess == true){
            return redirect(route("employees", ["successMessage" => "Pomyślnie utworzono zamówienie!"]));
        }else{
            return redirect(route("employee.create", ["errorMessage" => $errorMessage]));
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
}
