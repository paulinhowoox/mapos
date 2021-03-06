<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Events\ProductCreatedEvent;
use App\Events\ProductDeletedEvent;
use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Admin\ProductController
 */
class ProductControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_displays_view()
    {
        $loggedUser = factory(User::class)->create();
        $products = factory(Product::class, 3)->create();

        $response = $this
            ->actingAs($loggedUser)
            ->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertViewIs('admin.products.index');
        $response->assertViewHas('products');
    }

    /**
     * @test
     */
    public function create_displays_view()
    {
        $loggedUser = factory(User::class)->create();
        $response = $this
            ->actingAs($loggedUser)
            ->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertViewIs('admin.products.create');
        $response->assertViewHas('product');
    }

    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Admin\ProductController::class,
            'store',
            \App\Http\Requests\Admin\ProductStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves_and_redirects()
    {
        $loggedUser = factory(User::class)->create();
        $name = $this->faker->word;
        $description = $this->faker->word;
        $barcode = $this->faker->word;
        $purchase_price = $this->faker->randomFloat(2, 0, 99999999.99);
        $sale_price = $this->faker->randomFloat(2, 0, 99999999.99);
        $unit = $this->faker->word;
        $quantity_in_stock = $this->faker->randomNumber();
        $minimum_quantity_in_stock = $this->faker->randomNumber();

        Event::fake();

        $response = $this
            ->actingAs($loggedUser)
            ->post(route('admin.products.store'), [
                'name' => $name,
                'description' => $description,
                'barcode' => $barcode,
                'purchase_price' => $purchase_price,
                'sale_price' => $sale_price,
                'unit' => $unit,
                'quantity_in_stock' => $quantity_in_stock,
                'minimum_quantity_in_stock' => $minimum_quantity_in_stock,
            ]);

        $products = Product::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('barcode', $barcode)
            ->where('purchase_price', $purchase_price)
            ->where('sale_price', $sale_price)
            ->where('unit', $unit)
            ->where('quantity_in_stock', $quantity_in_stock)
            ->where('minimum_quantity_in_stock', $minimum_quantity_in_stock)
            ->get();
        $this->assertCount(1, $products);
        $product = $products->first();

        $response->assertRedirect(route('admin.products.index'));

        Event::assertDispatched(ProductCreatedEvent::class, function ($event) use ($product) {
            return $event->product->is($product);
        });
    }

    /**
     * @test
     */
    public function show_displays_view()
    {
        $loggedUser = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $response = $this
            ->actingAs($loggedUser)
            ->get(route('admin.products.show', $product));

        $response->assertOk();
        $response->assertViewIs('admin.products.show');
        $response->assertViewHas('product');
    }

    /**
     * @test
     */
    public function edit_displays_view()
    {
        $loggedUser = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $response = $this
            ->actingAs($loggedUser)
            ->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertViewIs('admin.products.edit');
        $response->assertViewHas('product');
    }

    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Admin\ProductController::class,
            'update',
            \App\Http\Requests\Admin\ProductUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_saves_and_redirects()
    {
        $loggedUser = factory(User::class)->create();
        $product = factory(Product::class)->create();
        $name = $this->faker->word;
        $description = $this->faker->word;
        $barcode = $this->faker->word;
        $purchase_price = $this->faker->randomFloat(2, 0, 99999999.99);
        $sale_price = $this->faker->randomFloat(2, 0, 99999999.99);
        $unit = $this->faker->word;
        $quantity_in_stock = $this->faker->randomNumber();
        $minimum_quantity_in_stock = $this->faker->randomNumber();

        Event::fake();

        $response = $this
            ->actingAs($loggedUser)
            ->put(route('admin.products.update', $product), [
                'name' => $name,
                'description' => $description,
                'barcode' => $barcode,
                'purchase_price' => $purchase_price,
                'sale_price' => $sale_price,
                'unit' => $unit,
                'quantity_in_stock' => $quantity_in_stock,
                'minimum_quantity_in_stock' => $minimum_quantity_in_stock,
            ]);

        $products = Product::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('barcode', $barcode)
            ->where('purchase_price', $purchase_price)
            ->where('sale_price', $sale_price)
            ->where('unit', $unit)
            ->where('quantity_in_stock', $quantity_in_stock)
            ->where('minimum_quantity_in_stock', $minimum_quantity_in_stock)
            ->get();
        $this->assertCount(1, $products);
        $product = $products->first();

        $response->assertRedirect(route('admin.products.index'));

        Event::assertDispatched(ProductUpdatedEvent::class, function ($event) use ($product) {
            return $event->product->is($product);
        });
    }

    /**
     * @test
     */
    public function destroy_deletes()
    {
        $loggedUser = factory(User::class)->create();
        $product = factory(Product::class)->create();

        Event::fake();

        $response = $this
            ->actingAs($loggedUser)
            ->delete(route('admin.products.destroy', $product));

        $response->assertJson([
            'name' => $product->name,
            'description' => $product->description,
            'barcode' => $product->barcode,
            'purchase_price' => $product->purchase_price,
            'sale_price' => $product->sale_price,
            'unit' => $product->unit,
            'quantity_in_stock' => $product->quantity_in_stock,
            'minimum_quantity_in_stock' => $product->minimum_quantity_in_stock,
        ]);
        $response->assertOk();

        $this->assertDeleted($product);

        Event::assertDispatched(ProductDeletedEvent::class, function ($event) use ($product) {
            return $event->product->is($product);
        });
    }
}
