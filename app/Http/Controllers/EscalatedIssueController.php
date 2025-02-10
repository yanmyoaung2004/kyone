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
    public function index(Request $request): EscalatedIssueCollection
    {
        $escalatedIssues = EscalatedIssue::all();

        return new EscalatedIssueCollection($escalatedIssues);
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
}
