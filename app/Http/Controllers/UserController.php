<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use App\DTOs\UserUpdateDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view', User::class);

        $name = $request->query('name');
        $result = (!empty($name)) ? $this->userService->findUserByName($name) : $this->userService->getAllUsers();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',User::class);
        $userDTO = UserDTO::fromRequest($request);
        $res = $this->userService->createUser($userDTO);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', User::class);
        
        $response = $this->userService->findUserById($id);
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit',User::class);
        $userUpdateDTO = UserUpdateDTO::fromRequest($request,$id);
        $res = $this->userService->updateUser($userUpdateDTO,$id);
        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete',User::class);
        $res = $this->userService->deleteUser($id);

        return response()->json($res);
    }
}
