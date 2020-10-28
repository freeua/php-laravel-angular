<?php

namespace App\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Gates\Portal;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BaseRepository
 *
 * @package App\Repositories
 */
abstract class BaseRepository
{
    /** @var int */
    protected $perPage = 20;
    /** @var Model */
    protected $model;
    /** @var array */
    protected $filterWhereColumns = [];
    /** @var array */
    protected $filterHavingColumns = [];
    /** @var array */
    protected $searchWhereColumns = [];
    /** @var array */
    protected $searchHavingColumns = [];

    /**
     * @param string $orderBy
     *
     * @param string $direction
     *
     * @return Collection|static[]
     */
    public function all(string $orderBy = null, string $direction = 'asc'): Collection
    {
        return $orderBy
            ? $this->model->orderBy($orderBy, $direction)->get()
            : $this->model->get();
    }

    /**
     * Return paginated results of the given model from the database
     *
     * @param array $params
     * @param array $relationships
     *
     * @return LengthAwarePaginator
     * @throws BadRequestHttpException
     */
    public function list(array $params, array $relationships = [])
    {
        $query = $this->model->newQuery();
        if (!empty($params['status_id'])) {
            $query->where(['status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @param Builder $query
     * @param array   $params
     * @param array   $relationships
     *
     * @return LengthAwarePaginator
     * @throws BadRequestHttpException
     */
    protected function processList(Builder $query, array $params = [], array $relationships = [])
    {
        if ($relationships) {
            $query->with($relationships);
        }

        $query = $this->applyFilters($query, $params);

        if (!empty($params['search'])) {
            $query = $this->applySearch($query, $params['search']);
        }

        $orderBy = $params['order_by'] ?? 'id';
        $order = $params['order'] ?? 'desc';
        $perPage = $params['per_page'] ?? $this->perPage;


        return $query
            ->orderBy($orderBy, $order)
            ->paginate($perPage);
    }

    /**
     * Return total of search results of the given model from the database
     *
     * @param array $params
     *
     * @return int
     */
    public function searchTotal(array $params): int
    {
        $query = $this->model->newQuery();

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * Return a model by ID from the database. If relationships are provided, eager load those relationships.
     *
     * @param int   $id
     * @param array $relationships
     *
     * @return Model|null
     */
    public function find(int $id, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->findOrFail($id);
    }

    /**
     * @param string $column
     * @param string $value
     * @param array  $relationships
     *
     * @return Builder[]|Collection|Model[]|\Illuminate\Support\Collection
     */
    public function findBy(string $column, string $value, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->where($column, $value)
            ->get();
    }

    /**
     * @param array $where
     * @param array $relationships
     *
     * @return Builder[]|Collection|Model[]|\Illuminate\Support\Collection
     */
    public function findWhere(array $where, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->where($where)
            ->get();
    }

    /**
     * Create a new Eloquent Query Builder instance
     *
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Update model
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data)
    {
        return $this->find($id)->update($data);
    }

    /**
     * @param array $ids
     * @param array $data
     *
     * @return bool|null
     */
    public function butchUpdate(array $ids, array $data)
    {
        return $this->model->whereIn('id', $ids)->update($data);
    }

    /**
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function butchDelete(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * @param int $id
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param Model $model
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteModel(Model $model)
    {
        return $model->delete();
    }

    /**
     * Delete all models
     *
     * @param array $where
     *
     * @return bool|null
     *
     */
    public function deleteAll(array $where = []): ?bool
    {
        $query = $this->newQuery();

        if ($where) {
            $query->where($where);
        }

        return $query
            ->delete();
    }

    /**
     * @param string $name
     * @param int    $lettersCount
     * @param int    $digitsCount
     * @param string $delimiter
     * @param string $prefix
     * @param string $field
     * @param int    $id
     *
     * @return string
     */
    public function generateCode(
        string $name,
        int $lettersCount = 3,
        int $digitsCount = 3,
        string $delimiter = '',
        string $prefix = '',
        string $field = 'code',
        int $id = null
    ): string {
        $code = $prefix . strtoupper(substr(preg_replace('/\W/', '', $name), 0, $lettersCount)) . $delimiter;

        $query = $this->newQuery()
            ->where($field, 'like', '%' . $code . '%');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->latest('id')
            ->first();

        if ($exists) {
            if ($delimiter) {
                $parts = explode($delimiter, $exists->{$field});
                $number = end($parts);
            } else {
                $number = intval(substr($exists->{$field}, -$digitsCount));
            }
        } else {
            $number = 0;
        }

        $code .= str_pad(++$number, $digitsCount, '0', STR_PAD_LEFT);

        return $code;
    }

    /**
     * @param Builder $query
     * @param array   $params
     *
     * @return Builder
     * @throws BadRequestHttpException
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        if (!isset($params['filter']) || !$params['filter']['value']) {
            return $query;
        }

        $column = $params['filter']['column'];
        $value = $params['filter']['value'];

        if (!isset($this->filterWhereColumns[$column]) && !isset($this->filterHavingColumns[$column])) {
            throw new BadRequestHttpException($this->getFilterErrorMessage());
        }

        $columnConditions = $this->filterWhereColumns[$column] ?? null;

        if ($columnConditions) {
            $query->where(function ($query) use ($columnConditions, $value) {
                /* @var Builder $query */

                if (is_array($columnConditions)) {
                    $this->applyComplexFilterWhereColumnConditions($query, $columnConditions, $value);
                } else {
                    $query->where($columnConditions, 'like', '%' . $value . '%');
                }
            });
        }

        $columnConditions = $this->filterHavingColumns[$column] ?? null;

        if ($columnConditions) {
            $query->having(DB::raw($columnConditions), 'like', '%' . $value . '%');
        }

        return $query;
    }

    /**
     * Apply complex where filter to query
     *
     * @param Builder $query
     * @param array   $columnConditions
     * @param string  $value
     */
    protected function applyComplexFilterWhereColumnConditions(Builder $query, array $columnConditions, string $value): void
    {
        list($column, $operator) = $columnConditions;

        $valueType = $columnConditions[2] ?? null;
        $delimiter = $columnConditions[3] ?? null;

        $value = $this->prepareFilterValue($value, $valueType, $delimiter);

        switch ($operator) {
            case 'between':
                $query->whereBetween(DB::raw($column), $value);
                break;
            default:
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $query->orWhere($column, 'like', '%' . $item . '%');
                    }
                } else {
                    $query->where($column, 'like', '%' . $value . '%');
                }
                break;
        }
    }

    /**
     * @param string $value
     * @param string $type
     * @param string $delimiter
     *
     * @return mixed
     */
    protected function prepareFilterValue(string $value, string $type = null, string $delimiter = null)
    {
        if ($delimiter) {
            $value = explode($delimiter, $value);
        }

        switch ($type) {
            case 'timestamps':
                if (is_array($value)) {
                    $value[0] = Carbon::createFromTimestampUTC($value[0])->startOfDay();
                    $value[1] = Carbon::createFromTimestampUTC($value[1])->endOfDay();
                } else {
                    $value = Carbon::createFromTimestampUTC($value);
                }
                break;
        }

        return $value;
    }

    /**
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    protected function applySearch(Builder $query, string $search): Builder
    {
        if (!$this->searchWhereColumns && !$this->searchHavingColumns) {
            return $query;
        }
        $query->where(function ($query) use ($search) {
            /* @var Builder $query */
            foreach ($this->searchWhereColumns as $column) {
                $query->orWhere(DB::raw($column), 'like', '%' . $search . '%');
            }
        });
        foreach ($this->searchHavingColumns as $column) {
            $query->orHaving($column, 'like', '%' . $search . '%');
        }

        return $query;
    }

    /**
     * @return string
     */
    protected function getFilterErrorMessage(): string
    {
        return __('exception.invalid_filter', ['params' => implode(', ', array_keys(array_merge($this->filterWhereColumns, $this->filterHavingColumns)))]);
    }
}
