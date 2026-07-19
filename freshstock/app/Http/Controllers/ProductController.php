<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'unit', 'supplier'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('products.form', $this->formData(new Product));
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect('/products')->with('status', 'Product created.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'supplier', 'batches.warehouse']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect('/products')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect('/products')->with('status', 'Product deleted.');
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'unique:products,sku'.($product ? ",$product->id" : '')],
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'storage_temp' => ['required', 'in:ambient,chilled,frozen'],
            'shelf_life_days' => ['required', 'integer', 'min:0'],
            'reorder_point' => ['required', 'integer', 'min:0'],
        ]);
    }
}
