<?php

namespace App\Http\Controllers;

use App\DTOs\CategoryDTO;
use App\DTOs\CategoryUpdateDTO;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    protected $categoryService;

    public function __construct(CategoryService $categoryService){
        $this->categoryService = $categoryService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $name = $request->query('name');
        $res = (!empty($name)) ? $this->categoryService
            ->findCategoryByName($name) :
            $this->categoryService->getAllCategories();

        return response()->json($res);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $categoryDTO = CategoryDTO::fromRequest($request);
        $res = $this->categoryService->createCategory($categoryDTO);
        
        return response()->json($res);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', Category::class);

        $res = $this->categoryService->findCategoryById($id);
        
        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Category::class);

        $categoryDTO = CategoryUpdateDTO::fromRequest($request, $id);
        $res = $this->categoryService->updateCategory($id, $categoryDTO);

        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Category::class);

        $res = $this->categoryService->deleteCategory($id);
        
        return response()->json($res);
    }
}