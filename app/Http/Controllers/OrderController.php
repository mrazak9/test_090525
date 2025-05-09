<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Query orders with filter
        $orders = Order::all();
        foreach ($orders as $order) {
            $order->total_items = $order->orderDetails->sum('quantity');
            $order->total_amount = $order->orderDetails->sum(function ($detail) {
                return $detail->product->price * $detail->quantity;
            });
        }
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $selectedProduct = null;
        $cart = [];

        if (request('product_id')) {
            $selectedProduct = Product::find(request('product_id'));

            if ($selectedProduct) {
                $cart[] = $selectedProduct; // Tambahkan produk terpilih ke dalam keranjang sementara
            }
        }

        return view('orders.create', compact('products', 'selectedProduct', 'cart'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $products = Product::whereIn('id', array_column($request->products, 'id'))->get()->keyBy('id');

            // Cek stok dan hitung total
            foreach ($request->products as $item) {
                $product = $products->get($item['id']);
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->produk} tidak mencukupi! Tersedia: {$product->stock}");
                }
                $total += $product->price * $item['quantity'];
            }

            // Buat order
            $order = Order::create([]);

            // Buat order_number setelah order_id dapat
            $order->order_number = $order->id . '-' . date('Ymd');
            $order->save();

            // Simpan detail order + update stok
            foreach ($request->products as $item) {
                $product = $products->get($item['id']);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function show(Order $order)
    {
        $order->load('orderDetails.product');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('orderDetails.product');
        $products = Product::where('stock', '>', 0)->get();
        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;

            // Return stock to products first
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                $product->stock += $detail->quantity;
                $product->save();
            }

            // Validate stock availability for new order
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error', "Stok {$product->produk} tidak mencukupi! Tersedia: {$product->stock}");
                }

                $total += $product->price * $item['quantity'];
            }

            // Update order
            $order->update([
                'total_amount' => $total
            ]);

            // Remove old order details
            $order->orderDetails()->delete();

            // Create new order details and reduce stock
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);

                // Reduce stock
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        DB::beginTransaction();

        try {
            // Return stock to products
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                $product->stock += $detail->quantity;
                $product->save();
            }

            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil dihapus dan stok telah dikembalikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function destroyDetail($id)
    {
        $detail = OrderDetail::findOrFail($id);

        $detail->delete();

        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $detail = OrderDetail::findOrFail($id);

        $detail->quantity = $request->quantity;
        $detail->save();

        return back()->with('success', 'Quantity berhasil diupdate.');
    }
}
