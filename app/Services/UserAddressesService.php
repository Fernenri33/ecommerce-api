<?php
namespace App\Services;

use App\DTOs\UserAddressDTO;
use App\DTOs\UserAddressUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Model;

class UserAddressesService extends BaseService
{
    protected $with = ['user:name,last_name'];
    public function __construct()
    {
        parent::__construct(new UserAddress, 'Direcciones');
    }
    public function getAllUserAdresses()
    {
        return $this->getAll();
    }
    public function getUserAdresses($id)
    {
        return $this->find($id);
    }
    public function findUserAddressesByPlace($place)
{
    try {
        $searchTerm = trim($place);
        
        if (empty($searchTerm)) {
            return ResponseHelper::error("El término de búsqueda no puede estar vacío");
        }

        $addresses = UserAddress::where(function($query) use ($searchTerm) {
            $query->where('line2', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('city', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('state', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('postal_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('country', 'LIKE', "%{$searchTerm}%");
                  //->orWhere('place_id', 'LIKE', "%{$searchTerm}%");
        })
        ->whereNull('deleted_at')
        ->get();

        if ($addresses->isEmpty()) {
            return ResponseHelper::success("No se encontraron direcciones");
        }

        return ResponseHelper::notFound("usuario con esa dirección");
        
    } catch (\Exception $e) {
        return ResponseHelper::error(
            "Error al obtener las direcciones: " . $e->getMessage()
        );
    }
}
    public function createUserAddress(UserAddressDTO $userAddressDTO){
        return $this->create($userAddressDTO->toArray());
    }
    public function updateUserAddress($id, UserAddressUpdateDTO $userAddressUpdateDTO){
        return $this->update($id, $userAddressUpdateDTO->toArray());
    }
    public function deleteUserAddress($id){
        return $this->delete($id);
    }
}