<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
    // Get all complaints
    public function index()
    {
        return response()->json(Complaint::all(), 200);
    }

    // Get a single complaint
    public function show($id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }
        return response()->json($complaint, 200);
    }

    // Create a new complaint
    public function store(Request $request)
    {

        try{
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'type' => 'required|in:delayed,faulty,wrong,missing'
        ]);

        $complaint = Complaint::create($validated);
        return response()->json(['message' => 'Complaint created successfully', 'complaint' => $complaint], 201);
    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),
            
        ], 422);
    }
    }

    // Update complaint
    public function update(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        try{

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'order_id' => 'sometimes|nullable|exists:orders,id',
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'type' => 'sometimes|in:delayed,faulty,wrong,missing'
        ]);

        $complaint->update($validated);
        return response()->json(['message' => 'Complaint updated successfully', 'complaint' => $complaint], 200);
    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),    
        ], 422);
    }
    }

    // Delete complaint
    public function destroy($id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        $complaint->delete();
        return response()->json(['message' => 'Complaint deleted successfully'], 200);
    }


    
    public function filterComplaint(Request $request)
    {
        // Get the filter parameters (status in this case)
        $filters = $request->query('filter', []);
        
        // Check if a valid status filter is provided
        $validStatuses =["open","in_progress","resolved","closed"];
        
        if (isset($filters['status']) && !in_array($filters['status'], $validStatuses)) {
            return response()->json(['message' => 'Invalid status provided'], 400);
        }
        // Build the query
        $query = Complaint::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Get the filtered orders
        $complaints = $query->get();

        // Check if no orders were found
        if ($complaints->isEmpty()) {
            return response()->json(['message' => 'No Complaint found for the given filter'], 404);
        }
        // Return the filtered orders
        return response()->json([
            'complaint_count' => $complaints->count(),
            'complaints' => $complaints// You can specify fields to return if needed
        ]);
    }

}
