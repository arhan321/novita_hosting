<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'material' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'is_available' => 'nullable|boolean',
            'estimation_days' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image', 'specifications']);
        
        // Filter empty specifications
        if ($request->has('specifications')) {
            $specs = array_filter($request->specifications, function($value) {
                return !empty($value);
            });
            $data['specifications'] = !empty($specs) ? $specs : null;
        }
        
        // Handle checkbox
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'material' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'is_available' => 'nullable|boolean',
            'estimation_days' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image', 'specifications']);
        
        // Filter empty specifications
        if ($request->has('specifications')) {
            $specs = array_filter($request->specifications, function($value) {
                return !empty($value);
            });
            $data['specifications'] = !empty($specs) ? $specs : null;
        }
        
        // Handle checkbox
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
