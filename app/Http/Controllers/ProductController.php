<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\DTOs\ProductUpdateDTO;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $name = $request->query('name');
        $result = (!empty($name)) ? $this->productService->findProductByName($name) : $this->productService->getAllProducts();

        return response()->json($result);
    }

    public function show($id)
    {
        $this->authorize('view', Product::class);

        $result = $this->productService->findProductById($id);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $productDTO = ProductDTO::fromRequest($request);
        $result = $this->productService->createProduct($productDTO);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('update', Product::class);

        $productDTO = ProductUpdateDTO::fromRequest($request, $id);
        $result = $this->productService->updateProduct($id, $productDTO);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->authorize('delete', Product::class);

        $result = $this->productService->deleteProduct($id);
        return response()->json($result);
    }
}