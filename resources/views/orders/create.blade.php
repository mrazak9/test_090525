@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Buat Order Baru</h1>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">

                        <div class="badge badge-stock bg-{{ $product->stock > 10 ? 'success' : 'warning' }}">
                            Stock: {{ $product->stock }}
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $product->produk }}</h5>
                            <p class="card-text text-muted">
                                {{ Str::limit($product->description, 50) }}
                            </p>
                            <h6 class="text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</h6>
                        </div>

                        <div class="card-footer bg-white">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-primary">Detail</a>
                            @auth
                                <button type="button" class="btn btn-sm btn-success"
                                    onclick="addToOrder({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})">
                                    <i class="fas fa-shopping-cart me-1"></i> Beli
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login untuk Beli</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Tidak ada produk yang sesuai dengan pencarian Anda.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-body">
                <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <div class="alert alert-info" id="emptyProductAlert">
                            <i class="fas fa-info-circle me-2"></i> Belum ada produk dipilih. Silakan tambahkan produk ke
                            order.
                        </div>

                        <div class="table-responsive" id="productTableContainer" style="display: none;">
                            <table class="table table-bordered" id="productTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th style="width: 150px;">Jumlah</th>
                                        <th>Subtotal</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <!-- Produk akan ditambahkan secara dinamis di sini -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td colspan="2" class="fw-bold" id="totalAmount">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2"
                            onclick="window.location.href='{{ route('orders.index') }}'">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-save me-1"></i> Simpan Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productTableBody = document.getElementById('productTableBody');
            const productTableContainer = document.getElementById('productTableContainer');
            const emptyProductAlert = document.getElementById('emptyProductAlert');
            const totalAmount = document.getElementById('totalAmount');
            const submitBtn = document.getElementById('submitBtn');

            let productsInOrder = {}; // id => { id, name, price, quantity, stock }

            window.addToOrder = function(id, name, price, stock) {
                if (!productsInOrder[id]) {
                    productsInOrder[id] = {
                        id: id,
                        name: name,
                        price: price,
                        quantity: 0,
                        stock: stock
                    };
                }

                if (productsInOrder[id].quantity < stock) {
                    productsInOrder[id].quantity += 1;
                } else {
                    alert('Stok produk tidak mencukupi!');
                    return;
                }

                renderTable();
            }

            function renderTable() {
                productTableBody.innerHTML = '';

                let total = 0;
                for (let id in productsInOrder) {
                    const item = productsInOrder[id];
                    const subtotal = item.price * item.quantity;
                    total += subtotal;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>
                    ${item.name}
                    <input type="hidden" name="products[${id}][id]" value="${item.id}">
                </td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="products[${id}][quantity]" value="${item.quantity}" min="1" max="${item.stock}" onchange="updateQuantity(${id}, this.value)">
                </td>
                <td>Rp ${(subtotal).toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
                    productTableBody.appendChild(row);
                }

                totalAmount.textContent = 'Rp ' + total.toLocaleString('id-ID');

                productTableContainer.style.display = Object.keys(productsInOrder).length ? 'block' : 'none';
                emptyProductAlert.style.display = Object.keys(productsInOrder).length ? 'none' : 'block';
                submitBtn.disabled = Object.keys(productsInOrder).length === 0;
            }

            window.removeProduct = function(id) {
                delete productsInOrder[id];
                renderTable();
            }

            window.updateQuantity = function(id, qty) {
                qty = parseInt(qty);
                if (qty > productsInOrder[id].stock) {
                    alert('Jumlah melebihi stok!');
                    qty = productsInOrder[id].stock;
                }
                if (qty <= 0) {
                    removeProduct(id);
                } else {
                    productsInOrder[id].quantity = qty;
                }
                renderTable();
            }
        });
    </script>
@endpush
