<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class RolUpdateDTO{
    public readonly ?string $name;
    public readonly ?string $description;

    public function __construct(array $data, int $rolId){
        $this->validate($data, $rolId);

        $this->name = array_key_exists('name', $data) && $data['name'] !== null ? trim($data['name']) : null;
        $this->description = array_key_exists('description', $data) && $data['description'] !== null ? trim($data['description']) : null;
    }
    public static function fromRequest(Request $request, int $rolId): self
    {
        return new self($request->all(), $rolId);
    }

    public function toArray() : array {
        // devolver solo campos presentes (no nulos)
        return array_filter([
            'name' => $this->name,
            'description' => $this->description
        ], fn($v) => $v !== null);
    }
    private function validate(array $data, int $rolId) :void{
        $validator = Validator::make($data, [
            'name' => ['nullable','string','max:100', Rule::unique('roles','name')->ignore($rolId)],
            'description' => ['nullable','string','max:255'],
        ],[
            'name.max' => 'El nombre no puede exceder 100 caracteres',
            'name.unique' => 'Ya existe un rol con este nombre',
        ]);
        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }

}