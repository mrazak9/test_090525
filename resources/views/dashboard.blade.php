@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Produk</h5>
                        <p class="card-text display-4">{{ $totalProducts }}</p>
                        <a href="{{ route('products.index') }}" class="text-white">Lihat detail <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Order</h5>
                        <p class="card-text display-4">{{ $totalOrders }}</p>
                        <a href="{{ route('orders.index') }}" class="text-white">Lihat detail <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengguna</h5>
                        <p class="card-text display-4">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">Produk Stok Menipis</h5>
                    </div>
                    <div class="card-body">
                        @if ($lowStockProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Stok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProducts as $product)
                                            <tr>
                                                <td>{{ $product->produk }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $product->stock <= 5 ? 'danger' : 'warning' }}">
                                                        {{ $product->stock }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('products.edit', $product) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Tidak ada produk dengan stok menipis.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Order Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Order</th>
                                            <th>tgl. Order</th>
                                            <th>Jml. Item</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('orders.show', $order) }}">
                                                        {{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                                <td>{{ $order->total_items }}</td>
                                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Belum ada order.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
