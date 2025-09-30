<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {
        $result = $this->productService->getAllProducts();
        return response()->json($result, $result['status_code'] ?? 200);
    }
    public function store(Request $request)
    {
        try {
            $productDTO = ProductDTO::fromRequest($request);
            $result = $this->productService->createProduct($productDTO);
            return response()->json($result, $result['status_code'] ?? 200);

        } catch (\InvalidArgumentException $e) {
            return response()->json($result, $result['status_code'] ?? 200);
        }
    }
    public function show($id)
    {
        $result = $this->productService->findProductById($id);
        return response()->json($result);
    }
    public function destroy($id){
        $result = $this->productService->deleteProduct($id);
        return response()->json($result);
    }

}
