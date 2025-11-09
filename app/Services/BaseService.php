<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected $model;
    protected $resourceName;
    protected $with = [];

    public function __construct(Model $model, string $resourceName)
    {
        $this->model = $model;
        $this->resourceName = $resourceName;
    }

    public function find($id)
    {
        try {
            $item = $this->model::with($this->with)->find($id);

            if ($item) {
                return ResponseHelper::success(
                    "{$this->resourceName} se ha encontrado",
                    $item
                );
            }

            return ResponseHelper::notFound($this->resourceName);
        } catch (\Exception $e) {
            return ResponseHelper::exception("buscar {$this->resourceName}", $e);
        }
    }

    public function getAll($perPage = 20)
    {
        try {
            $query = $this->model::query();
            $query->with($this->with);

            $items = $query->paginate($perPage);

            return ResponseHelper::success(
                "{$this->resourceName}s obtenidos exitosamente",
                $items
            );
        } catch (\Exception $e) {
            return ResponseHelper::exception("obtener {$this->resourceName}s", $e);
        }
    }

    public function create(array $data)
    {
        try {
            $item = $this->model::create($data);
            return ResponseHelper::created($this->resourceName, $item);
        } catch (\Exception $e) {
            return ResponseHelper::exception("crear {$this->resourceName}", $e);
        }
    }

    public function update($id, array $data)
    {
        try {
            $item = $this->model::find($id);

            if (!$item) {
                return ResponseHelper::notFound($this->resourceName);
            }

            $item->update($data);

            $item->load($this->with);

            return ResponseHelper::updated($this->resourceName, $item);
        } catch (\Exception $e) {
            return ResponseHelper::exception("actualizar {$this->resourceName}", $e);
        }
    }

    public function delete($id)
    {
        try {
            $item = $this->model::find($id);

            if (!$item) {
                return ResponseHelper::notFound($this->resourceName);
            }

            $item->delete();
            return ResponseHelper::deleted($this->resourceName);
        } catch (\Exception $e) {
            return ResponseHelper::exception("eliminar {$this->resourceName}", $e);
        }
    }

    public function findByName(string $name, $perPage = 20)
    {
        try {
            $query = $this->model::query()->with($this->with);
            $items = $query->where('name', 'LIKE', "%{$name}%")->paginate($perPage);

            if ($items->isEmpty()) {
                return ResponseHelper::notFound($this->resourceName);
            }

            return ResponseHelper::success(
                "{$this->resourceName}s encontrados exitosamente",
                $items
            );
        } catch (\Exception $e) {
            return ResponseHelper::exception("Error al buscar {$this->resourceName}s", $e);
        }
    }
}
