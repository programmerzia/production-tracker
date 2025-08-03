<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductItem;
use Illuminate\Http\Request;

class ProductItemController extends Controller
{
    public function index()
    {
        return ProductItem::with('project')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'status' => 'required|in:pending,in_progress,completed,blocked',
        ]);

        $item = ProductItem::create($validated);
        return response()->json($item, 201);
    }

    public function show(ProductItem $productItem)
    {
        return $productItem->load('project');
    }

    public function update(Request $request, ProductItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'version' => 'nullable|string|max:50',
            'status' => 'required|in:pending,in_progress,completed,blocked',
        ]);
        $item->update($validated);

        return response()->json($item);
    }

    public function updateStatus(Request $request, ProductItem $item)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,blocked',
        ]);

        $item->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated', 'item' => $item]);
    }


    public function destroy(ProductItem $productItem)
    {
        $productItem->delete();
        return response()->json(null, 204);
    }
}
