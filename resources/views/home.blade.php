@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="mt-2 text-gray-600">Welcome to your API Pod Dashboard. Here you can manage your API usage and subscriptions.</p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700">API Usage</h2>
            <p class="mt-2 text-gray-600">Monitor your token consumption and track your API calls.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">View Usage &rarr;</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700">Subscriptions</h2>
            <p class="mt-2 text-gray-600">Manage your active plans and explore new subscription options.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Manage Subscriptions &rarr;</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700">Shop</h2>
            <p class="mt-2 text-gray-600">Purchase additional tokens or upgrade your current plan.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Visit Shop &rarr;</a>
        </div>
    </div>
@endsection