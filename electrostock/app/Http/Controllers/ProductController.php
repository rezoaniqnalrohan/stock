<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['brand', 'category'])
            ->withSum('stocks as total_stock', 'quantity')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"))
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
        $product = Product::create($this->validated($request));
        // seed a zero stock row per outlet so the product shows everywhere
        foreach (Outlet::pluck('id') as $oid) {
            Stock::firstOrCreate(['product_id' => $product->id, 'outlet_id' => $oid], ['quantity' => 0]);
        }

        return redirect()->route('products.index')->with('status', "Product “{$product->name}” created.");
    }

    public function edit(Product $product)
    {
        return view('products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('products.index')->with('status', "Product “{$product->name}” updated.");
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku' . ($product ? ",{$product->id}" : '')],
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'variant' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'reorder_point' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ];
    }
}
