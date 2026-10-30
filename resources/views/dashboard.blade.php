@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-2">Dashboard</h1>
    <p class="text-sm text-gray-600 mb-6">
        Halo, <span class="font-medium">{{ auth()->user()->name }}</span>
        ({{ auth()->user()->role }})
    </p>

    <div class="bg-white rounded border border-gray-200 p-6 text-sm text-gray-500">
        Isi dashboard (statistik + grafik) menyusul di tahap berikutnya.
    </div>
@endsection
