@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Order</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <strong>No Transaksi:</strong> {{ $order->order_number }} <br>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d-m-Y') }}
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Quantity</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderDetails as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->product->produk }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>Rp {{ number_format($detail->product->price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $detail->id }}"
                                            data-quantity="{{ $detail->quantity }}" title="Edit"
                                            style="width: 36px; height: 36px;">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('orderdetails.destroy', $detail->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus item ini?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center"
                                                title="Hapus" style="width: 36px; height: 36px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>


                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var quantity = button.getAttribute('data-quantity');

            var modal = this;
            modal.querySelector('#quantity').value = quantity;

            var form = modal.querySelector('#editForm');
            form.action = '/order-details/' + id;
        });
    </script>
@endpush

<!-- Modal Edit Quantity -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" min="1" class="form-control" id="quantity" name="quantity"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
