<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportProjectSummary($id)
    {
        $project = Project::with('items')->findOrFail($id);

        $statusCounts = $project->items->groupBy('status')->map(fn($group) => $group->count());

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=project_{$project->id}_summary.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Status', 'Count'];

        $callback = function () use ($statusCounts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($statusCounts as $status => $count) {
                fputcsv($file, [$status, $count]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportAllProjectsSummary()
    {
        $projects = Project::with('items')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=all_projects_summary.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');

            foreach ($projects as $project) {
                $statusCounts = $project->items->groupBy('status')->map(fn($group) => $group->count());

                foreach ($statusCounts as $status => $count) {
                    fputcsv($file, [
                        $project->id,
                        $project->name,
                        $status,
                        $count
                    ]);
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportProductPerformance($id)
    {
        $product = Product::with('projects.items')->findOrFail($id);

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=product_{$product->id}_performance.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Project ID', 'Project Name', 'Status', 'Count'];

        $callback = function () use ($product, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($product->projects as $project) {
                $statusCounts = $project->items->groupBy('status')->map(fn($group) => $group->count());

                foreach ($statusCounts as $status => $count) {
                    fputcsv($file, [
                        $project->id,
                        $project->name,
                        $status,
                        $count
                    ]);
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
