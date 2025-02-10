<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->address = $validatedData['address'];
            $customer->phone = $validatedData['phone'];
            $customer->save();

            return response()->json(['message' => 'Customer created successfully', 'customer' => $customer], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer creation failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomer($id)
    {
        try {
            $customer = Customer::with('user')->findOrFail($id);
            return response()->json(['customer' => $customer], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer not found', 'message' => $e->getMessage()], 404);
        }
    }

    public function getAllCustomers()
    {
        try {
            $customers = Customer::with('user')->get();
            return response()->json(['customers' => $customers], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve customers', 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $id,
            'phone' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'password' => 'sometimes|required|string',
        ]);

        try {
            $customer = Customer::findOrFail($id);
            $user = User::findOrFail($customer->user_id);

            if (isset($validatedData['name'])) {
                $user->name = $validatedData['name'];
            }
            if (isset($validatedData['email'])) {
                $user->email = $validatedData['email'];
            }
            if (isset($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
            }
            $user->save();

            if (isset($validatedData['address'])) {
                $customer->address = $validatedData['address'];
            }
            if (isset($validatedData['phone'])) {
                $customer->phone = $validatedData['phone'];
            }
            $customer->save();
            $customer = Customer::with('user')->get();
            return response()->json(['message' => 'Customer updated successfully', 'customer' => $customer], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer update failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer delete failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function histories($id)
    {
        try {
            $histories = Order::where('customer_id', $id)->get();
            return response()->json(["histories" => $histories]);
        } catch (Exception $e) {
            return response()->json(['error' => "Get customer historeis failed", 'message' => $e->getMessage()]);
        }
    }
    public function getCustomerDetails($customerId)
    {
        // Get the customer along with total orders, last order, and all orders
        $customer = Customer::withCount('orders') // This will count all orders
            ->with(['orders' => function ($query) {
                // Get all orders
                $query->orderBy('created_at', 'desc'); // Sort orders by created_at (latest first)
            }])
            ->findOrFail($customerId);

        // Get the last order (first one after sorting)
        $lastOrder = $customer->orders->first();

        return response()->json([
            'customer' => $customer,
            'total_orders' => $customer->orders_count, // Total orders
            'last_order' => $lastOrder, // Last order
            'all_orders' => $customer->orders, // All orders
        ]);
    }
}
