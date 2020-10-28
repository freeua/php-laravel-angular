<?php

namespace App\System\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Models\Role;
use App\Repositories\BaseRepository;
use App\System\Models\User;
use App\Portal\Models\User as PortalUser;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class UserRepository
 *
 * @package App\System\Repositories
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends BaseRepository
{
    public const ROLE_ADMIN = 'Administrator';

    public const ROLE_PORTAL_OWNER = 'Portalbesitzer';

    public const COMPANY_SYSTEM = 'System';

    /** @var PortalUser */
    private $portalUser;
    /** @var array */
    protected $filterWhereColumns = [
        'company' => 'company',
        'role'    => 'role',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'code',
        'email',
        'company',
        'role',
        'name',
    ];

    /**
     * UserRepository constructor.
     *
     * @param User       $user
     * @param PortalUser $portalUser
     */
    public function __construct(User $user, PortalUser $portalUser)
    {
        $this->model = $user;
        $this->portalUser = $portalUser;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     * @throws \Exception
     */
    public function list(array $params, array $relations = []): LengthAwarePaginator
    {
        $usersQuery = User::query()
            ->whereNull('deleted_at')
            ->with(['status']);

        $portalUsersQuery = PortalUser::query()
            ->whereNull('portal_users.deleted_at')
            ->with(['status', 'roles', 'company', 'portal', 'supplier']);
        if (!empty($params['status_id'])) {
            $usersQuery->where(['status_id' => $params['status_id']]);
            $portalUsersQuery->where(['portal_users.status_id' => $params['status_id']]);
        }
        $usersQuery = $this->applyFilters($usersQuery, $params);
        $portalUsersQuery = $this->applyFilters($portalUsersQuery, $params);
        if (!empty($params['search'])) {
            $usersQuery = $this->applySearch($usersQuery, $params['search']);
            $portalUsersQuery = $this->applySearch($portalUsersQuery, $params['search']);
        }

        $items = $portalUsersQuery
            ->orderBy($params['order_by'] ?? 'id', $params['order'] ?? 'desc')
            ->get();
        $items = $items->concat($usersQuery
            ->orderBy($params['order_by'] ?? 'id', $params['order'] ?? 'desc')
            ->get());


        $perPage = $params['per_page'] ?? $this->perPage;
        $page = $params['page'] ?? 1;
        $result = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($items), $perPage);
    }

    public function findAllEmployees(array $params, array $relations = []): LengthAwarePaginator
    {

        $role = Role::ROLE_EMPLOYEE;

        $portalUsersQuery = PortalUser::query()
            ->whereNull('portal_users.deleted_at')
            ->whereHas('roles', function (Builder $query) use ($role) {
                 $query
                     ->where('name', '=', $role);
            })
            ->with(['status', 'roles', 'company', 'portal', 'supplier']);
        if (!empty($params['status_id'])) {
            $portalUsersQuery->where(['portal_users.status_id' => $params['status_id']]);
        }
        $portalUsersQuery = $this->applyFilters($portalUsersQuery, $params);
        if (!empty($params['search'])) {
            $portalUsersQuery = $this->applySearch($portalUsersQuery, $params['search']);
        }

        $items = $portalUsersQuery
            ->orderBy($params['order_by'] ?? 'id', $params['order'] ?? 'desc')
            ->get();


        $perPage = $params['per_page'] ?? $this->perPage;
        $page = $params['page'] ?? 1;
        $result = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($items), $perPage);
    }


    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $usersQuery = $this
            ->newQuery()
            ->select([
                'id',
                'code',
                \DB::raw('CONCAT(first_name, " ", last_name) as name'),
                'email',
                \DB::raw('"' . self::COMPANY_SYSTEM . '" as company'),
                \DB::raw('"' . self::ROLE_ADMIN . '" as role'),
                \DB::raw('"system" as type'),
                'status',
            ])
            ->whereNull('deleted_at');

        $portalUsersQuery = $this->portalUser
            ->newQuery()
            ->select([
                'portal_users.id as id',
                'code',
                \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
                'portal_users.email as email',
                'portals.name as company',
                \DB::raw('"' . self::ROLE_PORTAL_OWNER . '" as role'),
                \DB::raw('"portal" as type'),
                'portal_users.status',
            ])
            ->join('portals', 'portal_users.portal_id', 'portals.id')
            ->whereNull('portal_users.deleted_at');

        if (!empty($params['search'])) {
            $usersQuery = $this->applySearch($usersQuery, $params['search']);
        }

        if (!empty($params['search'])) {
            $portalUsersQuery = $this->applySearch($portalUsersQuery, $params['search']);
        }

        return $portalUsersQuery
            ->union($usersQuery)
            ->get()
            ->count();
    }

    /**
     * Returns list of users for export
     *
     * @param array $params
     *
     * @return Collection
     * @throws \Exception
     */
    public function exportList(array $params): Collection
    {
        return $this
            ->list($params)->map(function ($item) {
                /** @var $item User|PortalUser */
                return $item->only([
                    'code',
                    'name',
                    'email',
                    'company',
                    'role',
                ]);
            });
    }

    /**
     * @param array $data
     *
     * @return User|false
     */
    public function create(array $data)
    {
        $user = $this->model->newInstance();

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->password_updated_at = Carbon::now();
        $user->organization_name = $data['organization_name'] ?? null;
        $user->address = $data['address'] ?? null;
        $user->status_id = $data['status_id'] ?? User::STATUS_ACTIVE;

        if (!$user->save()) {
            return false;
        }

        $user->code = User::CODE_PREFIX . '-' . $user->id;

        return $user->save() ? $user : false;
    }

    /**
     * @param Builder $query
     * @param array   $params
     *
     * @return Builder
     * @throws \Exception
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

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query
            ->where(function (Builder $query) use ($search) {
                if ($query->getModel() === $this->portalUser) {
                    $query->orWhereHas('company', function (Builder $query) use ($search) {
                        $query
                            ->orWhere('name', 'like', "%$search%");
                    })->orWhereHas('roles', function (Builder $query) use ($search) {
                            $query
                                ->orWhere('name', 'like', "%$search%");
                    });
                }
                return $query
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            });
    }
}
