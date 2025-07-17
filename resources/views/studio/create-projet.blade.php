@extends('layouts.app')

@section('title', 'Créer un projet - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Créer un nouveau projet</h1>
        <a href="{{ route('studio.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('studio.projets.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="titre" class="block text-sm font-medium text-gray-700">Titre du projet</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="artiste_id" class="block text-sm font-medium text-gray-700">Artiste</label>
                        <select name="artiste_id" id="artiste_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un artiste</option>
                            @foreach($artistes as $artiste)
                                <option value="{{ $artiste->id }}" {{ old('artiste_id') == $artiste->id ? 'selected' : '' }}>
                                    {{ $artiste->name }} ({{ $artiste->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('artiste_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('date_debut')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_fin_prevue" class="block text-sm font-medium text-gray-700">Date de fin prévue</label>
                            <input type="date" name="date_fin_prevue" id="date_fin_prevue" value="{{ old('date_fin_prevue') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('date_fin_prevue')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Étapes de production</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Le projet sera créé avec l'étape "Enregistrement" (bleu) par défaut. Vous pourrez ensuite mettre à jour les étapes suivantes :</p>
                                    <ul class="mt-2 list-disc list-inside">
                                        <li>Mixage (rose)</li>
                                        <li>Mastering (jaune)</li>
                                        <li>Distribution en cours (orange) - Admin uniquement</li>
                                        <li>Distribution terminée (vert) - Admin uniquement</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('studio.dashboard') }}" 
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Créer le projet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 