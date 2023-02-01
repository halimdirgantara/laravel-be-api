<?php

namespace App\Http\Controllers\API;

use App\Models\File;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        // get the file by user id
        $files = File::where('user_id', $user->id)->paginate(10);
        // get all the files if Admin or Super Admin
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin')) {
            $files = File::paginate(10);
        }

        // dd(count($files));
        $message = 'File retrieved successfully';
        if (count($files) < 1) {
            $message = 'No files were retrieved';
            $files = NULL;
        }

        return response()->json([
            'message' => $message,
            'data' => $files,
        ], 200);
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
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx,xls,xlsx,csv,pptx,ppt,pps,ppsx',
            'description' => 'nullable|string',
        ]);

        // Handle the file upload
        $file = $request->file('file');
        $path = $file->store('files');
        $size = $file->getSize();
        $file_type = $file->getMimeType();
        $user_id = Auth::user()->id;

        $file = File::create([
            'name' => $validatedData['name'],
            'file' => $path,
            'path' => $path,
            'file_type' => $file_type,
            'description' => $validatedData['description'],
            'size' => $size,
            'user_id' => $user_id,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => $file,
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
        //get file by id
        $file = $this->fileService->getFile($id);
        //check file ownership
        $this->fileService->checkFileOwnership($file->user_id);
        return response()->json([
            'message' => 'File retrieved successfully',
            'data' => $file,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //get file by id
        $file = $this->fileService->getFile($id);
        //check file ownership
        $this->fileService->checkFileOwnership($file->user_id);

        return response()->json([
            'message' => 'File retrieved successfully',
            'data' => $file,
        ], 200);
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
        $validatedData = $request->validate([
            'name' => 'required|string',
            'file' => 'nullable|file',
            'description' => 'nullable|string',
        ]);

        // get the file
        $file = $this->fileService->getFile($id);

        //check if the file owner is user except Admin or Super Admin
        $file = $this->fileService->checkFileOwnership($id);

        if ($request->hasFile('file')) {
            // Handle the file upload
            $newFile = $request->file('file');
            $path = $newFile->store('files');
            $size = $newFile->getSize();
            $file_type = $newFile->getMimeType();

            //delete old file
            Storage::delete($file->file);

            $file->name = $validatedData['name'];
            $file->file = $path;
            $file->path = $path;
            $file->file_type = $file_type;
            $file->size = $size;
            $file->description = $validatedData['description'];
            $file->save();
        } else {
            $file->name = $validatedData['name'];
            $file->description = $validatedData['description'];
            $file->save();
        }

        return response()->json([
            'message' => 'File updated successfully',
            'data' => $file,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = $this->fileService->getFile($id);

        $file = $this->fileService->checkFileOwnership($id);

        //delete file from storage
        Storage::delete($file->file);
        //delete file from database
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
        ], 200);
    }
}
