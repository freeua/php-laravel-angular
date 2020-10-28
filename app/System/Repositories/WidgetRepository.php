<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\System\Models\User;
use App\System\Models\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class WidgetRepository
 *
 * @package App\System\Repositories
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
     * @return Collection
     */
    public function currentUserAll(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->widgets;
    }

    /**
     * @return int
     */
    public function checkUserLimit(): int
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->widgets()->count() < Widget::USER_LIMIT;
    }
}
