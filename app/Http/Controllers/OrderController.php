<?php

namespace App\Http\Controllers;

use App\DTOs\OrderDTO;
use App\DTOs\OrderUpdateDTO;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use AuthorizesRequests;
    protected $orderService;
    public function __construct(OrderService $orderService){
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Order::class);

        $response = $this->orderService->getAllOrders();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $orderDTO = OrderDTO::fromRequest($request);
        $res = $this->orderService->createOrder($orderDTO);
        return response()->json($res);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', Order::class);
        $res = $this->orderService->findOrderById($id);

        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update',Order::class);
        $orderUpdateDTO = OrderUpdateDTO::fromRequest($request);
        $res = $this->orderService->updateOrder($id, $orderUpdateDTO);

        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete',Order::class);
        $res = $this->orderService->deleteOrder($id);
        return response()->json($res);
    }
}
