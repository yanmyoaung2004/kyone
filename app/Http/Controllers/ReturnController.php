<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnStoreRequest;
use App\Http\Requests\ReturnUpdateRequest;
use App\Http\Resources\ReturnCollection;
use App\Http\Resources\ReturnResource;
use App\Models\Return;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReturnController extends Controller
{
    public function index(Request $request): ReturnCollection
    {
        $returns = Return::all();

        return new ReturnCollection($returns);
    }

    public function store(ReturnStoreRequest $request): ReturnResource
    {
        $return = Return::create($request->validated());

        return new ReturnResource($return);
    }

    public function show(Request $request, Return $return): ReturnResource
    {
        return new ReturnResource($return);
    }

    public function update(ReturnUpdateRequest $request, Return $return): ReturnResource
    {
        $return->update($request->validated());

        return new ReturnResource($return);
    }

    public function destroy(Request $request, Return $return): Response
    {
        $return->delete();

        return response()->noContent();
    }
}
