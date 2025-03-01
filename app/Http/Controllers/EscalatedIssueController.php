<?php

namespace App\Http\Controllers;

use App\Http\Requests\EscalatedIssueStoreRequest;
use App\Http\Requests\EscalatedIssueUpdateRequest;
use App\Http\Resources\EscalatedIssueCollection;
use App\Http\Resources\EscalatedIssueResource;
use App\Models\Driver;
use App\Models\EscalatedIssue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EscalatedIssueController extends Controller
{
    public function index()
    {
        $escalatedIssues = EscalatedIssue::with('driver.user')->orderBy('created_at', 'desc')->get();
        $escalatedIssues = $escalatedIssues->map(function ($escalatedIssue){
            $city = $escalatedIssue->getCity();
            $truck = $escalatedIssue->getTruck();
            $escalatedIssue->city = $city;
            $escalatedIssue->truck = $truck;
            return $escalatedIssue;
        });
        return response()->json($escalatedIssues);
    }

    public function store(Request $request)
    {
        $driver = Driver::where('user_id',$request->get('driver_id'))->first();
        if(!$driver){
            return response()->json("Driver does not exist");
        }

        $escalatedIssue = EscalatedIssue::create([
            'description' => $request->get('description'),
            'route_key' => $request->get('route_key'),
            'driver_id' => $driver->id,
            'priority' => $request->get('priority'),
            'status' => 'pending',
        ]);
        return new EscalatedIssueResource($escalatedIssue);
    }

    public function show(Request $request, EscalatedIssue $escalatedIssue): EscalatedIssueResource
    {
        return new EscalatedIssueResource($escalatedIssue);
    }

    public function update(EscalatedIssueUpdateRequest $request, EscalatedIssue $escalatedIssue): EscalatedIssueResource
    {
        $escalatedIssue->update($request->validated());

        return new EscalatedIssueResource($escalatedIssue);
    }

    public function destroy(Request $request, EscalatedIssue $escalatedIssue): Response
    {
        $escalatedIssue->delete();

        return response()->noContent();
    }

    public function updateStatus($id, Request $request){
        $status = $request->get('status');
        $escalatedIssue = EscalatedIssue::find($id);
        if(!$escalatedIssue){
            return response()->json("Escalated issue not found");
        }
        $escalatedIssue->status = $status;
        $escalatedIssue->save();
        return response()->json($escalatedIssue);
    }
}
