<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;
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

        $message = 'File retrieved successfully';
        if (count($files) < 1) {
            $message = 'No files were retrieved';
            $files = null;
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

        //Save the file
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
        //validate request
        $validatedData = $request->validate([
            'name' => 'required|string',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx,xls,xlsx,csv,pptx,ppt,pps,ppsx',
            'description' => 'nullable|string',
        ]);

        // get the file
        $file = $this->fileService->getFile($id);

        //check if the file owner is user except Admin or Super Admin
        $this->fileService->checkFileOwnership($file->user_id);

        if ($request->hasFile('file')) {
            // Handle the file upload
            $newFile = $request->file('file');
            $path = $newFile->store('files');
            $size = $newFile->getSize();
            $file_type = $newFile->getMimeType();
            $user_id = Auth::user()->id;

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
            // return response()->json([
            //     'message' => 'File can not be deleted from storage.',
            // ], 403);
        $file = $this->fileService->getFile($id);
        $this->fileService->checkFileOwnership($id);

        //how to check file that is not have foreign id in other tables?

        $relations = $file->has('categories')->first();
        if (empty($relations)) {
            // The file table doesn't have any relationships with other tables

            //delete file from storage
            Storage::delete($file->file);
            //delete file from database
            $file->delete();

            return response()->json([
                'message' => 'File deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'File failed to be deleted!',
            ], 401);
        }
    }
}
