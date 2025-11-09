<?php
// app/DTOs/SubcategoryUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class SubcategoryUpdateDTO
{
    public readonly ?int $category_id;
    public readonly ?string $name;
    public readonly ?string $description;

    public function __construct(array $data, ?int $subcategoryId = null)
    {
        $this->validate($data, $subcategoryId);

        $this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
        $this->name        = isset($data['name']) ? trim($data['name']) : null;
        $this->description = $data['description'] ?? null;
    }

    public static function fromRequest(Request $request, ?int $subcategoryId = null): self
    {
        return new self($request->all(), $subcategoryId);
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->category_id !== null) $out['category_id'] = $this->category_id;
        if ($this->name        !== null) $out['name']        = $this->name;
        if ($this->description !== null) $out['description'] = $this->description;
        return $out;
    }

    private function validate(array $data, ?int $subcategoryId): void
    {
        $rules = [];
        $messages = [
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'name.max'           => 'El nombre no puede exceder 100 caracteres.',
            'name.unique'        => 'Ya existe una subcategoría con ese nombre en esta categoría.',
            'description.max'    => 'La descripción no puede exceder 500 caracteres.',
        ];

        if (array_key_exists('category_id', $data)) {
            $rules['category_id'] = ['integer','exists:categories,id'];
        }

        if (array_key_exists('name', $data)) {
            // Si cambia 'name' o 'category_id', validamos unicidad compuesta ignorando el propio registro
            $categoryForRule = $data['category_id'] ?? null;
            $rules['name'] = [
                'string','max:100',
                Rule::unique('subcategories', 'name')
                    ->ignore($subcategoryId)
                    ->when($categoryForRule !== null,
                        fn($r) => $r->where('category_id', $categoryForRule)
                    ),
            ];
        }

        if (array_key_exists('description', $data)) {
            $rules['description'] = ['nullable','string','max:500'];
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
