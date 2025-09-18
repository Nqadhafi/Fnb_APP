<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Riwayat pesanan user
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->latest()->paginate(12);
        return view('public.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
        $order->load(['items','payments']);
        return view('public.orders.show', compact('order'));
    }
}
