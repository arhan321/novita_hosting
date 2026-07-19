<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected FileUploadService $uploadService
    ) {}

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'material' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'is_available' => 'nullable|boolean',
            'estimation_days' => 'nullable|integer|min:0',
        ]);

        $data = $this->productData($validated, $request);
        $uploadedImage = null;

        try {
            if ($request->hasFile('image')) {
                $uploadedImage = $this->uploadService->uploadPublic(
                    $request->file('image'),
                    'products'
                );
                $data['image_path'] = $uploadedImage;
            }

            $product = Product::create($data);
        } catch (Throwable $exception) {
            $this->uploadService->deletePublic($uploadedImage);

            throw $exception;
        }

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'material' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'is_available' => 'nullable|boolean',
            'estimation_days' => 'nullable|integer|min:0',
        ]);

        $data = $this->productData($validated, $request);
        $oldImage = $product->getRawOriginal('image_path');
        $uploadedImage = null;

        try {
            if ($request->hasFile('image')) {
                $uploadedImage = $this->uploadService->uploadPublic(
                    $request->file('image'),
                    'products'
                );
                $data['image_path'] = $uploadedImage;
            }

            $product->update($data);
        } catch (Throwable $exception) {
            $this->uploadService->deletePublic($uploadedImage);

            throw $exception;
        }

        if ($uploadedImage !== null && $oldImage !== $uploadedImage) {
            $this->uploadService->deletePublic($oldImage);
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $imagePath = $product->getRawOriginal('image_path');
        $product->delete();
        $this->uploadService->deletePublic($imagePath);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function productData(array $validated, Request $request): array
    {
        $specifications = array_filter(
            $validated['specifications'] ?? [],
            fn (mixed $value): bool => $value !== null && $value !== ''
        );

        return [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'material' => $validated['material'],
            'category' => $validated['category'],
            'specifications' => $specifications !== [] ? $specifications : null,
            'stock' => $validated['stock'] ?? 0,
            'min_order' => $validated['min_order'] ?? 1,
            'unit' => $validated['unit'] ?? null,
            'estimation_days' => $validated['estimation_days'] ?? null,
            'is_available' => $request->boolean('is_available'),
        ];
    }
}
