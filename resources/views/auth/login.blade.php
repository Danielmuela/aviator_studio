@extends('layouts.app')

@section('title', 'Connexion - Aviatrax Studio')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-black text-white">
    <div class="bg-[#101828] p-8 rounded shadow-md w-full max-w-md">
        <div class="flex justify-center items-center mb-8" style="min-height:320px;">
            <video autoplay loop muted playsinline class="rounded-lg shadow-lg" style="max-width:350px; width:100%; height:auto; display:block; margin:auto; background:#000;">
                <source src="{{ asset('Aviator Music Clip.mp4') }}" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div>
        <div>
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-400">Connexion</h2>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="block text-sm font-medium text-blue-300">Adresse e-mail</label>
                    <input id="email" name="email" type="email" required autofocus class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-blue-300">Mot de passe</label>
                    <input id="password" name="password" type="password" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full bg-orange-400 text-black py-2 rounded-md hover:bg-orange-500">Se connecter</button>
            </div>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500">
                    Pas encore de compte ? S'inscrire
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 