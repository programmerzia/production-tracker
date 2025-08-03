<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index()
    {
        return $this->productService->getAll();
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return $this->productService->show($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);
        return response()->json(null, 204);
    }

    public function performance($id)
    {
        return response()->json($this->productService->getPerformanceSummary($id));
    }

    public function dropdownList()
    {
        return response()->json($this->productService->dropdownList());
    }
}
