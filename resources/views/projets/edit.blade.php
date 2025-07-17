@extends('layouts.app')

@section('title', 'Modifier le projet - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 text-white">
    <h2 class="text-2xl font-bold text-blue-400 mb-6">Modifier le projet</h2>

    <form action="{{ route('projets.update', $projet) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Titre</label>
            <input type="text" name="titre" value="{{ old('titre', $projet->titre) }}" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('titre')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Description</label>
            <textarea name="description" rows="3" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">{{ old('description', $projet->description) }}</textarea>
            @error('description')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Artiste</label>
            <select name="artiste_id" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                @foreach($artistes as $artiste)
                    <option value="{{ $artiste->id }}" {{ $projet->artiste_id == $artiste->id ? 'selected' : '' }}>{{ $artiste->name }}</option>
                @endforeach
            </select>
            @error('artiste_id')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Date de début</label>
            <input type="date" name="date_debut" value="{{ old('date_debut', optional($projet->date_debut)->format('Y-m-d')) }}" class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('date_debut')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Date de fin prévue</label>
            <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue', optional($projet->date_fin_prevue)->format('Y-m-d')) }}" class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
            @error('date_fin_prevue')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-blue-300 mb-1">Statut</label>
            <select name="statut" required class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                <option value="actif" {{ $projet->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="termine" {{ $projet->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="suspendu" {{ $projet->statut == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
            </select>
            @error('statut')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('projets.show', $projet) }}" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-600 font-semibold">Annuler</a>
            <button type="submit" class="ml-2 bg-orange-400 text-black px-6 py-2 rounded hover:bg-orange-500 font-semibold">Enregistrer</button>
        </div>
    </form>
</div>
@endsection 