<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class BasicSearchFilter
{
    public static function apply(Builder $query, array $params, array $searchable = [])
    {
        if ($search = $params['search'] ?? null) {
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'LIKE', "%$search%");
                }
            });
        }
        
        if (isset($params['filter']) && is_string($params['filter'])) {
            $ids = array_filter(array_map('trim', explode(',', $params['filter'])), 'is_numeric');
            if (!empty($ids)) {
                $query->whereIn('department_id', $ids);
            }
        }

        return $query;
    }
}
