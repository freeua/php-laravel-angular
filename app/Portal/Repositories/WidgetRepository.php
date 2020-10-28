<?php

namespace App\Portal\Repositories;

use App\Portal\Helpers\AuthHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\User;
use App\Portal\Models\Widget;
use Illuminate\Support\Collection;

/**
 * Class WidgetRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Widget find(int $id, array $relations = [])
 */
class WidgetRepository extends BaseRepository
{
    /**
     * WidgetRepository constructor.
     *
     * @param Widget $widget
     */
    public function __construct(Widget $widget)
    {
        $this->model = $widget;
    }

    /**
     * @param array $data
     *
     * @return Widget|false
     */
    public function create(array $data)
    {
        $widget = $this->model->newInstance();

        $widget->user_id = $data['user_id'];
        $widget->source = $data['source'];
        $widget->position = $data['position'];
        $widget->style = $data['style'];

        return $widget->save() ? $widget : false;
    }

    /**
     * @param int $userId
     *
     * @return Collection|static[]
     */
    public function userAll(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * @param bool $singleCompany
     *
     * @return Collection
     */
    public function currentUserAll(bool $singleCompany = false): Collection
    {
        /** @var User $user */
        $user = AuthHelper::user();

        return $singleCompany
            ? $user->widgets()->whereNotIn('source', Widget::getAllCompaniesSources())->get()
            : $user->widgets;
    }

    /**
     * @return int
     */
    public function checkUserLimit(): int
    {
        /** @var User $user */
        $user = AuthHelper::user();

        return $user->widgets()->count() < Widget::USER_LIMIT;
    }
}
