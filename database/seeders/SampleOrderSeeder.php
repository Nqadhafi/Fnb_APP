<?php

namespace Database\Seeders;

use App\Models\{
    Cart, CartItem,
    Category, DiningTable, TableSession,
    Order, OrderItem, OrderPayment, PosSession,
    Product, User
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email','admin@example.com')->first();
        $user  = User::where('email','user@example.com')->first();

        // Buka sesi kasir demo (POS)
        $pos = PosSession::firstOrCreate(
            ['opened_by' => $admin?->id, 'closed_at' => null],
            ['counter_name' => 'Front Cashier', 'opening_float' => 200000]
        );

        // Buka sesi meja T01
        $table = DiningTable::where('code','T01')->first();
        $sess  = TableSession::firstOrCreate(
            ['dining_table_id' => $table->id, 'closed_at' => null],
            ['opened_by' => $admin?->id, 'guest_count' => 2]
        );
        $table->update(['status' => 'occupied']);

        // Ambil beberapa produk
        $espresso = Product::where('slug','espresso')->first();
        $fries    = Product::where('slug','french-fries')->first();
        $matcha   = Product::where('slug','matcha-latte')->first();

        // --- Order #1: dine-in, paid CASH (POS) ---
        DB::transaction(function () use ($user, $sess, $pos, $espresso, $fries) {
            $subtotal = ($espresso->final_price * 2) + ($fries->final_price * 1);
            $order = Order::create([
                'order_type'      => Order::TYPE_DINEIN,
                'status'          => Order::ST_PAID,
                'user_id'         => $user?->id,
                'table_session_id'=> $sess->id,
                'subtotal'        => $subtotal,
                'discount_total'  => 0,
                'service_charge'  => 0,
                'tax_total'       => 0,
                'grand_total'     => $subtotal,
                'payment_method'  => 'cash',
                'paid_at'         => now(),
                'pos_session_id'  => $pos->id,
                'notes'           => 'Sample order cash',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $espresso->id,
                'product_name' => $espresso->name,
                'unit_price' => $espresso->final_price,
                'qty' => 2,
                'selected_options' => null,
                'notes' => null,
                'prep_status' => 'queued',
                'line_total' => $espresso->final_price * 2,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $fries->id,
                'product_name' => $fries->name,
                'unit_price' => $fries->final_price,
                'qty' => 1,
                'selected_options' => null,
                'notes' => 'extra sauce',
                'prep_status' => 'queued',
                'line_total' => $fries->final_price * 1,
            ]);

            OrderPayment::create([
                'order_id'      => $order->id,
                'status'        => OrderPayment::ST_VERIFIED,
                'method'        => 'cash',
                'amount'        => $order->grand_total,
                'paid_at'       => now(),
                'cash_received' => $order->grand_total,
                'change_given'  => 0,
                'verified_by'   => $pos->opened_by,
                'verified_at'   => now(),
            ]);
        });

        // --- Order #2: dine-in, TRANSFER (pending proof) ---
        DB::transaction(function () use ($user, $sess, $matcha) {
            $subtotal = $matcha->final_price * 2;
            $order = Order::create([
                'order_type'      => Order::TYPE_DINEIN,
                'status'          => Order::ST_PENDING,
                'user_id'         => $user?->id,
                'table_session_id'=> $sess->id,
                'subtotal'        => $subtotal,
                'discount_total'  => 0,
                'service_charge'  => 0,
                'tax_total'       => 0,
                'grand_total'     => $subtotal,
                'payment_method'  => 'transfer',
                'notes'           => 'Sample order transfer (menunggu upload bukti)',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $matcha->id,
                'product_name' => $matcha->name,
                'unit_price' => $matcha->final_price,
                'qty' => 2,
                'selected_options' => null,
                'notes' => null,
                'prep_status' => 'queued',
                'line_total' => $matcha->final_price * 2,
            ]);

            // payment record akan dibuat user saat upload bukti (PaymentController@uploadProof)
        });

        // --- (Opsional) contoh cart aktif untuk user (biar halaman cart tidak kosong total) ---
        $cart = Cart::firstOrCreate(
            ['user_id' => $user?->id, 'status' => 'active'],
            ['session_id' => 'seed-'.uniqid(), 'table_session_id' => $sess->id, 'subtotal'=>0,'discount_total'=>0,'grand_total'=>0]
        );

        if ($espresso) {
            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $espresso->id],
                ['product_name'=>$espresso->name, 'unit_price'=>$espresso->final_price, 'qty'=>1, 'selected_options'=>null, 'notes'=>null]
            );
            $cart->update([
                'subtotal' => $cart->items()->sum(DB::raw('unit_price * qty')),
                'discount_total' => 0,
                'grand_total' => $cart->items()->sum(DB::raw('unit_price * qty')),
            ]);
        }
    }
}
