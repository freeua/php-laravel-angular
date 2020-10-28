<?php

namespace App\Portal\Repositories\Base;

use App\Models\City;
use App\Portal\Models\User;
use App\Repositories\BaseRepository;
use App\Models\Portal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class UserRepository
 *
 * @package App\Portal\Repositories\Base
 *
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param array $data
     *
     * @param Portal $portal
     * @return User|false
     */
    public function create(array $data, Portal $portal)
    {
        $user = $this->model->newInstance();

        $user->portal()->associate($portal);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->company_id = $data['company_id'] ?? null;
        $user->supplier_id = $data['supplier_id'] ?? null;
        $user->password = isset($data['password_bcrypt'])
            ? $data['password_bcrypt']
            : Hash::make($data['password']);
        $user->password_updated_at = Carbon::now();
        $user->status_id = $data['status_id'] ?? User::STATUS_ACTIVE;
        if (isset($data['city_name']) && !empty($data['city_name'])) {
            $city = City::where('name', $data['city_name'])->first();
            $user->city_id = $city->id;
        }
        $user->phone = $data['phone'] ?? null;
        $user->postal_code = $data['postal_code'] ?? null;
        $user->street = $data['street'] ?? null;

        return $user->save() ? $user : false;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function existsEmail(string $email): bool
    {
        return $this->newQuery()->where('email', $email)->exists();
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findBy('email', $email, ['portal'])
            ->first();
    }

    /**
     * @inheritdoc
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        if (!isset($params['filter']) || !$params['filter']['value']) {
            return $query;
        }

        $column = $params['filter']['column'];
        $value = strtolower($params['filter']['value']);

        if (!isset($this->filterWhereColumns[$column])) {
            throw new BadRequestHttpException($this->getFilterErrorMessage());
        }

        $query->having($this->filterWhereColumns[$column], 'like', '%' . $value . '%');

        return $query;
    }

    /**
     * @param int    $id
     * @param string $role
     *
     * @return bool
     */
    public function checkRole(int $id, string $role): bool
    {
        $user = $this->find($id);

        return $user ? $user->hasRole($role) : false;
    }

    /**
     * @param string   $role
     * @param int|null $supplierId
     * @param int|null $companyId
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByRole(string $role, int $portalId, ?int $supplierId = null, ?int $companyId = null)
    {
        return $this->newQuery()
            ->whereHas('roles', function (Builder $query) use ($role) {
                $query->where('name', $role);
            })
            ->where('supplier_id', $supplierId)
            ->where('company_id', $companyId)
            ->where('portal_id', $portalId)
            ->where('status_id', User::STATUS_ACTIVE)
            ->get();
    }
}
