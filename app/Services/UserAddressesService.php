<?php

namespace App\Services;

use App\DTOs\UserAddressDTO;
use App\DTOs\UserAddressUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressesService extends BaseService
{
    protected $with = ['user'];

    public function __construct()
    {
        parent::__construct(new UserAddress, 'Direcciones');
    }

    /**
     * Lista direcciones del usuario autenticado (con filtro opcional por place)
     */
    public function getAddressesForUser(User $user, ?string $place = null)
    {
        try {
            $query = UserAddress::query()
                ->where('user_id', $user->id)
                ->whereNull('deleted_at');

            if (!empty($place)) {
                $search = trim($place);
                $query->where(function ($q) use ($search) {
                    $q->where('line2', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%")
                        ->orWhere('state', 'LIKE', "%{$search}%")
                        ->orWhere('postal_code', 'LIKE', "%{$search}%")
                        ->orWhere('country', 'LIKE', "%{$search}%");
                });
            }

            $addresses = $query->get();

            return ResponseHelper::success(
                $addresses->isEmpty()
                    ? 'No se encontraron direcciones para este usuario'
                    : 'Direcciones obtenidas correctamente',
                $addresses
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Error al obtener las direcciones: ' . $e->getMessage()
            );
        }
    }

    /**
     * Obtiene UNA dirección del usuario (asegurando que sea suya)
     */
    public function getAddressForUserById(User $user, int $id)
    {
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->find($id);

            if (!$address) {
                return ResponseHelper::notFound('Dirección no encontrada');
            }

            return ResponseHelper::success('Dirección obtenida correctamente', $address);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Error al obtener la dirección: ' . $e->getMessage()
            );
        }
    }

    /**
     * Crea dirección para un usuario (forzando user_id)
     */
    public function createUserAddress(User $user, UserAddressDTO $dto)
    {
        $data = $dto->toArray();
        $data['user_id'] = $user->id; // ignorar cualquier user_id del request

        return $this->create($data);
    }

    /**
     * Actualiza dirección SOLO si pertenece al usuario
     */
    public function updateUserAddress(User $user, int $id, UserAddressUpdateDTO $dto)
    {
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->find($id);

            if (!$address) {
                return ResponseHelper::notFound('Dirección no encontrada');
            }

            $data = $dto->toArray();

            $address->update($data);

            return ResponseHelper::success('Dirección actualizada correctamente', $address);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Error al actualizar la dirección: ' . $e->getMessage()
            );
        }
    }

    /**
     * Elimina dirección SOLO si pertenece al usuario
     */
    public function deleteUserAddress(User $user, int $id)
    {
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->find($id);

            if (!$address) {
                return ResponseHelper::notFound('Dirección no encontrada');
            }

            $address->delete();

            return ResponseHelper::success('Dirección eliminada correctamente');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Error al eliminar la dirección: ' . $e->getMessage()
            );
        }
    }
}
