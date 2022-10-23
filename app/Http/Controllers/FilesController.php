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
        $fileName = Str::uuid()->toString() . '.' . $request->file->getClientOriginalExtension();
        $request->file->storeAs('uploads', $fileName, 'public');;
        $file = new File();
        $file->filename = $fileName;
        $file->user_id = $request->user()->id;
        $file->type = $request->file->getClientMimeType();
        $file->size = $request->file->getSize();
        $file->save();
        return response()->json($file, 201);
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
