@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="jumbotron bg-light p-5 rounded mb-4">
            <h1 class="display-4">Selamat Datang di INABA E-Commerce</h1>
            <p class="lead">Temukan berbagai produk berkualitas dengan harga terbaik.</p>
            <hr class="my-4">
            <p>Mulai belanja sekarang dan dapatkan pengalaman berbelanja yang menyenangkan!</p>
            <a class="btn btn-primary btn-lg" href="{{ route('orders.index') }}" role="button">Belanja Sekarang</a>
        </div>
    </div>
@endsection
