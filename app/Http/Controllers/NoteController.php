<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
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
    public function create($relator, $relatorId, $old = [])
    {
        $relatorMeta = $this->getRelatorMeta();
        return view("admin.notes.create", [
            "relatorSlug" => $relator,
            "relatorName" => $relatorMeta[$relator]["relatorName"],
            "relatorId" => $relatorId,
            "tinyMCEKey" => env("APP_TINYMCE_API"),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $Note = new Note($request->all());
        $insertNoteSuccess = true;
        try{
            $Note->save();
        }catch(\Throwable $e){
            $insertNoteSuccess = false;
            $errorMessage = $e;
        }
        if($insertNoteSuccess == true){
            return redirect(route("note.edit", ["id" => $Note->noteId, "successMessage" => "Pomyślnie dodano notatkę"]));
        }else{
            return redirect(route("note.create", ["relator" => $request->input("noteRelatorSlug"), "relatorId" => $request->input("noteRelatorId"), "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $Note = Note::find($id);
        $relatorMeta = $this->getRelatorMeta();
        return view("admin.notes.edit", [
            "noteId" => $Note->noteId,
            "noteTitle" => $Note->noteTitle,
            "noteContent" => $Note->noteContent,
            "noteBackButton" => $relatorMeta[$Note->noteRelatorSlug]["backButton"].$Note->noteRelatorId,
            "tinyMCEKey" => env("APP_TINYMCE_API"),
            "noteCreated" => $Note->created_at,
            "noteUpdated" => $Note->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $request->request->remove('fakeUsernameAutofill');
        $success = true;
        try{        
            $Note = Note::find($request->input("noteId"));
            $Note->fill($request->all())->save();
        }catch(\Throwable $e){
            $success = false;
            $errorMessage = $e;
        }
        if($success){
            return redirect(route("note.edit", ["id" => $Note->noteId, "successMessage" => "Pomyślnie zaktualizowano notatkę"]));
        }else{
            return redirect(route("note.edit", ["id" => $Note->noteId, "errorMessage" => $errorMessage]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        //
    }

    /**
     * Get relators info
     */
    private function getRelatorMeta(){
        return [
            "order" => [
                "relatorName" => "Zamówienie",
                "backButton" => "/admin".env("APP_ADMIN_POSTFIX")."/order/",
            ],
            "detail" => [
                "relatorName" => "Detal",
                "backButton" => "/admin".env("APP_ADMIN_POSTFIX")."/detail/",
            ]
        ];
    }
}
