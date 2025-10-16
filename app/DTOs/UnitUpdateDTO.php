<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class UnitUpdateDTO
{
    public readonly ?string $name;
    public readonly ?string $description;

    public function __construct(array $data, int $unitId)
    {
        $this->validate($data, $unitId);

        $this->name = array_key_exists('name', $data) && $data['name'] !== null ? trim($data['name']) : null;
        $this->description = trim($data['description']);
    }

    public static function fromRequest(Request $request, int $unitId): self
    {
        return new self($request->all(), $unitId);
    }

    public function toArray(): array
    {
        // Devolver solo campos presentes (no nulos)
        return array_filter([
            'name' => $this->name,
            'description' => $this->description
        ], fn($v) => $v !== null);
    }

    private function validate(array $data, int $unitId): void
    {
        $validator = Validator::make($data, [
            'name' => ['nullable', 'string', 'max:255', Rule::unique('units', 'name')->ignore($unitId)],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'name.unique' => 'Ya existe una unidad con ese nombre',
            'description.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}