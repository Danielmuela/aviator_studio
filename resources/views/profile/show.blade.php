@extends('layouts.app')

@section('title', 'Mon profil - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 text-white">
    <h2 class="text-2xl font-bold text-blue-400 mb-6">Mon profil</h2>

    @if(session('success'))
        <div class="mb-4 bg-green-600 text-white px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-[#101828] shadow rounded-lg p-6 space-y-6">
        <div class="flex flex-col items-center space-y-4">
            <div>
                @if($user->photo_path)
                    <div class="w-28 h-28 rounded-full bg-blue-900 flex items-center justify-center border-4 border-blue-400 shadow overflow-hidden">
                        <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Photo de profil" class="w-full h-full object-cover object-center" loading="lazy">
                    </div>
                @else
                    <div class="w-28 h-28 rounded-full bg-blue-900 flex items-center justify-center border-4 border-blue-400 shadow">
                        <span class="text-4xl text-blue-200 w-full text-center flex items-center justify-center h-full">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div>
            <span class="block text-sm font-medium text-blue-300 mb-1">Nom</span>
            <span class="block text-lg text-white">{{ $user->name }}</span>
        </div>
        <div>
            <span class="block text-sm font-medium text-blue-300 mb-1">Adresse e-mail</span>
            <span class="block text-lg text-white">{{ $user->email }}</span>
        </div>
        <div>
            <span class="block text-sm font-medium text-blue-300 mb-1">Téléphone</span>
            <span class="block text-lg text-white">{{ $user->telephone ?? '-' }}</span>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('profile.edit') }}" class="bg-orange-400 text-black px-6 py-2 rounded hover:bg-orange-500 font-semibold">Modifier le profil</a>
        </div>
    </div>
</div>
@endsection 