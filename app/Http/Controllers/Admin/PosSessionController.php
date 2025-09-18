<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use Illuminate\Http\Request;

class PosSessionController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function index()
    {
        $rows = PosSession::latest()->paginate(20);
        return view('admin.pos.index', compact('rows'));
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'counter_name'  => 'nullable|string|max:120',
            'opening_float' => 'required|numeric|min:0',
        ]);

        $row = PosSession::create([
            'counter_name'  => $data['counter_name'] ?: 'Front Cashier',
            'opened_by'     => $request->user()->id,
            'opening_float' => $data['opening_float'],
        ]);

        return redirect()->route('admin.pos.index')->with('ok','Sesi kasir dibuka.');
    }

    public function close(Request $request, PosSession $session)
    {
        $data = $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $expected = $session->opening_float + $session->cash_total - $session->noncash_total; // sederhana; bisa disesuaikan
        $variance = $data['actual_cash'] - $expected;

        $session->update([
            'closed_by'       => $request->user()->id,
            'closed_at'       => now(),
            'expected_cash'   => $expected,
            'actual_cash'     => $data['actual_cash'],
            'cash_variance'   => $variance,
            'notes'           => $data['notes'] ?? null,
        ]);

        return back()->with('ok','Sesi kasir ditutup. Selisih: '.number_format($variance,0,',','.'));
    }
}
