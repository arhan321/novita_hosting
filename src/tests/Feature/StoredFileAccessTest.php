<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoredFileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_open_an_order_file_from_their_own_order(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('order-files/design.png', 'design');

        $customer = $this->user('customer', 'customer@example.test');
        $order = $this->orderFor($customer);
        $file = OrderFile::create([
            'order_id' => $order->id,
            'file_type' => 'design',
            'file_path' => 'order-files/design.png',
            'file_name' => 'design.png',
        ]);

        $this->actingAs($customer)
            ->get(route('files.order-files.show', $file))
            ->assertOk();
    }

    public function test_customer_cannot_open_another_customers_order_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('order-files/design.png', 'design');

        $owner = $this->user('customer', 'owner@example.test');
        $otherCustomer = $this->user('customer', 'other@example.test');
        $order = $this->orderFor($owner);
        $file = OrderFile::create([
            'order_id' => $order->id,
            'file_type' => 'design',
            'file_path' => 'order-files/design.png',
            'file_name' => 'design.png',
        ]);

        $this->actingAs($otherCustomer)
            ->get(route('files.order-files.show', $file))
            ->assertForbidden();
    }

    public function test_admin_can_open_a_payment_proof(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('payment-proofs/proof.jpg', 'proof');

        $customer = $this->user('customer', 'customer@example.test');
        $admin = $this->user('admin', 'admin@example.test');
        $order = $this->orderFor($customer);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'bank_transfer',
            'payment_type' => 'full',
            'amount' => 100000,
            'payment_proof' => 'payment-proofs/proof.jpg',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('files.payment-proofs.show', $payment))
            ->assertOk();
    }

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function orderFor(User $customer): Order
    {
        return Order::create([
            'user_id' => $customer->id,
            'order_number' => 'ORD'.str_pad((string) $customer->id, 8, '0', STR_PAD_LEFT),
            'type' => 'custom',
            'status' => 'pending',
            'total_price' => 100000,
        ]);
    }
}
