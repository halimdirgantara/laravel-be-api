<?php

namespace App\Services;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class FileService
{
    public function getFile($id) {
        $file = File::find($id);
        if(!$file){
            return response()->json([
                'message' => 'File not found'
            ],404);
        }
        return $file;
    }

    public function storeFile($slug, $description, $path, $size, $file_type, $user_id) {
        //Upload the file
        $file = File::create([
            'name' => $slug,
            'file' => $path,
            'path' => $path,
            'file_type' => $file_type,
            'description' => $description,
            'size' => $size,
            'user_id' => $user_id,
        ]);
        return $file;
    }

    public function checkFileOwnership($id) {
        $user = Auth::user();
        if($id != $user->id && (!$user->hasRole('Admin') || !$user->hasRole('Super Admin')) ) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        return true;
    }
}
