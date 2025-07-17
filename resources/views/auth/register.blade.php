@extends('layouts.app')


@section('title', 'Inscription - Aviatrax Studio')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-black text-white">
    <div class="bg-[#101828] p-8 rounded shadow-md w-full max-w-md">
        <div>
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-400">Inscription</h2>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="block text-sm font-medium text-blue-300">Nom</label>
                    <input id="name" name="name" type="text" required autofocus class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-blue-300">Adresse e-mail</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
                <div>
                    <label for="telephone" class="sr-only">Téléphone</label>
                    <input id="telephone" name="telephone" type="tel" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Téléphone" value="{{ old('telephone') }}">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-blue-300">Mot de passe</label>
                    <input id="password" name="password" type="password" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-blue-300">Confirmer le mot de passe</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full bg-orange-400 text-black py-2 rounded-md hover:bg-orange-500">S'inscrire</button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">
                    Déjà un compte ? Se connecter
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 