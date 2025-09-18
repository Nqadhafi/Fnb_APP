<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderItem, OrderPayment, PosSession};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(){ $this->middleware('admin'); }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = Order::with(['items','tableSession.table'])->latest();
        if ($status) $q->where('status',$status);
        $orders = $q->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders','status'));
    }

public function show(Order $order)
{
    $order->load(['items','payments','tableSession.table']);

    $openSessions = PosSession::whereNull('closed_at')
        ->orderByDesc('id')
        ->get();

    return view('admin.orders.show', compact('order','openSessions'));
}


    // Update status dapur / order
public function updateStatus(Request $request, \App\Models\Order $order)
{
    $data = $request->validate([
        'status' => 'required|in:open,pending,paid,preparing,ready,served,completed,cancelled',
    ]);

    $old = $order->status;
    $order->update(['status' => $data['status']]);

    if (in_array($data['status'], ['completed','cancelled'], true)) {
        $this->closeTableSessionIfNoActiveOrders($order);
    }

    return back()->with('ok', "Status diubah: {$old} → {$order->status}");
}


    public function updateItemStatus(Request $request, OrderItem $item)
    {
        $data = $request->validate(['prep_status' => 'required|in:queued,preparing,ready,served,void']);
        $item->update(['prep_status' => $data['prep_status']]);
        return back()->with('ok','Status item diperbarui.');
    }

    // Pembayaran tunai di POS (hitung kembalian)
public function payCash(Request $request, Order $order)
{
    $data = $request->validate([
        'cash_received'  => 'required|numeric|min:0',
        'pos_session_id' => 'nullable|integer|exists:pos_sessions,id',
    ]);

    // Tentukan POS session yang dipakai
    $posSessionId = $data['pos_session_id'] ?? null;

    if ($posSessionId) {
        // Jika dipilih, pastikan masih open
        $ps = \App\Models\PosSession::find($posSessionId);
        if ($ps && $ps->closed_at) {
            return back()->withErrors(['pos_session_id' => 'Sesi kasir sudah ditutup, pilih sesi lain.']);
        }
    } else {
        // Jika tidak dipilih, dan hanya ada SATU sesi open ⇒ pakai itu otomatis
        $open = \App\Models\PosSession::whereNull('closed_at')->orderByDesc('id')->limit(2)->get(['id']);
        if ($open->count() == 1) {
            $posSessionId = $open->first()->id;
        }
    }

    $due    = $order->grand_total;
    $change = max(0, $data['cash_received'] - $due);

    DB::transaction(function () use ($order, $data, $due, $change, $request, $posSessionId) {
        \App\Models\OrderPayment::create([
            'order_id'      => $order->id,
            'status'        => \App\Models\OrderPayment::ST_VERIFIED,
            'method'        => 'cash',
            'amount'        => $due,
            'paid_at'       => now(),
            'cash_received' => $data['cash_received'],
            'change_given'  => $change,
            'verified_by'   => $request->user()->id,
            'verified_at'   => now(),
        ]);

        $order->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => 'cash',
            'pos_session_id' => $posSessionId ?? $order->pos_session_id,
        ]);

        // Auto-close sesi meja jika dine-in & tidak ada order lain yang masih aktif di sesi ini
        // if ($order->order_type === 'dine_in' && $order->table_session_id) {
        //     $sid = $order->table_session_id;

        //     $hasOpen = \App\Models\Order::where('table_session_id', $sid)
        //         ->whereNotIn('status', ['completed', 'cancelled'])
        //         ->where('id', '<>', $order->id)
        //         ->exists();

        //     if (! $hasOpen) {
        //         $session = \App\Models\TableSession::with('table')->find($sid);
        //         if ($session && ! $session->closed_at) {
        //             $session->update(['closed_at' => now()]);
        //             if ($session->table) {
        //                 $session->table->update(['status' => 'available']);
        //             }
        //         }
        //     }
        // }
    });

    return back()->with('ok', 'Pembayaran tunai berhasil. Kembalian: ' . number_format($change, 0, ',', '.'));
}


private function closeTableSessionIfNoActiveOrders(\App\Models\Order $order): void
{
    if ($order->order_type !== 'dine_in' || ! $order->table_session_id) {
        return;
    }

    $sid = $order->table_session_id;

    $hasActive = \App\Models\Order::where('table_session_id', $sid)
        ->whereNotIn('status', ['completed','cancelled'])
        ->exists();

    if (! $hasActive) {
        $session = \App\Models\TableSession::with('table')->find($sid);
        if ($session && ! $session->closed_at) {
            $session->update(['closed_at' => now()]);
            if ($session->table) {
                $session->table->update(['status' => 'available']);
            }
        }
    }
}


    // Verifikasi bukti transfer/e-wallet
    public function verifyPayment(Request $request, OrderPayment $payment)
    {
        $request->validate(['approve' => 'required|boolean']);
        $approve = $request->boolean('approve');

        DB::transaction(function() use ($payment, $approve, $request) {
            $payment->update([
                'status'      => $approve ? OrderPayment::ST_VERIFIED : OrderPayment::ST_REJECTED,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            if ($approve) {
                $order = $payment->order;
                $order->update([
                    'status'         => 'paid',
                    'paid_at'        => now(),
                    'payment_method' => $payment->method ?? 'transfer',
                ]);
            }
        });

        return back()->with('ok', $approve ? 'Pembayaran diverifikasi.' : 'Pembayaran ditolak.');
    }

    // Cetak struk sederhana (view Blade akan format struk)
    public function printReceipt(Order $order)
    {
        $order->load(['items','payments','tableSession.table']);
        return view('admin.orders.receipt', compact('order'));
    }
}
