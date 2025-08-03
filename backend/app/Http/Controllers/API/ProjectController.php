<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::with('product')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        return $project->load('product');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return response()->json($project);
    }
    public function summary($id)
    {
        $project = Project::with('items')->findOrFail($id);

        $summary = $project->items
            ->groupBy('status')
            ->map(fn($items) => count($items));

        return response()->json([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'status_summary' => $summary
        ]);
    }

    public function allSummary(){
        $projects = Project::with('product') // Optional: if you want product name too
        ->withCount([
            'items as total_items',
            'items as pending' => fn($q) => $q->where('status', 'pending'),
            'items as in_progress' => fn($q) => $q->where('status', 'in_progress'),
            'items as completed' => fn($q) => $q->where('status', 'completed'),
            'items as blocked' => fn($q) => $q->where('status', 'blocked'),
        ])
        ->get(['id', 'name', 'product_id']);

    $summary = $projects->map(fn($project) => [
        'project_id' => $project->id,
        'project_name' => $project->name,
        'product_name' => $project->product?->name, // optional
        'total_items' => $project->total_items,
        'pending' => $project->pending,
        'in_progress' => $project->in_progress,
        'completed' => $project->completed,
        'blocked' => $project->blocked,
    ]);
        return response()->json($summary);
    }

    public function projectList(){
        //list for dropdown
        $projects = Project::all();
        return response()->json($projects);
    }
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }
}
