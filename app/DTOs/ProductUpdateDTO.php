<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use App\Enums\Status;

class ProductUpdateDTO
{
    public readonly ?string $name;
    public readonly ?string $sku;
    public readonly ?string $description;
    public readonly ?string $imagen;
    public readonly ?int $available_quantity;
    public readonly ?int $warehouse_quantity;
    public readonly ?int $unit_id;
    public readonly ?Status $status;
    public readonly ?float $unit_cost;

    public function __construct(array $data, int $productId)
    {
        $this->validate($data, $productId);
        
        $this->name = isset($data['name']) ? trim($data['name']) : null;
        $this->sku = isset($data['sku']) ? (!empty($data['sku']) ? trim($data['sku']) : null) : null;
        $this->description = isset($data['description']) ? (!empty($data['description']) ? trim($data['description']) : null) : null;
        $this->imagen = $data['imagen'] ?? null;
        $this->available_quantity = isset($data['available_quantity']) ? (int) $data['available_quantity'] : null;
        $this->warehouse_quantity = isset($data['warehouse_quantity']) ? (int) $data['warehouse_quantity'] : null;
        $this->unit_id = isset($data['unit_id']) ? (int) $data['unit_id'] : null;
        $this->status = isset($data['status']) ? Status::from($data['status']) : null;
        $this->unit_cost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
            ? (float) $data['unit_cost']
            : null;
    }

    public static function fromRequest(Request $request, int $productId): self
    {
        return new self($request->all(), $productId);
    }

    public function toArray(): array
    {
        $data = [];
        
        if ($this->name !== null) $data['name'] = $this->name;
        if ($this->sku !== null) $data['sku'] = $this->sku;
        if ($this->description !== null) $data['description'] = $this->description;
        if ($this->imagen !== null) $data['imagen'] = $this->imagen;
        if ($this->available_quantity !== null) $data['available_quantity'] = $this->available_quantity;
        if ($this->warehouse_quantity !== null) $data['warehouse_quantity'] = $this->warehouse_quantity;
        if ($this->unit_id !== null) $data['unit_id'] = $this->unit_id;
        if ($this->status !== null) $data['status'] = $this->status->value;
        if ($this->unit_cost !== null) $data['unit_cost'] = $this->unit_cost; // added
        
        return $data;
    }

    private function validate(array $data, int $productId): void
    {
        $rules = [];
        $messages = [
            'name.max' => 'El nombre no puede exceder 100 caracteres',
            'name.unique' => 'Ya existe un producto con este nombre',
            'sku.unique' => 'El SKU ya está en uso',
            'sku.max' => 'El SKU no puede exceder 32 caracteres',
            'imagen.max' => 'La ruta de imagen no puede exceder 255 caracteres',
            'available_quantity.min' => 'La cantidad disponible no puede ser negativa',
            'warehouse_quantity.min' => 'La cantidad en almacén no puede ser negativa',
            'unit_id.exists' => 'La unidad de medida seleccionada no existe',
            'status.in' => 'El estado debe ser: active, inactive o hidden',
            'unit_cost.numeric' => 'El costo unitario debe ser un número',
            'unit_cost.min' => 'El costo unitario no puede ser negativo',
        ];

        // Solo validar campos que están presentes
        if (array_key_exists('name', $data)) {
            $rules['name'] = [
                'string',
                'max:100',
                Rule::unique('products', 'name')->ignore($productId)
            ];
        }

        if (array_key_exists('sku', $data)) {
            $rules['sku'] = [
                'nullable',
                'string',
                'max:32',
                Rule::unique('products', 'sku')->ignore($productId)
            ];
        }

        if (array_key_exists('description', $data)) {
            $rules['description'] = 'nullable|string';
        }

        if (array_key_exists('imagen', $data)) {
            $rules['imagen'] = 'string|max:255';
        }

        if (array_key_exists('available_quantity', $data)) {
            $rules['available_quantity'] = 'integer|min:0';
        }

        if (array_key_exists('warehouse_quantity', $data)) {
            $rules['warehouse_quantity'] = 'integer|min:0';
        }

        if (array_key_exists('unit_id', $data)) {
            $rules['unit_id'] = 'integer|exists:units,id';
        }

        if (array_key_exists('status', $data)) {
            $rules['status'] = 'in:active,inactive,hidden';
        }

        if (array_key_exists('unit_cost', $data)) {
            $rules['unit_cost'] = 'nullable|numeric|min:0';
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
