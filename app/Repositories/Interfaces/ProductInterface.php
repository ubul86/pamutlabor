<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<int, Product>
     */
    public function all(array $filters): LengthAwarePaginator;
    public function find(int $id): Product;
    public function createWithTags(array $data): Product;
    public function updateWithTags(int $id, array $data): Product;
    public function delete(int $id): bool|null;
    public function syncTags(Product $product, ?array $tagIds): Product;
}
