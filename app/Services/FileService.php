<?php

namespace App\Services;
use App\Models\File;

class FileService extends Service
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
}
