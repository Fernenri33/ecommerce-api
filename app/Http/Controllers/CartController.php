<?php

namespace App\Http\Controllers;

use App\DTOs\CartDTO;
use App\DTOs\CartUpdateDTO;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use AuthorizesRequests;
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Display a listing of the resource.
     * Pasa como parametro el id del usuario dueño del carrito o devuelve todos los carritos
     */
    public function index(Request $request)
    {
        // Policy: usuario autenticado puede ver sus carritos
        $this->authorize('viewAny', Cart::class);

        // Tomamos el id del usuario autenticado
        $userId = $request->user()->id;

        // Tu servicio ya está pensado para esto: findCartsByUser($userId)
        $result = $this->cartService->findCartsByUser($userId);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Cart::class);

        $cartDTO = CartDTO::fromRequest($request);
        $res = $this->cartService->createCart($cartDTO);
        return response()->json($res);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', Cart::class);
        $res = $this->cartService->find($id);
        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Cart::class);
        $cartUpdateDTO = CartUpdateDTO::fromRequest($request);
        $res = $this->cartService->updateCart($id, $cartUpdateDTO);
        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Cart::class);
        $res = $this->cartService->deleteCart($id);
        return response()->json($res);
    }
}
