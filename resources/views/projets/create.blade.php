@extends('layouts.app')

@section('title', 'Créer un projet - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto text-white">
    <div class="bg-[#101828] shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-blue-400 mb-4">Créer un nouveau projet</h2>
            
            <form action="{{ route('projets.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="titre" class="block text-sm font-medium text-blue-300">Titre du projet</label>
                        <input type="text" name="titre" id="titre" required value="{{ old('titre') }}" 
                               class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-blue-300">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="artiste_id" class="block text-sm font-medium text-blue-300">Artiste</label>
                        <select name="artiste_id" id="artiste_id" required 
                                class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                            <option value="">Sélectionner un artiste</option>
                            @foreach($artistes as $artiste)
                                <option value="{{ $artiste->id }}" {{ old('artiste_id') == $artiste->id ? 'selected' : '' }}>
                                    {{ $artiste->name }} ({{ $artiste->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="responsable_id" class="block text-sm font-medium text-blue-300">Responsable studio</label>
                        <select name="responsable_id" id="responsable_id" required 
                                class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                            <option value="">Sélectionner un responsable studio</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}" {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                    {{ $responsable->name }} ({{ $responsable->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-blue-300">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}"
                                   class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                        </div>

                        <div>
                            <label for="date_fin_prevue" class="block text-sm font-medium text-blue-300">Date de fin prévue</label>
                            <input type="date" name="date_fin_prevue" id="date_fin_prevue" value="{{ old('date_fin_prevue') }}"
                                   class="mt-1 block w-full border-blue-900 bg-black text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-orange-400">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('projets.index') }}" 
                           class="bg-blue-900 text-white px-4 py-2 rounded-md hover:bg-blue-800">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="bg-orange-400 text-black px-4 py-2 rounded-md hover:bg-orange-500">
                            Créer le projet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 