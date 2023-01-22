<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string',
            'file' => 'required|file|mimetypes:application/pdf,
                        application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                        application/vnd.ms-excel,
                        application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                        application/jpeg,application/png,application/jpg,application/gif
                        ,application/webp, application/svg',
            'description' => 'nullable|string',
        ]);

        // Handle the file upload
        $file = $request->file('file');
        $path = $file->store('files');
        $size = $file->getSize();
        $file_type = $file->getMimeType();

        $file = File::create([
            'name' => $validatedData['name'],
            'file' => $path,
            'path' => $path,
            'file_type' => $file_type,
            'description' => $validatedData['description'],
            'size' => $size
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => $file
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
