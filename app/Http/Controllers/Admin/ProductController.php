<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, ProductImage};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function index()
    {
        $rows = Product::with('category')->orderBy('name')->paginate(20);
        return view('admin.products.index', compact('rows'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'     => 'nullable|exists:categories,id',
            'name'            => 'required|max:255',
            'slug'            => 'nullable|max:255|unique:products,slug',
            'sku'             => 'nullable|max:100|unique:products,sku',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'discount_price'  => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'is_active' => 'sometimes',
            'options_schema'  => 'nullable|array',
            'main_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');
        if (isset($data['options_schema'])) {
            $data['options_schema'] = json_encode($data['options_schema']);
        }

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products','public');
            $data['main_image_path'] = $path;
            $data['main_image_disk'] = 'public';
        }

        $product = Product::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $img) {
                $path = $img->store('products','public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'disk'       => 'public',
                    'is_primary' => false,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('ok','Produk dibuat.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $images = $product->images()->orderBy('is_primary','desc')->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product','categories','images'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'     => 'nullable|exists:categories,id',
            'name'            => 'required|max:255',
            'slug'            => 'required|max:255|unique:products,slug,'.$product->id,
            'sku'             => 'nullable|max:100|unique:products,sku,'.$product->id,
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'discount_price'  => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'is_active' => 'sometimes',
            'options_schema'  => 'nullable|array',
            'main_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_active'] = $request->has('is_active');
        if (isset($data['options_schema'])) {
            $data['options_schema'] = json_encode($data['options_schema']);
        }

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products','public');
            $data['main_image_path'] = $path;
            $data['main_image_disk'] = 'public';
        }

        $product->update($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $img) {
                $path = $img->store('products','public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'disk'       => 'public',
                    'is_primary' => false,
                    'sort_order' => $i,
                ]);
            }
        }

        return back()->with('ok','Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('ok','Produk dihapus.');
    }

    // Hapus foto galeri
    public function destroyImage(ProductImage $image)
    {
        $image->delete();
        return back()->with('ok','Gambar dihapus.');
    }

    // Tandai sebagai primary
    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('ok','Gambar utama diset.');
    }
}
