<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    // List semua produk katalog
    public function index(): View
    {
        $products = Product::latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // Form create
    public function create(): View
    {
        return view('admin.products.create');
    }

    // Store
    public function store()
    {
        // TODO: Store product
    }

    // Detail
    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    // Form edit
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    // Update
    public function update(Product $product)
    {
        // TODO: Update product
    }

    // Delete
    public function destroy(Product $product)
    {
        // TODO: Delete product
    }
}
