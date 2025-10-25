<?php

namespace App\Http\Controllers;

use App\DTOs\UserAddressDTO;
use App\DTOs\UserAddressUpdateDTO;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Services\UserAddressesService;
use LDAP\Result;

class UserAddressController extends Controller
{
    protected $userAddressesService;
    public function __construct(UserAddressesService $userAddressesService)
    {
        $this->userAddressesService = $userAddressesService;
    }
    /**
     * Display a listing of the resource.
     * Este index funciona diferente
     * Esta entidad no contiene el atributo 'name'
     * Por lo que será sustituido por una query a los atributos que la dirección posee
     */

    // Si se está creando la documentación por favor tener esto en cuenta, gracias
    public function index(Request $request)
    {
        $this->authorize('view', arguments: UserAddress::class);
        $place = $request->query('place');
        $result = (!empty($place)) ? $this->userAddressesService->findUserAddressesByPlace($place) : 
        $this->userAddressesService->getAllUserAdresses();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', arguments: UserAddress::class);
        $userAddress = UserAddressDTO::fromRequest($request);
        $result = $this->userAddressesService->createUserAddress($userAddress);
        
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', arguments: UserAddress::class);
        $result = $this->userAddressesService->find($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', arguments: UserAddress::class);
        $userAddressUpdateDTO = UserAddressUpdateDTO::fromRequest($request);
        $result = $this->userAddressesService->updateUserAddress($id, $userAddressUpdateDTO);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', arguments: UserAddress::class);
        $result = $this->userAddressesService->deleteUserAddress($id);
        return response()->json($result);
    }
}
