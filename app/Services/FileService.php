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
