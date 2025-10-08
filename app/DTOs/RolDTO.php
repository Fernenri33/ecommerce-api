<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class RolDTO{
    public readonly string $name;
    public readonly ?string $description;

    public function __construct(array $data){
        $this->validate($data);

        $this->name = trim($data['name']);
        $this->description = trim($data['description']);
    }
    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
    public function toArray() : array {
        return[
            'name' => $this->name,
            'descrition' => $this->description
        ];
    }
    private function validate(array $data) :void{
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'description' => 'string'
        ],[
            'name.required' => 'El nombre del rol es obligatorio',
            'name.max' => 'El nombre no puede exceder 100 caracteres',
            'name.unique' => 'Ya existe un rol con este nombre',
        ]);
        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }

}