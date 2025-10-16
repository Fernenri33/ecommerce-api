<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CategoryDTO
{
    public readonly string $name;
    public readonly ?string $description = null;
    public function __construct(array $data) {
        $this->validate($data);

        $this->name = array_key_exists('name', $data) && $data['name'] !== null ? trim($data['name']) : null;
        $this->description = trim($data['description']);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
    private function validate(array $data) :void{
        $validator = Validator::make($data,[
            'name' => 'required|string|max:255|unique:units,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Ya existe una unidad con ese nombre',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'description.max' => 'La descripción no puede exceder 500 caracteres',
        ]);
        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}