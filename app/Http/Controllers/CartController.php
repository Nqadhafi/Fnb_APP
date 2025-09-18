<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Product, TableSession, DiningTable, Order};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected function currentCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId, 'status' => Cart::ST_ACTIVE],
            ['user_id' => $request->user()->id ?? null, 'subtotal'=>0, 'discount_total'=>0, 'grand_total'=>0]
        );
        if ($request->user() && !$cart->user_id) {
            $cart->user_id = $request->user()->id;
            $cart->save();
        }
        return $cart;
    }

    public function index(Request $request)
    {
        $cart = $this->currentCart($request)->load('items');
        return view('public.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'options'    => 'nullable|array',
            'notes'      => 'nullable|string|max:500',
            'table_session_id' => 'nullable|exists:table_sessions,id',
        ]);

        $cart = $this->currentCart($request);

        if (!empty($data['table_session_id'])) {
            $cart->table_session_id = $data['table_session_id'];
            $cart->save();
        }

        $product = Product::findOrFail($data['product_id']);
        abort_unless($product->is_active, 400);

        DB::transaction(function() use ($cart, $product, $data) {
            CartItem::create([
                'cart_id'         => $cart->id,
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'unit_price'      => $product->discount_price ?? $product->price,
                'qty'             => $data['qty'],
                'selected_options'=> $data['options'] ?? null,
                'notes'           => $data['notes'] ?? null,
            ]);

            $subtotal = $cart->items()->sum(DB::raw('unit_price * qty'));
            $cart->update([
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'grand_total' => $subtotal,
            ]);
        });

        return back()->with('ok','Item ditambahkan.');
    }

    public function update(Request $request, CartItem $item)
    {
        $data = $request->validate(['qty' => 'required|integer|min:1']);
        abort_unless($item->cart->session_id === $request->session()->getId(), 403);

        $item->update(['qty' => $data['qty']]);

        $cart = $item->cart;
        $subtotal = $cart->items()->sum(DB::raw('unit_price * qty'));
        $cart->update(['subtotal'=>$subtotal,'grand_total'=>$subtotal]);

        return back()->with('ok','Jumlah diperbarui.');
    }

    public function remove(Request $request, CartItem $item)
    {
        abort_unless($item->cart->session_id === $request->session()->getId(), 403);
        $cart = $item->cart;
        $item->delete();

        $subtotal = $cart->items()->sum(DB::raw('unit_price * qty'));
        $cart->update(['subtotal'=>$subtotal,'grand_total'=>$subtotal]);

        return back()->with('ok','Item dihapus.');
    }

    public function clear(Request $request)
    {
        $cart = $this->currentCart($request);
        $cart->items()->delete();
        $cart->update(['subtotal'=>0,'discount_total'=>0,'grand_total'=>0]);
        return back()->with('ok','Keranjang dikosongkan.');
    }

public function setTable(Request $request)
{
    $data = $request->validate([
        'table_session_id' => 'nullable|exists:table_sessions,id',
        'table_code'       => 'nullable|string|max:20',
    ]);

    $cart = $this->currentCart($request);

    $session = null;
    if (!empty($data['table_session_id'])) {
        $session = TableSession::where('id', $data['table_session_id'])->whereNull('closed_at')->first();
    } elseif (!empty($data['table_code'])) {
        $table = DiningTable::where('code', $data['table_code'])->first();
        if ($table) {
            $session = TableSession::where('dining_table_id', $table->id)->whereNull('closed_at')->first();
        }
    }

    if (!$session) {
        return back()->withErrors(['table' => 'Sesi meja tidak ditemukan/aktif.']);
    }

    $cart->update(['table_session_id' => $session->id]);
    return back()->with('ok','Meja berhasil dipasang: '.$session->table->code);
}

public function claimTable(Request $request)
{
    $data = $request->validate([
        'table_id'    => 'required|exists:dining_tables,id',
        'guest_count' => 'nullable|integer|min:1|max:12',
    ]);

    $user = $request->user();
    $cart = $this->currentCart($request);

    $existing = TableSession::where('opened_by', $user->id)->whereNull('closed_at')->first();
    if ($existing) {
        $cart->update(['table_session_id' => $existing->id]);
        return back()->with('ok','Kamu sudah punya sesi meja aktif: '.$existing->table->code);
    }

    DB::transaction(function() use ($data, $user, $cart) {
        $table = DiningTable::where('id',$data['table_id'])->lockForUpdate()->first();
        if (!$table || $table->status !== 'available') {
            abort(422,'Meja sudah terisi / tidak tersedia.');
        }
        $session = TableSession::create([
            'dining_table_id' => $table->id,
            'opened_by'       => $user->id,
            'guest_count'     => $data['guest_count'] ?? 1,
            'opened_at'       => now(),
        ]);
        $table->update(['status'=>'occupied']);
        $cart->update(['table_session_id' => $session->id]);
    });

    return back()->with('ok','Meja berhasil diambil.');
}
}
