<?php

namespace App\Repositories;

use App\Filters\ProductFilter;
use App\Models\Product;
use App\Models\Tag;
use App\Repositories\Interfaces\ProductInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Collection;

class ProductRepository implements ProductInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<int, Product>
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Product::with('tags');

        $productFilter = new ProductFilter($filters);
        $filteredQuery = $productFilter->apply($query);

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $filteredQuery->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): Product
    {
        try {
            return Product::with('tags')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Product not found: ' . $e->getMessage());
        }
    }

    public function createWithTags(array $data): Product
    {
        try {
            $tags = $this->extractTags($data);
            $product = Product::create($data);
            $product->tags()->sync($tags);
            return $product->load('tags');
        } catch (Exception $e) {
            throw new Exception('Failed to create: ' . $e->getMessage());
        }
    }

    public function updateWithTags(int $id, array $data): Product
    {
        try {
            $product = Product::findOrFail($id);
            $tags = $this->extractTags($data);
            $product->update($data);
            if ($tags !== null) {
                $product->tags()->sync($tags);
            }
            return $product->load('tags');
        } catch (ModelNotFoundException $e) {
            throw new Exception('Product not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update: ' . $e->getMessage());
        }
    }

    public function delete(int $id): bool|null
    {
        try {
            $product = Product::findOrFail($id);
            $product->tags()->detach();
            return $product->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Product not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete: ' . $e->getMessage());
        }
    }

    public function syncTags(Product $product, ?array $tagIds): Product
    {
        try {
            $product->tags()->sync($tagIds);
            return $product->load('tags');
        } catch (\Exception $e) {
            throw new Exception('Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return Collection<int, int>|null
     */
    private function extractTags(array &$data): Collection|null
    {
        if (!isset($data['tags'])) {
            return null;
        }

        $tagIds = (new Collection($data['tags']))->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });

        unset($data['tags']);
        return $tagIds;
    }
}
