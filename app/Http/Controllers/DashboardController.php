<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $recentOrders = Order::latest()->take(5)->get();
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        foreach ($recentOrders as $order) {
            $order->total_items = $order->orderDetails->sum('quantity');
            $order->total_amount = $order->orderDetails->sum(function ($detail) {
                return $detail->product->price * $detail->quantity;
            });
        }

        return view('dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalUsers',
            'recentOrders',
            'lowStockProducts'
        ));
    }
}
