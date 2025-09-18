<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->with(['products' => function($q){
            $q->active()->orderBy('name');
        }])->orderBy('name')->get();

        $featured = Product::active()->orderByDesc('id')->take(8)->get();

        return view('public.home', compact('categories','featured'));
    }
}
