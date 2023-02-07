<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
        //get categories
        $categories = Category::get();

        $message = 'Category retrieved successfully';
        if (count($categories) < 1) {
            $message = 'No Category were retrieved';
            $categories = null;
        }

        return response()->json([
            'message' => $message,
            'data' => $categories,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        // Validate the request 'name', 'slug', 'description', 'file_id'
        $validatedData = $request->validate([
            'name' => 'required|string',
            'file' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:1024',
            'description' => 'nullable|string',
        ]);
        $slug = Str::slug($validatedData['name']);
        $description = $request->description ? $validatedData['description'] : 'Category';
        $file_id = null;
        if ($request->file) {
            // Handle the file upload
            $file = $request->file('file');
            $path = $file->store('files');
            $size = $file->getSize();
            $file_type = $file->getMimeType();
            $user_id = Auth::user()->id;
            $file_upload = $this->fileService->storeFile($slug, $description, $path, $size, $file_type, $user_id);
            $file_id = $file_upload->id;
        }

        //Save category
        $category = Category::create([
            'name' => $validatedData['name'],
            'description' => $description,
            'slug' => $slug,
            'file_id' => $file_id,
        ]);

        return response()->json([
            'message' => 'Category added successfully',
            'data' => $category->load('file'),
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
        $category = Category::with('file')->find($id);

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::with('file')->find($id);

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category,
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
        // Validate the request 'name', 'slug', 'description', 'file_id'
        $validatedData = $request->validate([
            'name' => 'string',
            'file' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:1024',
            'description' => 'nullable|string',
        ]);

        try {

            $category = Category::find($id);
            $oldFile = $this->fileService->getFile($category->file_id);

            $name = $request->name ? $validatedData['name'] : $category->name;
            $description = $request->description ? $validatedData['description'] : $category->description;
            $slug = Str::slug($name);
            $file_id = $category->file_id;
            if ($request->file) {
                // Handle the file upload
                $newFile = $request->file('file');
                $path = $newFile->store('files');
                $size = $newFile->getSize();
                $file_type = $newFile->getMimeType();
                $user_id = Auth::user()->id;
                $file_upload = $this->fileService->storeFile($slug, $description, $path, $size, $file_type, $user_id);
                $file_id = $file_upload->id;

                //delete old file
                Storage::delete($oldFile->file);

                $category->name = $name;
                $category->slug = $slug;
                $category->description = $description;
                $category->file_id = $file_id;
                $category->save();
            } else {
                $category->name = $name;
                $category->slug = $slug;
                $category->description = $description;
                $category->file_id = $file_id;
                $category->save();
            }

            return response()->json([
                'message' => 'File updated successfully',
                'data' => $category->load('file'),
            ], 200);

        } catch (\Throwable$th) {
            return response()->json([
                'message' => $th,
            ], 401);
        }
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
