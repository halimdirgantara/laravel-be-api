<?php

namespace App\Services;
use App\Models\File;

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

    public function checkFileOwnership($id) {
        $file = File::find($id);
        $user = auth()->user();
        if($file->user_id !== $user->id || (!$user->hasRole('Admin') || !$user->hasRole('Super Admin')) ) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        return $file;
    }
}
