@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

{{-- products form --}}
{{-- <livewire:product-form /> --}}

{{-- dashboard content --}}

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("Welcome to your Perfume Inventory Dashboard!") }}
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Example Widget 1 -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Total Products</h3>
            <p class="mt-1 text-sm text-gray-600">Count: {{-- Fetch and display actual count --}}</p>
        </div>

        <!-- Example Widget 2 -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Low Stock Items</h3>
            <p class="mt-1 text-sm text-gray-600">Count: {{-- Fetch and display actual count --}}</p>
        </div>

        <!-- Example Widget 3 -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            <ul class="mt-2 space-y-2">
                <li class="text-sm text-gray-600">Product X added</li>
                <li class="text-sm text-gray-600">Product Y updated</li>
                <li class="text-sm text-gray-600">Product Z deleted</li>
            </ul>
        </div>


@endsection
