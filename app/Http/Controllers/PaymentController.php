<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Upload bukti transfer/e-wallet oleh user
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'proof'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes'  => 'nullable|string|max:500',
        ]);

        $path = $request->file('proof')->store('payments','public');

        OrderPayment::create([
            'order_id'     => $order->id,
            'status'       => OrderPayment::ST_PENDING,
            'method'       => $order->payment_method ?: 'transfer',
            'amount'       => $request->amount,
            'proof_path'   => $path,
            'proof_disk'   => 'public',
            'notes'        => $request->notes,
        ]);

        return back()->with('ok','Bukti pembayaran diunggah. Menunggu verifikasi kasir.');
    }
}
