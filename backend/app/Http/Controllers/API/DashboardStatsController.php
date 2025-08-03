<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProductItem;

class DashboardStatsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $response = [];

        if (in_array($role, ['product_manager'])) {
            $response['total_products'] = Product::count();
        }

        if (in_array($role, ['product_manager', 'project_manager'])) {
            $response['active_projects'] = Project::count();
        }

        if (in_array($role, ['product_manager', 'project_manager', 'engineer'])) {
            $response['pending_items'] = ProductItem::where('status', 'pending')->count();
            $response['in_progress_items'] = ProductItem::where('status', 'in_progress')->count();
            $response['completed_items'] = ProductItem::where('status', 'completed')->count();
            $response['blocked_items'] = ProductItem::where('status', 'blocked')->count();
        }

        return response()->json($response);
    }
}
