<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TravelOrderTest extends TestCase {
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    private function authHeader(): array {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_user_can_create_travel_order(): void {
        $response = $this->postJson('/api/travel-orders', [
            'requester_name' => 'João Silva',
            'destination' => 'São Paulo',
            'departure_date' => '2026-04-01',
            'return_date' => '2026-04-10',
        ], $this->authHeader());

        $response->assertStatus(201)
            ->assertJsonFragment(['destination' => 'São Paulo']);
    }

    public function test_user_can_list_own_travel_orders(): void {
        TravelOrder::factory()->count(3)->create(['user_id' => $this->user->id]);
        TravelOrder::factory()->count(2)->create(); // de outro usuário

        $response = $this->getJson('/api/travel-orders', $this->authHeader());

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_view_own_travel_order(): void {
        $order = TravelOrder::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/travel-orders/{$order->id}", $this->authHeader());

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    public function test_user_cannot_view_other_users_travel_order(): void {
        $order = TravelOrder::factory()->create(); // de outro usuário

        $response = $this->getJson("/api/travel-orders/{$order->id}", $this->authHeader());

        $response->assertStatus(403);
    }

    public function test_owner_cannot_update_own_order_status(): void {
        $order = TravelOrder::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/travel-orders/{$order->id}/status", [
            'status' => 'approved',
        ], $this->authHeader());

        $response->assertStatus(403);
    }

    public function test_other_user_can_approve_order(): void {
        $order = TravelOrder::factory()->create(['user_id' => $this->user->id]);

        $otherUser = User::factory()->create();
        $otherToken = JWTAuth::fromUser($otherUser);

        $response = $this->patchJson("/api/travel-orders/{$order->id}/status", [
            'status' => 'approved',
        ], ['Authorization' => "Bearer {$otherToken}"]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'approved']);
    }

    public function test_can_filter_orders_by_status(): void {
        TravelOrder::factory()->create(['user_id' => $this->user->id, 'status' => 'approved']);
        TravelOrder::factory()->create(['user_id' => $this->user->id, 'status' => 'requested']);

        $response = $this->getJson('/api/travel-orders?status=approved', $this->authHeader());

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_approved_order_can_be_cancelled(): void {
        $order = TravelOrder::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'approved',
        ]);

        $otherUser = User::factory()->create();
        $otherToken = JWTAuth::fromUser($otherUser);

        $response = $this->patchJson("/api/travel-orders/{$order->id}/status", [
            'status' => 'cancelled',
        ], ['Authorization' => "Bearer {$otherToken}"]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'cancelled']);
    }
}