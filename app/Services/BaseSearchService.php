<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

/**
 * Class SearchService
 *
 * @package App\Services
 */
abstract class BaseSearchService
{
    const LIVE_ITEMS_COUNT = 6;

    /**
     * @return array
     */
    abstract public static function getCategories(): array;

    /**
     * @return array
     */
    abstract public function getCategoryRepositoryMap(): array;

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function search(array $params): Collection
    {
        $result = new Collection();

        /** @var BaseRepository[] $categoryRepositoryMap */
        $categoryRepositoryMap = $this->getCategoryRepositoryMap();

        $total = 0;

        foreach (static::getCategories() as $category) {
            $searchResult = $categoryRepositoryMap[$category]->list($params);
            $result->put($category, $searchResult);
            $total += $searchResult->total();
        }

        $result->put('total', $total);

        return $result;
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function live(array $params): Collection
    {
        $result = new Collection();

        /** @var BaseRepository[] $categoryRepositoryMap */
        $categoryRepositoryMap = $this->getCategoryRepositoryMap();

        $total = 0;

        foreach (static::getCategories() as $category) {
            $searchResult = $categoryRepositoryMap[$category]->list($params);
            $result->put($category, $searchResult->take(self::LIVE_ITEMS_COUNT));
            $total += $searchResult->total();
        }

        $result->put('total', $total);

        return $result;
    }
}
