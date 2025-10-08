<?php

namespace App\Http\Controllers;

use App\Services\RolService;
use Illuminate\Http\Request;
use App\DTOs\RolDTO;
use App\DTOs\RolUpdateDTO;

class RolController extends Controller
{
    protected $rolService;

    public function __construct(RolService $rolService){
        $this->rolService = $rolService;
    }
    public function index(Request $request)
    {
        $name = $request->query('name');
        $result = (!empty($name)) ? $this->rolService->findRolByName($name) : $this->rolService->getAllRoles();
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rolDTO = RolDTO::fromRequest($request);
            $result = $this->rolService->createRol($rolDTO);
            return response()->json($result);
        }catch (\InvalidArgumentException $e) {
            return response()->json($result);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $result = $this->rolService->findRolById($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $rolDTO = RolUpdateDTO::fromRequest($request, $id);
            $result = $this->rolService->updateRol($id, $rolDTO);
            return response()->json($result);

        } catch (\InvalidArgumentException $e) {
            return response()->json($result);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->rolService->deleteRol($id);
        return response()->json($result);
    }
}
