<?php

namespace Tests\Feature\Livewire\Product;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use App\Models\LocationInventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Product\ProductList;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function renders_successfully()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ProductList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_products()
    {
        $this->actingAs(User::factory()->create());
        Product::factory()->count(15)->create();

        Livewire::test(ProductList::class)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 10; // Default perPage is 10
            });
    }

    /** @test */
    public function can_paginate_products()
    {
        $this->actingAs(User::factory()->create());
        Product::factory()->count(25)->create();

        Livewire::test(ProductList::class)
            ->set('perPage', 20)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 20;
            });
    }

    /** @test */
    public function can_search_for_a_product_by_name()
    {
        $this->actingAs(User::factory()->create());
        Product::factory()->create(['name' => 'My Awesome Product']);
        Product::factory()->create(['name' => 'Another Product']);

        Livewire::test(ProductList::class)
            ->set('search', 'Awesome')
            ->assertViewHas('products', function ($products) {
                return $products->count() === 1 && $products->first()->name === 'My Awesome Product';
            });
    }

    /** @test */
    public function can_search_for_a_product_by_sku()
    {
        $this->actingAs(User::factory()->create());
        Product::factory()->create(['name' => 'Product A', 'sku' => 'SKU123']);
        Product::factory()->create(['name' => 'Product B', 'sku' => 'SKU456']);

        Livewire::test(ProductList::class)
            ->set('search', 'SKU123')
            ->assertViewHas('products', function ($products) {
                return $products->count() === 1 && $products->first()->name === 'Product A';
            });
    }

    /** @test */
    public function can_filter_by_category()
    {
        $this->actingAs(User::factory()->create());
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Product::factory()->count(5)->create(['category_id' => $categoryA->id]);
        Product::factory()->count(3)->create(['category_id' => $categoryB->id]);

        Livewire::test(ProductList::class)
            ->set('categoryFilter', $categoryA->id)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 5;
            });
    }

    /** @test */
    public function can_delete_a_product()
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        // Ensure the product has no stock
        $product->locationInventories()->delete();

        Livewire::test(ProductList::class)
            ->call('confirmDelete', $product->id)
            ->call('deleteProduct');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function cannot_delete_a_product_with_stock()
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        $location = Location::factory()->create();
        LocationInventory::factory()->create([
            'inventoriable_id' => $product->id,
            'inventoriable_type' => 'product',
            'location_id' => $location->id,
            'stock_quantity' => 10,
        ]);

        Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /** @test */
    public function cannot_delete_a_product_linked_to_a_sales_order()
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        $salesOrder = SalesOrder::factory()->create();
        SalesOrderItem::factory()->create([
            'saleable_id' => $product->id,
            'saleable_type' => 'product',
            'sales_order_id' => $salesOrder->id,
        ]);

        Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
