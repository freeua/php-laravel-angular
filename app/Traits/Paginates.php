<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-16
 * Time: 17:16
 */

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait Paginates
{
    public $perPage = 10;
    /** @var array */
    protected $filterWhereColumns = [];
    /** @var array */
    protected $filterHavingColumns = [];
    /** @var array */
    protected $searchWhereColumns = [];
    /** @var array */
    protected $searchHavingColumns = [];

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
            $query->having(\DB::raw($columnConditions), 'like', '%' . $value . '%');
        }

        return $query;
    }

    protected function applyComplexFilterWhereColumnConditions(Builder $query, array $columnConditions, string $value): void
    {
        list($column, $operator) = $columnConditions;

        $valueType = $columnConditions[2] ?? null;
        $delimiter = $columnConditions[3] ?? null;

        $value = $this->prepareFilterValue($value, $valueType, $delimiter);

        switch ($operator) {
            case 'between':
                $query->whereBetween(\DB::raw($column), $value);
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

    protected function applySearch(Builder $query, string $search): Builder
    {
        if (!$this->searchWhereColumns && !$this->searchHavingColumns) {
            return $query;
        }

        $query->where(function ($query) use ($search) {
            /* @var Builder $query */
            foreach ($this->searchWhereColumns as $column) {
                $query->orWhere(\DB::raw($column), 'like', '%' . $search . '%');
            }
        });

        foreach ($this->searchHavingColumns as $column) {
            $query->orHaving($column, 'like', '%' . $search . '%');
        }

        return $query;
    }

    protected function getFilterErrorMessage(): string
    {
        return __('exception.invalid_filter', ['params' => implode(', ', array_keys(array_merge($this->filterWhereColumns, $this->filterHavingColumns)))]);
    }
}
