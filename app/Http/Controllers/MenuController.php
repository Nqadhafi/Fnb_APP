<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');
        $query = Product::active()->orderBy('name');

        if ($categorySlug) {
            $cat = Category::where('slug',$categorySlug)->first();
            if ($cat) $query->where('category_id', $cat->id);
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('public.menu', compact('products','categories','categorySlug'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $images = $product->images()->orderBy('is_primary','desc')->orderBy('sort_order')->get();
        return view('public.product', compact('product','images'));
    }
}
