<?php

namespace App\Repositories\Interfaces;

use App\Models\Tag;
use Illuminate\Support\Collection;

interface TagInterface
{
    /**
     * @return Collection<int, Tag>
     */
    public function all(): Collection;
    public function create(array $data): Tag;
}
