<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PermissionsController as PC;
use DB;

class ToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($status, $getPage = 0, $errorMessage = ""){ 
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        if($status != "all"){
            $where = 'toolStatus = "'.$status.'"';
        }else{
            $where = "";
        }
        $orderBy = array(
            array("+toolStatus", "'damaged', 'workbench', 'available'"),
            array("updated_at", "ASC"),
        );
        $tools = $this->getPage($getPage, "*", $where, $orderBy);
        $toolStatuses = $this->getToolStatuses();
        $tools["objects"] = $this->fillToolsWithClasses($tools["objects"]);
        $tools["objects"] = $this->fillToolsWithTranslations($tools["objects"]);
        $tools["objects"] = $this->fillToolsWithMTBF($tools["objects"]);
        if($getPage == 0){
            $getPage = 1;
        }
        return view("admin.tools.index", [
            "tools" => $tools["objects"],
            "pageHeader" => "Narzędzia",
            "toolStatuses" => $toolStatuses,
            "currentPage" => (int) $getPage,
            "totalPages" => (int) $tools["totalPages"],
            "currentRoute" => "admin".env('APP_ADMIN_POSTFIX').".tools.".$status,
            "errorMessage" => $errorMessage
        ]);
    }

    /**
     * Get specified page of tools.
     */
    public function getPage($page = 0, $columns = '*', $where = "", $sortOrder = null){ 
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        // Get count of all items meeting the condition:
        $itemsPerPage = env("APP_ITEMS_PER_PAGE");
        $Tools = DB::table('tools')->select($columns)->take($itemsPerPage);
        $TotalTools = DB::table('tools')->selectRaw("count(*) as counter");

        if($page != 0){
            $page--;
            $Tools = $Tools->skip((int) $page * (int) $itemsPerPage);
        }
        if(strlen($where) != 0){
            $Tools = $Tools->whereRaw($where);
            $TotalTools = $TotalTools->whereRaw($where);
        }
        if($sortOrder != null){
            foreach($sortOrder as $sortOrder){
                if($sortOrder[1] != "ASC" && $sortOrder[1] != "DESC" && substr($sortOrder[0], 0, 1) != '+'){
                    $sortOrder[1] = "ASC";
                }
                if(substr($sortOrder[0], 0, 1) == '-'){      
                    $withoutMinus = ltrim($sortOrder[0], '-');
                    $toolByRaw = '-`'.$withoutMinus.'`'." ".$sortOrder[1];
                    $Tools = $Tools->orderByRaw($toolByRaw); 
                }else if(substr($sortOrder[0], 0, 1) == '+'){      
                    $withoutPlus = ltrim($sortOrder[0], '+');
                    $toolByRaw = "FIELD($withoutPlus, ".$sortOrder[1].")";
                    $Tools = $Tools->orderByRaw($toolByRaw); 
                }else{
                    $Tools = $Tools->orderBy($sortOrder[0], $sortOrder[1]);
                }   
            }
        }
        $Tools = $Tools->get();
        $TotalTools = $TotalTools->first()->counter;

        $returnObject = [
            "objects" => $Tools,
            "currentPage" => $page,
            "totalPages" => (int) ceil($TotalTools/$itemsPerPage),
        ];
        return $returnObject;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        return view("admin.tools.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        $request->request->remove('fakeUsernameAutofill');
        $request->merge(['toolUpdatedBy' => Auth::id()]);
        $Tool = new Tool($request->all());
        $insertToolSuccess = true;
        try{
            $Tool->save();
        }catch(\Throwable $e){
            $insertToolSuccess = false;
            $errorMessage = $e;
        }
        if($insertToolSuccess){
            return redirect(route("tool.edit", ["id" => $Tool->toolId, "successMessage" => "Pomyślnie utworzono narzędzie!"]));
        }else{
            return redirect(route("tool.create", ["errorMessage" => $errorMessage]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        $tool = Tool::find($id);
        $toolStatuses = $this->getToolStatuses();
        $tool = $this->fillToolsWithTranslations($tool, true);
        return view("admin.tools.edit", [
            "tool" => $tool,
            "toolStatuses" => $toolStatuses
        ]);
    }

    /**
     * Update order info
     */
    public function update(Request $request){
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        $errorMessage = "";
        try{        
            $Tool = Tool::find($request->input("editToolId"));
            $Tool->fill($request->all())->save();
        }catch(\Throwable $e){
            $errorMessage = $e;
        }
        if($errorMessage == ""){
            return redirect(route("tool.edit", ["id" => $request->input("editToolId"), "successMessage" => "Pomyślnie zaktualizowano!"]));
        }else{
            return redirect(route("tool.edit", ["id" => $request->input("editToolId"), "errorMessage" => $errorMessage]));
        }
    }


    /**
     * Remove tool
     */
    public function remove(Request $request){
        if(!PC::cp('manage_tools')){
            abort(403);
        }
        $success = true;
        $errorMessage = "";
        try{
            Tool::where('toolId', $request->input('deleteToolId'))->delete();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route('tools.status', ["status" => "all", "successMessage" => "Pomyślnie usunięto narzędzie."]));
        }else{
            return redirect(route('tools.status', ["status" => "all", "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Get last five damaged tools
     */
    public function getLastFiveDamagedTools(){
        if(!PC::cp('manage_tools')){
            return null;
        }
        $success = true;
        $errorMessage = "";
        try{
            $Tools = DB::table("tools")->whereRaw('toolStatus = "damaged"')->orderBy("updated_at", "DESC")->limit(5)->get();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        $toolStatuses = $this->getToolStatuses();
        $Tools = $this->fillToolsWithClasses($Tools);
        $Tools = $this->fillToolsWithTranslations($Tools);
        return $Tools;
    }

    /**
     * Get avaliable in system statuses of tool
     */
    private function getToolStatuses(){
        return [
            "available" => [
                "translation" => "Sprawne",
                "slug" => "available",
                "colorClass" => ""
            ],
            "damaged" => [
                "translation" => "Uszkodzone",
                "slug" => "damaged",
                "colorClass" => "bg-danger"
            ],
            "workbench" => [
                "translation" => "W naprawie",
                "slug" => "workbench",
                "colorClass" => "bg-warning"
            ],
        ];
    }

    /**
     * Fill tools with clases (based for example on status )
     */
    private function fillToolsWithClasses($tools, $single = false){
        $statusClassesColors = $this->getToolStatuses();
        if($single == true){ // Single element
            $tools->toolClasses = $statusClassesColors[$tools->toolStatus]["colorClass"];
        }else{ // Multiple elements
            for($i=0; $i<$tools->count(); $i++){
                $tools[$i]->toolClasses = $statusClassesColors[$tools[$i]->toolStatus]["colorClass"];
            }
        }
        return $tools;
    }

    /**
     * Fill tools with translations
     */
    private function fillToolsWithTranslations($tools, $single = false){
        $statusClassesColors = $this->getToolStatuses();
        if($single == true){ // Single element
            $tools->toolStatusMnemonic = $statusClassesColors[$tools->toolStatus]["translation"];
        }else{ // Multiple elements
            for($i=0; $i<$tools->count(); $i++){
                $tools[$i]->toolStatusMnemonic = $statusClassesColors[$tools[$i]->toolStatus]["translation"];
            }
        }
        return $tools;
    }

     /**
     * Fill tools with MTBF date
     */
    private function fillToolsWithMTBF($tools, $single = false){
        $statusClassesColors = $this->getToolStatuses();
        if($single == true){ // Single element
            $tools->toolLastRepaired = $this->calculateDatesDifference(now(), $tools->updated_at, $outputType="days");
        }else{ // Multiple elements
            for($i=0; $i<$tools->count(); $i++){
                $tools[$i]->toolLastRepaired = $this->calculateDatesDifference(now(), $tools[$i]->updated_at, $outputType="days");
            }
        }
        return $tools;
    }

    
    /**
     * Calculate time between days
     */
    private function calculateDatesDifference($firstDate, $secondDate, $outputType="days"){
        $firstDate = strtotime($firstDate);
        $secondDate = strtotime($secondDate);
        $dateDiff = $secondDate - $firstDate;
        if($outputType == "days"){
            return ceil($dateDiff / (60 * 60 * 24));
        }
    }
}
