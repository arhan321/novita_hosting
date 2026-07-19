<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_product_catalog_index(): void
    {
        Product::create([
            'name' => 'Pagar Minimalis',
            'description' => 'Description here',
            'price' => 1500000,
            'material' => 'Besi',
            'category' => 'Pagar',
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Pagar Minimalis');
    }

    public function test_guest_can_access_product_catalog_detail(): void
    {
        $product = Product::create([
            'name' => 'Pagar Minimalis',
            'description' => 'Description here',
            'price' => 1500000,
            'material' => 'Besi',
            'category' => 'Pagar',
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.products.show', $product));

        $response->assertStatus(200);
        $response->assertSee('Pagar Minimalis');
        $response->assertSee('Pesan Produk Ini');
    }

    public function test_guest_cannot_order_without_login_and_is_redirected_to_login(): void
    {
        $product = Product::create([
            'name' => 'Pagar Minimalis',
            'description' => 'Description here',
            'price' => 1500000,
            'material' => 'Besi',
            'category' => 'Pagar',
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.orders.catalog.create', $product));

        $response->assertRedirect(route('login'));
    }

    public function test_intended_redirect_works_after_logging_in(): void
    {
        $product = Product::create([
            'name' => 'Pagar Minimalis',
            'description' => 'Description here',
            'price' => 1500000,
            'material' => 'Besi',
            'category' => 'Pagar',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        // Access the protected route first as a guest, which saves the intended URL in session
        $response = $this->get(route('customer.orders.catalog.create', $product));
        $response->assertRedirect(route('login'));

        // Post login details
        $loginResponse = $this->post(route('login'), [
            'email' => 'customer@test.com',
            'password' => 'password',
        ]);

        // It should redirect to the intended URL: customer.orders.catalog.create
        $loginResponse->assertRedirect(route('customer.orders.catalog.create', $product));
    }
}
