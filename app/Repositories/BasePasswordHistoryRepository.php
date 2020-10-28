<?php

namespace App\Repositories;

/**
 * Class BasePasswordHistoryRepository
 *
 * @package App\Repositories
 * @property int $limit
 */
abstract class BasePasswordHistoryRepository extends BaseRepository
{
    /** @var int */
    protected $limit;

    /**
     * @param array $data
     *
     * @return \App\Portal\Models\PasswordHistory|\App\System\Models\PasswordHistory|false
     */
    abstract public function create(array $data);

    /**
     * @param int    $userId
     * @param string $password
     *
     * @return bool
     * @throws \Exception
     */
    public function addNew(int $userId, string $password): bool
    {
        $result = $this->create([
            'user_id'  => $userId,
            'password' => $password,
        ]);

        if (!$result) {
            return false;
        }

        $this->deleteUserOld($userId);

        return true;
    }

    /**
     * @param int $userId
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteUserOld(int $userId)
    {
        $ids = $this->model
            ->select('id')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->offset($this->limit)
            ->limit(999)
            ->get()
            ->pluck('id')
            ->toArray();

        return $this->model
            ->whereIn('id', $ids)
            ->delete();
    }
}
