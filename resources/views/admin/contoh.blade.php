@extends('layouts.app')

@section('title', 'Admin Contoh')

@section('content')
    <h1 class="text-xl font-semibold mb-2">Halaman Admin</h1>
    <p class="text-sm text-gray-600">
        Halaman ini hanya bisa dibuka oleh role admin (dijaga middleware <code>role:admin</code>).
    </p>
@endsection
