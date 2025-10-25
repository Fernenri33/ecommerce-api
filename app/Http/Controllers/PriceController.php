<?php

namespace App\Http\Controllers;

use App\DTOs\PriceDTO;
use App\DTOs\PriceUpdateDTO;
use App\Models\Price;
use App\Services\PriceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    use AuthorizesRequests;
    protected $priceService;

    public function __construct(PriceService $priceService){
        $this->priceService = $priceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', arguments: Price::class);
        $name = $request->query('name');
        $result = (!empty($name)) ? $this->priceService->findPriceByName($name) : $this->priceService->getAllPrices();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', arguments: Price::class);

        $priceDTO = PriceDTO::fromRequest($request);
        $result = $this->priceService->createPrice($priceDTO);

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', arguments: Price::class);

        $result = $this->priceService->getPriceById($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', arguments: Price::class);

        $priceUpdateDTO = PriceUpdateDTO::fromRequest($request, $id);
        $result = $this->priceService->updatePrice($id, $priceUpdateDTO);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->authorize('detele', arguments: Price::class);
         $result = $this->priceService->deletePrice($id);
         return response()->json($result);

    }
}
