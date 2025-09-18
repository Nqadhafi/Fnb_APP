<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\DiningTable;

class DashboardController extends Controller
{
    public function __construct() { $this->middleware('admin'); }

    public function index()
    {
        $tables = DiningTable::orderBy('code')->get();
        $metrics = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today'=> Order::whereDate('created_at', today())->sum('grand_total'),
            'products'     => Product::count(),
            'open_orders'  => Order::whereIn('status',['open','pending','paid','preparing','ready'])->count(),
        ];
        $latestOrders = Order::latest()->take(10)->get();
        return view('admin.dashboard', compact('metrics','latestOrders','tables'));
    }
}
