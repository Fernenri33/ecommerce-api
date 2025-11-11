<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogService
{
    public function debugSubcategory(int $subcategoryId)
{
    // 1. Verificar productos sin filtros
    $totalProducts = Product::count();
    
    // 2. Verificar productos con status active
    $activeProducts = Product::where('status', 'active')->count();
    
    // 3. Verificar productos con la subcategoría
    $withSubcategory = Product::whereHas('subcategories', function($q) use ($subcategoryId) {
        $q->where('subcategories.id', $subcategoryId);
    })->count();
    
    // 4. Verificar productos con subcategoría Y status active
    $withSubcategoryActive = Product::where('status', 'active')
        ->whereHas('subcategories', function($q) use ($subcategoryId) {
            $q->where('subcategories.id', $subcategoryId);
        })->count();
    
    // 5. Ver el SQL real que se está generando
    $query = Product::query()
        ->sellable()
        ->whereHas('subcategories', fn($qs) => $qs->whereIn('subcategories.id', [$subcategoryId]));
    
    return [
        'total_products' => $totalProducts,
        'active_products' => $activeProducts,
        'with_subcategory' => $withSubcategory,
        'with_subcategory_active' => $withSubcategoryActive,
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings(),
    ];
}
    /**
     * Devuelve un producto con sus prices vendibles (+ discount si está activo).
     */
    public function getProductsWithPrices(int $productId): Product
    {
        return Product::query()
            ->sellable()
            ->with([
                'prices' => fn($q) => $q->sellable()->orderBy('price', 'asc'),
                'prices.discount' => fn($q) => $q->where('status', 'active'),
                'unit',
            ])
            ->findOrFail($productId);
    }


    /**
     * Lista general de productos vendibles con filtros.
     * Filtros soportados:
     * - q (string): búsqueda por nombre/sku
     * - category_id (int)
     * - subcategory_id (int|array)
     * - min_price, max_price (int, en centavos)
     * - available_only (bool)
     * - sort: 'price_asc' | 'price_desc' | 'newest' | 'oldest'
     * - per_page (int)
     */
    public function getProducts(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 20);

        $query = Product::query()
            ->sellable()
            ->when(
                !empty($filters['available_only']),
                fn($q) =>
                $q->where('available_quantity', '>', 0)
            )
            ->when(!empty($filters['q']), function ($q) use ($filters) {
                $term = trim($filters['q']);
                $q->where(
                    fn($qq) =>
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                );
            })
            ->when(
                !empty($filters['category_id']),
                fn($q) =>
                $q->whereHas(
                    'subcategories.category',
                    fn($qc) => $qc->where('categories.id', (int) $filters['category_id'])
                )
            )
            ->when(
                !empty($filters['subcategory_id']),
                fn($q) =>
                $q->whereHas(
                    'subcategories',
                    fn($qs) => $qs->whereIn('subcategories.id', (array) $filters['subcategory_id'])
                )
            )
            // limitar por rango de precio usando el scope del Price:
            ->when(isset($filters['min_price']) || isset($filters['max_price']), function ($q) use ($filters) {
                $q->whereHas('prices', function ($qp) use ($filters) {
                    $qp->sellable()
                        ->when(isset($filters['min_price']), fn($qq) => $qq->where('price', '>=', (int) $filters['min_price']))
                        ->when(isset($filters['max_price']), fn($qq) => $qq->where('price', '<=', (int) $filters['max_price']));
                });
            })
            ->with([
                'prices' => fn($q) => $q->sellable()->orderBy('price', 'asc'),
                'prices.discount' => fn($q) => $q->where('status', 'active'),
                'unit',
            ]);

        // ordenamiento por precio mínimo vendible o por fecha
        $sort = $filters['sort'] ?? 'price_asc';
        match ($sort) {
            'price_desc' => $query->withMin(['prices as min_sellable_price' => fn($q) => $q->sellable()], 'price')
                ->orderBy('min_sellable_price', 'desc'),
            'newest' => $query->orderBy('id', 'desc'),
            'oldest' => $query->orderBy('id', 'asc'),
            default => $query->withMin(['prices as min_sellable_price' => fn($q) => $q->sellable()], 'price')
                ->orderBy('min_sellable_price', 'asc'),
        };

        return $query->paginate($perPage);
    }

    public function getProductsByCategory(int $categoryId, array $opts = []): LengthAwarePaginator
    {
        $opts['category_id'] = $categoryId;
        return $this->getProducts($opts);
    }

    public function getProductsBySubcategory(int $subcategoryId, array $opts = []): LengthAwarePaginator
    {
        $opts['subcategory_id'] = $subcategoryId;
        return $this->getProducts($opts);
    }
}