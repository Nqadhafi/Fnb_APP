<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function sales(Request $request)
    {
        $range = $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date',
        ]);

        $q = Order::query()->whereIn('status',['paid','served','completed']);
        if (!empty($range['from'])) $q->whereDate('created_at','>=',$range['from']);
        if (!empty($range['to']))   $q->whereDate('created_at','<=',$range['to']);

        $rows = $q->orderByDesc('id')->paginate(50)->withQueryString();
        $summary = [
            'count' => (clone $q)->count(),
            'total' => (clone $q)->sum('grand_total'),
        ];

        return view('admin.reports.sales', compact('rows','summary'));
    }

    // placeholder untuk print/export (nanti view-nya format cetak)
public function salesPrint(Request $request)
    {
        // gunakan filter yang sama
        $from = $request->query('from');
        $to   = $request->query('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfDay();
        $toDate   = $to   ? Carbon::parse($to)->endOfDay()   : now()->endOfDay();

        $status  = $request->query('status');
        $method  = $request->query('payment_method');
        $otype   = $request->query('order_type');

        $q = Order::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at'); // urut lama->baru untuk cetak

        if ($status)  $q->where('status', $status);
        if ($method)  $q->where('payment_method', $method);
        if ($otype)   $q->where('order_type', $otype);

        $rows = $q->get();

        $summary = [
            'count' => $rows->count(),
            'total' => (float) $rows->sum('grand_total'),
        ];

        return view('admin.reports.sales_print', compact('rows','summary','from','to','status','method','otype'));
    }
}
