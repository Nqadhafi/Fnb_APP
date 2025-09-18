<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DiningTable, TableSession};
use Illuminate\Http\Request;

class TableSessionController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function open(Request $request, DiningTable $table)
    {
        $request->validate(['guest_count' => 'required|integer|min:1']);
        abort_if(TableSession::where('dining_table_id',$table->id)->whereNull('closed_at')->exists(), 400, 'Sesi meja masih aktif.');

        $sess = TableSession::create([
            'dining_table_id' => $table->id,
            'opened_by'       => $request->user()->id,
            'guest_count'     => $request->guest_count,
        ]);

        $table->update(['status' => 'occupied']);
        return back()->with('ok','Sesi meja dibuka.');
    }

    public function close(Request $request, TableSession $session)
    {
        $session->update(['closed_at' => now()]);
        $session->table->update(['status' => 'available']);
        return back()->with('ok','Sesi meja ditutup.');
    }
}
