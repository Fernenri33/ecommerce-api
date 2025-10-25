<?php
// app/DTOs/ProductDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Enums\Status;

class ProductDTO
{
    public readonly string $name;
    public readonly ?string $sku;
    public readonly ?string $description;
    public readonly string $imagen;
    public readonly int $available_quantity;
    public readonly int $warehouse_quantity;
    public readonly int $unit_id;
    public readonly Status $status;
    public readonly ?float $unit_cost;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->name = trim($data['name']);
        $this->sku = !empty($data['sku']) ? trim($data['sku']) : null;
        $this->description = !empty($data['description']) ? trim($data['description']) : null;
        $this->imagen = $data['imagen'];
        $this->available_quantity = (int) $data['available_quantity'];
        $this->warehouse_quantity = (int) $data['warehouse_quantity'];
        $this->unit_id = (int) $data['unit_id'];
        $this->status = Status::from($data['status'] ?? 'active');
        $this->unit_cost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
            ? (float) $data['unit_cost']
            : null;
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
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'imagen' => $this->imagen,
            'available_quantity' => $this->available_quantity,
            'warehouse_quantity' => $this->warehouse_quantity,
            'unit_id' => $this->unit_id,
            'status' => $this->status->value,
            'unit_cost' => $this->unit_cost,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:32|unique:products,sku',
            'description' => 'nullable|string',
            'imagen' => 'required|string|max:255',
            'available_quantity' => 'required|integer|min:0',
            'warehouse_quantity' => 'required|integer|min:0',
            'unit_id' => 'required|integer|exists:units,id',
            'status' => 'nullable|in:active,inactive,hidden',
            'unit_cost' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'El nombre del producto es obligatorio',
            'name.max' => 'El nombre no puede exceder 100 caracteres',
            'name.unique' => 'Ya existe un producto con este nombre',
            'sku.unique' => 'El SKU ya está en uso',
            'sku.max' => 'El SKU no puede exceder 32 caracteres',
            'imagen.required' => 'La imagen es obligatoria',
            'available_quantity.required' => 'La cantidad disponible es obligatoria',
            'available_quantity.min' => 'La cantidad disponible no puede ser negativa',
            'warehouse_quantity.required' => 'La cantidad en almacén es obligatoria',
            'warehouse_quantity.min' => 'La cantidad en almacén no puede ser negativa',
            'unit_id.required' => 'La unidad de medida es obligatoria',
            'unit_id.exists' => 'La unidad de medida seleccionada no existe',
            'status.in' => 'El estado debe ser: active, inactive o hidden',
            'unit_cost.numeric' => 'El costo unitario debe ser un número',
            'unit_cost.min' => 'El costo unitario no puede ser negativo',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}