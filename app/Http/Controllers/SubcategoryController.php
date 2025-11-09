<?php

namespace App\Http\Controllers;

use App\DTOs\SubcategoryDTO;
use App\DTOs\SubcategoryUpdateDTO;
use App\Models\Subcategory;
use App\Services\SubcategoryService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    use Authenticatable;
    protected $subcategoryService;

    public function __construct(SubcategoryService $subcategoryService){
        $this->subcategoryService = $subcategoryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny',Subcategory::class);

        $name = $request->query('name');
        $res = (!empty($name)) ? $this->subcategoryService
            ->findSubcategoryByName($name) :
            $this->subcategoryService->getAllSubcategories();

        return response()->json($res);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',Subcategory::class);

        $subcategoryDTO = SubcategoryDTO::fromRequest($request);
        $res = $this->subcategoryService->createSubCategory($subcategoryDTO);
        return response()->json($res);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view',Subcategory::class);

        $res = $this->subcategoryService->findSubcategoryById($id);
        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update',Subcategory::class);

        $subCategoryDTO = SubcategoryUpdateDTO::fromRequest($request);
        $res = $this->subcategoryService->updateSubCategory($id, $subCategoryDTO);

        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subcategory $subcategory)
    {
        //
    }
}
