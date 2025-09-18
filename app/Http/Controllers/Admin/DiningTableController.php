<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use Illuminate\Http\Request;

class DiningTableController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function index()
    {
        $rows = DiningTable::orderBy('code')->paginate(30);
        return view('admin.tables.index', compact('rows'));
    }

    public function create() { return view('admin.tables.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'     => 'required|max:20|unique:dining_tables,code',
            'name'     => 'nullable|max:120',
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:available,occupied,reserved,disabled',
        ]);
        DiningTable::create($data);
        return redirect()->route('admin.tables.index')->with('ok','Meja dibuat.');
    }

    public function edit(DiningTable $table) { return view('admin.tables.edit', compact('table')); }

    public function update(Request $request, DiningTable $table)
    {
        $data = $request->validate([
            'code'     => 'required|max:20|unique:dining_tables,code,'.$table->id,
            'name'     => 'nullable|max:120',
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:available,occupied,reserved,disabled',
        ]);
        $table->update($data);
        return back()->with('ok','Meja diperbarui.');
    }

    public function destroy(DiningTable $table)
    {
        $table->delete();
        return back()->with('ok','Meja dihapus.');
    }
}
