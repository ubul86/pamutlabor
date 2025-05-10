<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected ProductInterface $productRepo
    ) {
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<int, Product>
     */
    public function all(array $filters): LengthAwarePaginator
    {
        return $this->productRepo->all($filters);
    }

    public function find(int $id): Product
    {
        return $this->productRepo->find($id);
    }

    public function create(array $data): Product
    {
        return $this->productRepo->createWithTags($data);
    }

    public function update(int $id, array $data): Product
    {
        return $this->productRepo->updateWithTags($id, $data);
    }

    public function delete(int $id): bool|null
    {
        return $this->productRepo->delete($id);
    }

    public function syncTags(Product $product, array $tagIds): Product
    {
        return $this->productRepo->syncTags($product, $tagIds);
    }
}
