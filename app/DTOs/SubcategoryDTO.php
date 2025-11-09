<?php
// app/DTOs/SubcategoryDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class SubcategoryDTO
{
    public readonly int $category_id;
    public readonly string $name;
    public readonly ?string $description;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->category_id = (int) $data['category_id'];
        $this->name        = trim($data['name']);
        $this->description = $data['description'] ?? null;
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'category_id' => ['required','integer','exists:categories,id'],
            'name'        => [
                'required','string','max:100',
                // único por categoría
                Rule::unique('subcategories', 'name')
                    ->where(fn($q) => $q->where('category_id', $data['category_id'] ?? null)),
            ],
            'description' => ['nullable','string','max:500'],
        ], [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no existe.',
            'name.required'        => 'El nombre es obligatorio.',
            'name.max'             => 'El nombre no puede exceder 100 caracteres.',
            'name.unique'          => 'Ya existe una subcategoría con ese nombre en esta categoría.',
            'description.max'      => 'La descripción no puede exceder 500 caracteres.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
