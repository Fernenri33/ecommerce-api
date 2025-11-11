<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CatalogController extends Controller
{
    protected CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * GET /api/catalog
     * Lista general de productos con filtros (búsqueda, categoría, subcategoría, etc.)
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q'              => 'nullable|string|max:255',
            'category_id'    => 'nullable|integer|exists:categories,id',
            'subcategory_id' => 'nullable',
            'min_price'      => 'nullable|integer|min:0',
            'max_price'      => 'nullable|integer|min:0',
            'available_only' => 'nullable|boolean',
            'sort'           => 'nullable|in:price_asc,price_desc,newest,oldest',
            'per_page'       => 'nullable|integer|min:1|max:100',
        ]);

        // soporta array de subcategorías
        if (isset($validated['subcategory_id']) && is_string($validated['subcategory_id'])) {
            $validated['subcategory_id'] = array_filter(explode(',', $validated['subcategory_id']));
        }

        $products = $this->catalogService->getProducts($validated);

        return response()->json([
            'success' => true,
            'message' => 'Listado de productos',
            'data' => $products,
        ]);
    }

    /**
     * GET /api/catalog/{id}
     * Muestra un producto con sus prices y descuentos activos.
     */
    public function show(int $id)
    {
        try {
            $product = $this->catalogService->getProductsWithPrices($id);

            return response()->json([
                'success' => true,
                'message' => 'Detalle de producto',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'product' => 'Producto no encontrado o no disponible.',
            ]);
        }
    }

    /**
     * GET /api/catalog/category/{id}
     * Productos filtrados por categoría.
     */
    public function byCategory(int $id, Request $request)
    {
        $validated = $request->validate([
            'q'              => 'nullable|string|max:255',
            'min_price'      => 'nullable|integer|min:0',
            'max_price'      => 'nullable|integer|min:0',
            'available_only' => 'nullable|boolean',
            'sort'           => 'nullable|in:price_asc,price_desc,newest,oldest',
            'per_page'       => 'nullable|integer|min:1|max:100',
        ]);

        $products = $this->catalogService->getProductsByCategory($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Productos por categoría',
            'data' => $products,
        ]);
    }

    /**
     * GET /api/catalog/subcategory/{id}
     * Productos filtrados por subcategoría.
     */
    public function bySubcategory(int $id, Request $request)
    {
        $debug = $this->catalogService->debugSubcategory($id);

        $validated = $request->validate([
            'q'              => 'nullable|string|max:255',
            'min_price'      => 'nullable|integer|min:0',
            'max_price'      => 'nullable|integer|min:0',
            'available_only' => 'nullable|boolean',
            'sort'           => 'nullable|in:price_asc,price_desc,newest,oldest',
            'per_page'       => 'nullable|integer|min:1|max:100',
        ]);

        $products = $this->catalogService->getProductsBySubcategory($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Productos por subcategoría',
            'data' => $products,
        ]);
    }
}
