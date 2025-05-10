<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductFilter
{
    protected array $params = [];

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @param Builder<Product> $query
     * @return Builder<Product>
     */
    public function apply(Builder $query): Builder
    {
        $params = new Collection($this->params['filters'] ?? []);

        $this->applySort($query);

        if (!empty($this->params['search'])) {
            $search = $this->params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tags', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    /**
     * @param Builder<Product> $query
     * @return Builder<Product>
     */
    protected function applySort(Builder $query): Builder
    {
        if (!empty($this->params['sortBy'])) {
            foreach ($this->params['sortBy'] as $sort) {
                $this->applySingleSort($query, $sort);
            }
        }

        return $query;
    }

    /**
     * @param Builder<Product> $query
     * @param array $sort
     */
    protected function applySingleSort(Builder $query, array $sort): void
    {
        $key = $sort['key'];
        $order = $sort['order'] ?? 'asc';

        switch ($key) {
            case 'id':
            case 'name':
            case 'description':
            case 'created_at':
            case 'updated_at':
                $query->orderBy($key, $order);
                break;

            default:
                throw new \InvalidArgumentException("Invalid sort key: {$key}");
        }
    }
}
