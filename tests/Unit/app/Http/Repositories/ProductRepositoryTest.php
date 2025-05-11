<?php

namespace Tests\Unit\app\Http\Repositories;

use App\Models\Product;
use App\Models\Tag;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new ProductRepository();
    }

    /** @test */
    public function it_creates_a_product_with_tags()
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'A short description.',
            'price' => 99.99,
            'tags' => ['electronics', 'gadgets'],
        ];

        $product = $this->repo->createWithTags($data);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
        $this->assertCount(2, $product->tags);
        $this->assertEqualsCanonicalizing(['electronics', 'gadgets'], $product->tags->pluck('name')->toArray());
    }

    /** @test */
    public function it_updates_product_and_syncs_tags()
    {
        $product = Product::factory()->create();
        $tag1 = Tag::create(['name' => 'old']);
        $product->tags()->attach($tag1);

        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated desc',
            'price' => 149.99,
            'tags' => ['new', 'awesome'],
        ];

        $updated = $this->repo->updateWithTags($product->id, $data);

        $this->assertEquals('Updated Product', $updated->name);
        $this->assertCount(2, $updated->tags);
        $this->assertEqualsCanonicalizing(['new', 'awesome'], $updated->tags->pluck('name')->toArray());
    }

    /** @test */
    public function it_deletes_a_product_and_detaches_tags()
    {
        $product = Product::factory()->create();
        $tag = Tag::create(['name' => 'delete-me']);
        $product->tags()->attach($tag);

        $result = $this->repo->delete($product->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseHas('tags', ['name' => 'delete-me']);
    }
}
