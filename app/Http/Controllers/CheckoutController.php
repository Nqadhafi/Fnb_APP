<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Order, OrderItem, TableSession, DiningTable};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // pelanggan harus login
    }

    public function show(Request $request)
    {
        $cart = Cart::where('session_id', $request->session()->getId())
            ->where('status', 'active')
            ->with(['items','tableSession.table'])
            ->first();

        // ❗ Jangan 404: arahkan ke keranjang bila kosong
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Keranjang kamu masih kosong. Tambahkan item terlebih dahulu.']);
        }

        $activeTableSessions = TableSession::with('table')
            ->whereNull('closed_at')
            ->orderBy('id','desc')
            ->get();

        $availableTables = DiningTable::where('status','available')
            ->orderBy('code')->get();

        return view('public.checkout', compact('cart','activeTableSessions','availableTables'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'order_type'     => 'required|in:dine_in,takeaway',
            'payment_method' => 'required|in:cash,transfer,e-wallet',
            'notes'          => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Semua operasi kritikal di dalam transaction + lock
        $order = DB::transaction(function () use ($request, $user) {
            // Ambil cart + items dengan FOR UPDATE (di dalam transaksi)
            $cart = Cart::where('session_id', $request->session()->getId())
                ->where('status', 'active')
                ->with('items')
                ->lockForUpdate()
                ->first();

            // ❗ Jangan 400/403: redirect balik ke keranjang kalau kosong
            if (!$cart || $cart->items->isEmpty()) {
                abort(409, 'Keranjang kosong.'); // akan ditangani di catch (lihat bawah)
            }

            // Jika dine-in tanpa meja, kita tetap izinkan (kalau mau wajib meja, validasi di sini)
            // if ($request->order_type === 'dine_in' && !$cart->table_session_id) {
            //     abort(422, 'Silakan pilih/ambil meja terlebih dahulu.');
            // }

            // Hitung total
            $subtotal = $cart->items->sum(fn($i) => $i->unit_price * $i->qty);
            $service  = 0;
            $tax      = 0;
            $grand    = $subtotal + $service + $tax;

            // Buat order
            $order = Order::create([
                'order_type'       => $request->order_type,
                'status'           => Order::ST_PENDING,
                'user_id'          => $user->id,
                'table_session_id' => $cart->table_session_id, // jika self-order di meja
                'cart_id'          => $cart->id,
                'subtotal'         => $subtotal,
                'discount_total'   => 0,
                'service_charge'   => $service,
                'tax_total'        => $tax,
                'grand_total'      => $grand,
                'payment_method'   => $request->payment_method,
                'notes'            => $request->notes,
            ]);

            // Item
            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $ci->product_id,
                    'product_name'     => $ci->product_name,
                    'unit_price'       => $ci->unit_price,
                    'qty'              => $ci->qty,
                    'selected_options' => $ci->selected_options,
                    'notes'            => $ci->notes,
                    'prep_status'      => 'queued',
                    'line_total'       => $ci->unit_price * $ci->qty,
                ]);
            }

            // Tutup cart
            $cart->update(['status' => Cart::ST_CONVERTED]);

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('ok','Pesanan dibuat. Silakan lanjutkan pembayaran di kasir atau upload bukti transfer.');
    }
}
