<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by material
        if ($request->has('material') && $request->material) {
            $query->where('material', $request->material);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(12);

        $categories = Product::active()->distinct('category')->pluck('category');
        $materials = Product::active()->distinct('material')->pluck('material');

        return view('customer.products.index', compact('products', 'categories', 'materials'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        return view('customer.products.show', compact('product'));
    }
}
