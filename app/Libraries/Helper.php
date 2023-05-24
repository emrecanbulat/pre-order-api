<?php

namespace App\Libraries;

class Helper
{

    /**
     * @return array
     */
    public static function getSortVariables(): array
    {
        $order = 'id';
        $direction = 'desc';

        if (\request()->has('order') && !empty(\request()->order)) {
            $order = \request()->order;
        }
        if (\request()->has('direction') && in_array(\request()->direction, ['asc', 'desc'])) {
            $direction = \request()->direction;
        }

        return [
            'order' => $order,
            'direction' => $direction
        ];
    }

    /**
     * @param $query
     * @param $limit
     * @return mixed
     */
    public static function getPaginationResults($query, $limit = null): mixed
    {
        $sortVariables = self::getSortVariables();
        return $query->orderBy($sortVariables['order'], $sortVariables['direction'])->paginate($limit);
    }

}
