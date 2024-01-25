<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $destinationPath = $this->createTodaysPath();
        $file = $request->file('attachmentContent');
        $extension = $file->extension();
        $fileName = rand(1000,9999)."-".$file->getClientOriginalName();
        $success = true;
        try{
            $request->file('attachmentContent')->move(storage_path().$destinationPath, $fileName);
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            // File moved. Save to database.
            $Attachment = new Attachment(array_merge($request->all(), ['attachmentPath' => $destinationPath.$fileName]));
            try{
                $Attachment->save();
            }catch(\Throwable $e){    
                $success = false;
                $errorMessage = $e;
            }
            if($success){
                return redirect(route($request->input("attachmentRelatorSlug").".edit", ["id" => $request->input("attachmentRelatorId"), "successMessage" => "Pomyślnie dodano załącznik"]));
            }else{
                return redirect(route($request->input("attachmentRelatorSlug").".edit", ["id" => $request->input("attachmentRelatorId"), "errorMessage" => $errorMessage]));
            }
        }else{
            return redirect(route($request->input("attachmentRelatorSlug").".edit", ["id" => $request->input("attachmentRelatorId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attachment $attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attachment $attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        //
    }

    /**
     * Create current date file path, if doesn't exist.
     */
    private function createTodaysPath(){
        $todaysPath = "/uploaded/".date("Y")."/".date("m");
        if(!is_dir(storage_path().$todaysPath)){
            mkdir(storage_path().$todaysPath, 0777, true);
        }
        return $todaysPath."/";
    }
}
