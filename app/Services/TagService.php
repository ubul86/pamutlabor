<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\Interfaces\TagInterface;
use Illuminate\Support\Collection;

class TagService
{
    public function __construct(
        protected TagInterface $tagRepo
    ) {
    }

    /**
     * @return Collection<int, Tag>
     */
    public function all(): Collection
    {
        return $this->tagRepo->all();
    }

    public function create(array $data): Tag
    {
        return $this->tagRepo->create($data);
    }
}
