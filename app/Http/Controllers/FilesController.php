<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\File;

class FilesController extends Controller {

    public function index(Request $request) {
        $files = File::all();
        return response()->json($files);
    }

    public function show(Request $request, $id) {
        $file = File::findOrFail($id);
        return response()->json($file);
    }

    public function create(Request $request) {
        $request->validate([
            'file' => 'required|max:500',
        ]);
        $file = $this->createFile($request->file, $request->user()->id);
        return response()->json($file, 201);
    }

    public function group(Request $request) {
        $request->validate([
            'files.*' => 'required|max:500'
        ]);
        $files = [];
        foreach($request->file('files') as $uploadedFile){
            $files[] = $this->createFile($uploadedFile, $request->user()->id);
        }
        return response()->json($files, 201);
    }

    private function createFile($requestFile, $userId) {
        $fileName = Str::uuid()->toString() . '.' . $requestFile->getClientOriginalExtension();
        $requestFile->storeAs('uploads', $fileName, 'public');;
        $file = new File();
        $file->filename = $fileName;
        $file->user_id = $userId;
        $file->type = $requestFile->getClientMimeType();
        $file->size = $requestFile->getSize();
        $file->save();
        return $file;
    }

    public function destroy(Request $request, $id) {
        $file = File::findOrFail($id);
        if($request->forceDelete === 'true') {
            Storage::disk('public')->delete("uploads/{$file->filename}");
            $file->forceDelete();
        } else {
            $file->delete();
        }
        return response()->json(null, 204);
    }
}
