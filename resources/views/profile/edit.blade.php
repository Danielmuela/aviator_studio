@extends('layouts.app')

@section('title', 'Modifier mon profil - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 text-white">
    <h2 class="text-2xl font-bold text-blue-400 mb-6">Modifier mon profil</h2>

    @if(session('success'))
        <div class="mb-4 bg-green-600 text-white px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-[#101828] shadow rounded-lg p-6 space-y-6">
        @csrf
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
            <div>
                <label class="block text-sm font-medium text-blue-300 mb-1">Photo de profil</label>
                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-blue-200 file:bg-blue-900 file:text-blue-200 file:rounded file:border-none file:px-3 file:py-1 file:mr-2"
                    onchange="if(this.files[0] && this.files[0].size > 52428800){ alert('Le fichier dépasse 50MB !'); this.value=''; }">
                <p class="text-xs text-orange-400 mt-1">
                    ⚠️ Veuillez choisir une photo de moins de 50MB. Les fichiers plus volumineux seront refusés par le serveur.
                </p>
                @error('photo')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-blue-300 mb-1">Nom</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('name')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-blue-300 mb-1">Adresse e-mail</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('email')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-blue-300 mb-1">Téléphone</label>
            <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('telephone')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-between">
            <a href="{{ route('profile.show') }}" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-600 font-semibold">Annuler</a>
            <button type="submit" class="bg-orange-400 text-black px-6 py-2 rounded hover:bg-orange-500 font-semibold">Enregistrer</button>
        </div>
    </form>
</div>
@endsection 