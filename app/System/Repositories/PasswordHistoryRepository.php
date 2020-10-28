<?php

namespace App\System\Repositories;

use App\Repositories\BasePasswordHistoryRepository;
use App\System\Models\PasswordHistory;
use Carbon\Carbon;

/**
 * Class PasswordHistoryRepository
 *
 * @package App\System\Repositories
 * @method PasswordHistory find(int $id, array $relations = [])
 */
class PasswordHistoryRepository extends BasePasswordHistoryRepository
{
    /**
     * PasswordHistory constructor.
     *
     * @param PasswordHistory $passwordHistory
     */
    public function __construct(PasswordHistory $passwordHistory)
    {
        $this->model = $passwordHistory;
        $this->limit = config('auth.password_history');
    }

    /**
     * @param array $data
     *
     * @return PasswordHistory|false
     */
    public function create(array $data)
    {
        $passwordHistory = $this->model->newInstance();

        $passwordHistory->user_id = $data['user_id'];
        $passwordHistory->password = $data['password'];
        $passwordHistory->created_at = Carbon::now();

        return $passwordHistory->save() ? $passwordHistory : false;
    }
}
