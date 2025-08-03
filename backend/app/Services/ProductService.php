<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function getAll()
    {
        return Product::with('projects')->latest()->get();
    }

    public function store(array $data): Product
    {
        return Product::create($data);
    }

    public function show(Product $product): Product
    {
        return $product->load('projects');
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function getPerformanceSummary(int $productId)
    {
        $product = Product::with('projects.items')->findOrFail($productId);

        $projects = $product->projects->map(function ($project) {
            return [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'status_summary' => $project->items
                    ->groupBy('status')
                    ->map(fn($items) => count($items))
            ];
        });

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'projects' => $projects
        ];
    }

    public function dropdownList()
    {
        return Product::select('id', 'name')->get();
    }
}
