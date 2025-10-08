<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\DTOs\ProductUpdateDTO;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $name = $request->query('name');

        $result = (!empty($name)) ? $this->productService->findProductByName($name) : $this->productService->getAllProducts();

        return response()->json($result);
    }
    public function show($id)
    {
        $result = $this->productService->findProductById($id);
        return response()->json($result);
    }
    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);
        return response()->json($result);
    }
    public function store(Request $request)
    {
        try {
            $productDTO = ProductDTO::fromRequest($request);
            $result = $this->productService->createProduct($productDTO);
            return response()->json($result);

        } catch (\InvalidArgumentException $e) {
            return response()->json($result);
        }
    }
    public function update(Request $request, int $id)
    {
        try {
            $productDTO = ProductUpdateDTO::fromRequest($request, $id);
            $result = $this->productService->updateProduct($id, $productDTO);
            return response()->json($result);

        } catch (\InvalidArgumentException $e) {
            return response()->json($result);
        }
    }

}
