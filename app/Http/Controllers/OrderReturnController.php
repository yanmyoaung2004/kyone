<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnStoreRequest;
use App\Http\Requests\ReturnUpdateRequest;
use App\Http\Resources\ReturnCollection;
use App\Http\Resources\ReturnResource;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderReturnController extends Controller
{
    public function index(Request $request): ReturnCollection
    {
        $returns = Order::where('isReturn', true)->get();
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
}
