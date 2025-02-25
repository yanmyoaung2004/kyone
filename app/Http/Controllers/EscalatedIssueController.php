<?php

namespace App\Http\Controllers;

use App\Http\Requests\EscalatedIssueStoreRequest;
use App\Http\Requests\EscalatedIssueUpdateRequest;
use App\Http\Resources\EscalatedIssueCollection;
use App\Http\Resources\EscalatedIssueResource;
use App\Models\EscalatedIssue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EscalatedIssueController extends Controller
{
    public function index()
    {
        $escalatedIssues = EscalatedIssue::with('driver.user', 'order.invoice', 'order.customer.user')->orderBy('created_at', 'desc')->get();
        return response()->json($escalatedIssues);
    }

    public function store(EscalatedIssueStoreRequest $request): EscalatedIssueResource
    {
        $escalatedIssue = EscalatedIssue::create($request->validated());

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

    public function updateStatus(Request $request, $id)
    {
        $escalatedIssue = EscalatedIssue::find($id);

        if (!$escalatedIssue) {
            return response()->json(['message' => 'Escalated issue not found'], 404);
        }

        $escalatedIssue->status = $request->status;
        $escalatedIssue->save();

        return response()->json($escalatedIssue);
    }
}
