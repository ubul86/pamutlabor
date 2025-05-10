<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class TagRepository implements TagInterface
{
    /**
     * @return Collection<int, Tag>
     */
    public function all(): Collection
    {
        return Tag::with('products')->get();
    }

    public function create(array $data): Tag
    {
        try {
            return Tag::create($data);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Failed to create: ' . $e->getMessage());
        }
    }
}
