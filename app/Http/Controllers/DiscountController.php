<?php

namespace App\Http\Controllers;

use App\DTOs\DiscountDTO;
use App\DTOs\DiscountUpdateDTO;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    use AuthorizesRequests;
    protected $discountService;
    public function __construct(DiscountService $discountService){
        $this->discountService = $discountService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Discount::class);

        $name = $request->query('name');
        $result = (!empty($name)) ? $this->discountService->findProductByName($name) : $this->discountService->getAllDiscounts();

        return response()->json($result);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Discount::class);

        $discountDTO = DiscountDTO::fromRequest($request);
        $result = $this->discountService->createDiscount($discountDTO);
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view',Discount::class);
        $response = $this->discountService->findDiscountById($id);
        return response()->json($response);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $this->authorize('update', Discount::class);
        $discountUpdate = DiscountUpdateDTO::fromRequest($request);
        $result = $this->discountService->updateDiscount($id, $discountUpdate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this ->authorize('delete', Discount::class);

        $result = $this->discountService->deleteDiscount($id);
        return response()->json($result);
    }
}
