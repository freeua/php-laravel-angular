<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class BaseWidgetService
 *
 * @package App\Services
 */
abstract class BaseWidgetService
{
    /**
     * @param Collection $data
     * @param array      $params
     * @param bool       $weekData
     *
     * @return Collection
     */
    protected function formatDateWidgetData(Collection $data, array $params, bool $weekData = false): Collection
    {
        $dateFrom = Carbon::createFromFormat('Y-m-d', $params['date_from']);
        $dateTo = Carbon::createFromFormat('Y-m-d', $params['date_to']);

        $result = collect([]);

        if ($weekData) {
            $data = $data->keyBy('week');

            do {
                $startOfWeek = $dateFrom->copy()->startOfWeek();
                $startOfWeekStr = $startOfWeek->toDateString();

                $firstDayOfWeek = $startOfWeek < $dateFrom ? $dateFrom->day : $startOfWeek->day;

                $endOfWeek = $startOfWeek->copy()->endOfWeek();

                $lastDayOfWeek = $endOfWeek > $dateTo ? $dateTo->day : $endOfWeek->day;

                $week = $firstDayOfWeek . '-' . $lastDayOfWeek;

                $item = $data->get($startOfWeekStr)
                    ? ['week' => $week, 'total' => $data->get($startOfWeekStr)['total']]
                    : ['week' => $week, 'total' => 0];

                $result->push($item);
                $dateFrom->addWeek()->startOfWeek();
            } while ($dateFrom <= $dateTo);
        } else {
            $data = $data->keyBy(function ($item) {
                return $item->date instanceof Carbon ? $item->date->toDateString() : $item->date;
            });

            do {
                $dateFromStr = $dateFrom->toDateString();

                $item = $data->get($dateFromStr)
                    ? ['date' => $dateFromStr, 'total' => $data->get($dateFromStr)['total']]
                    : ['date' => $dateFromStr, 'total' => 0];

                $result->push($item);
                $dateFrom->addDay();
            } while ($dateFrom <= $dateTo);
        }

        return $result->values();
    }
}
