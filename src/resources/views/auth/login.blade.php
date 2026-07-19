@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center" style="background-color: #d4d4d4 ; background-image: linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 100px 100px, 100px 100px;">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" class="h-16 w-auto mb-2 rounded-md">
            <h2 class="text-2xl font-bold text-gray-900">Login</h2>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-navy-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Login
                </button>
            </div>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Daftar disini</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
