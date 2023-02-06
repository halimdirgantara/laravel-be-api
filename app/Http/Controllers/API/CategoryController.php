<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        $description = $request->description ? $validatedData['description'] : 'Category Image';
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
            'data' => $category,
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
