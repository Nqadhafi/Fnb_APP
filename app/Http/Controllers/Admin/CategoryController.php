<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function index()
    {
        $rows = Category::orderBy('name')->paginate(20);
        return view('admin.categories.index', compact('rows'));
    }

    public function create() { return view('admin.categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:120',
            'slug' => 'nullable|max:150|unique:categories,slug',
            'description' => 'nullable|string',
            'is_active' => 'sometimes',

        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('ok','Kategori dibuat.');
    }

    public function edit(Category $category) { return view('admin.categories.edit', compact('category')); }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|max:120',
            'slug' => 'required|max:150|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes',

        ]);
        $data['is_active'] = $request->has('is_active');
        $category->update($data);
        return back()->with('ok','Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('ok','Kategori dihapus.');
    }
}
