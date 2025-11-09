<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use Authenticatable;
    protected $cartService;
    public function __construct(CartService $cartService){
        $this->cartService = $cartService;
    }
    /**
     * Display a listing of the resource.
     * Pasa como parametro el  id del usuario dueño del carrito o devuelve todos los carritos
     */
    public function index(Request $request)
    {
        $this->authorize('view',Cart::class);
        $cartId = $request->query('cartId');
        $result = (!empty($cartId)) ? $this->cartService->findCartsByUser($cartId) : $this->cartService->getAllCarts();
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
