<?php

namespace App\Http\Controllers;

use App\DTOs\UserAddressDTO;
use App\DTOs\UserAddressUpdateDTO;
use App\Models\UserAddress;
use App\Services\UserAddressesService;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    protected $userAddressesService;

    public function __construct(UserAddressesService $userAddressesService)
    {
        $this->userAddressesService = $userAddressesService;
    }

    /**
     * Lista direcciones del usuario autenticado (con filtro opcional ?place=)
     */
public function index(Request $request)
{
    \Log::info('UserAddresses index HIT'); // <--- debug

    $user = $request->user();
    \Log::info('User in index', ['id' => $user?->id]);

    $this->authorize('viewAny', [UserAddress::class, $user]);

    $place = $request->query('place');

    $result = $this->userAddressesService
        ->getAddressesForUser($user, $place);

    return response()->json($result);
}


    /**
     * Crea nueva dirección para el usuario autenticado
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $this->authorize('create', [UserAddress::class, $user]);

        $dto = UserAddressDTO::fromRequest($request);

        $result = $this->userAddressesService
            ->createUserAddress($user, $dto);

        return response()->json($result);
    }

    /**
     * Muestra una dirección del usuario (si es suya)
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Primero la buscamos por user en el service
        $result = $this->userAddressesService
            ->getAddressForUserById($user, (int) $id);

        // Si tu helper devuelve el modelo en $result['content'], puedes autorizar con él
        if (isset($result['content']) && $result['content'] instanceof UserAddress) {
            $this->authorize('view', $result['content']);
        }

        return response()->json($result);
    }

    /**
     * Actualiza una dirección del usuario (si es suya)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $dto = UserAddressUpdateDTO::fromRequest($request);

        $result = $this->userAddressesService
            ->updateUserAddress($user, (int) $id, $dto);

        if (isset($result['content']) && $result['content'] instanceof UserAddress) {
            $this->authorize('update', $result['content']);
        }

        return response()->json($result);
    }

    /**
     * Elimina una dirección del usuario (si es suya)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // Podrías autorizar por modelo si lo traes primero
        // pero como en el service ya se valida user_id, aquí basta capacidad general
        $this->authorize('delete', [UserAddress::class, $user]);

        $result = $this->userAddressesService
            ->deleteUserAddress($user, (int) $id);

        return response()->json($result);
    }
}
