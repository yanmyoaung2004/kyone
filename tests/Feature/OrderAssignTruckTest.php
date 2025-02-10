<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\OrderAssignTruck;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderAssignTruckTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_truck_with_assigned_orders()
    {
        // Arrange: Create test data
        $truck = OrderAssignTruck::factory()->create();
        $order = Order::factory()->create();
        $truck->order()->associate($order); // Assuming a relationship exists
        $truck->save();

        // Act: Call the API endpoint
        $response = $this->getJson(route('truck_assigned_order', ['id' => $truck->truck_id]));

        // Assert: Check response structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'truck' => [
                         '*' => [
                             'id',
                             'truck_id',
                             'order' => ['id', 'order_number'] // Adjust fields as needed
                         ]
                     ]
                 ]);
    }
}
