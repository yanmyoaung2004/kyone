<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnStoreRequest;
use App\Http\Requests\ReturnUpdateRequest;
use App\Http\Resources\ReturnCollection;
use App\Http\Resources\ReturnResource;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderReturnController extends Controller
{
    public function index(Request $request): ReturnCollection
    {
        $returns = OrderReturn::all();

        return new ReturnCollection($returns);
    }

    public function store(ReturnStoreRequest $request): ReturnResource
    {
        $orderReturn = OrderReturn::create($request->validated());

        return new ReturnResource($orderReturn);
    }

    public function show(Request $request, OrderReturn $orderReturn): ReturnResource
    {
        return new ReturnResource($orderReturn);
    }

    public function update(ReturnUpdateRequest $request, OrderReturn $orderReturn): ReturnResource
    {
        $orderReturn->update($request->validated());

        return new ReturnResource($orderReturn);
    }

    public function destroy(Request $request, OrderReturn $orderReturn): Response
    {
        $orderReturn->delete();
        return response()->noContent();
    }
    public function filterOrderReturn(Request $request)
    {
        // Get filter parameters from the request
        $query = OrderReturn::query();

        // Filter by status (single or multiple values)
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Filter by order_id
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter by product_id
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Execute query and get results
        $orderReturns = $query->get();

        return response()->json($orderReturns);
    }
}
