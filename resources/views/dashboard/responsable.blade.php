@extends('layouts.app')

@section('title', 'Tableau de bord - Responsable Studio')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Responsable Studio</h1>

    @can('create', App\Models\Projet::class)
        <div class="mb-4">
            <a href="{{ route('projets.create') }}" class="inline-block text-sm font-medium text-indigo-600 hover:underline">+ Nouveau Projet</a>
        </div>
    @endcan

    @if($projets->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projets as $projet)
                @php
                    $color = match($projet->etape) {
                        'Enregistrement' => 'blue',
                        'Mixage' => 'deeppink',
                        'Mastering' => 'gold',
                        'Distribution en cours' => 'orange',
                        'Distribution terminée' => 'green',
                        default => 'black',
                    };
                @endphp

                <div class="bg-white rounded-xl shadow p-5 space-y-3 border hover:shadow-md transition duration-200">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $projet->titre }}</h2>
                    <p class="text-gray-700 text-sm">{{ $projet->description }}</p>

                    <div class="text-sm text-gray-600 space-y-1">
                        <p>🎤 <strong>Artiste :</strong> {{ $projet->artiste->name ?? 'Non assigné' }}</p>
                        <p>📅 <strong>Début :</strong> {{ \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') }}</p>
                        <p>
                            🎧 <strong>Étape :</strong> 
                            <span style="color: {{ $color }};">
                                {{ $projet->etape }}
                            </span>
                        </p>
                    </div>

                    <!-- Formulaire -->
                    <form action="{{ route('projets.updateEtape', $projet) }}" method="POST" class="pt-2 border-t mt-2">
                        @csrf
                        @method('PUT')
                        <div class="flex space-x-3 items-center mt-2">
                            <select name="etape" class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 text-sm">
                                <option value="enregistrement" @selected($projet->etape === 'enregistrement')>Enregistrement</option>
                                <option value="mixage" @selected($projet->etape === 'mixage')>Mixage</option>
                                <option value="mastering" @selected($projet->etape === 'mastering')>Mastering</option>
                                @if(auth()->user()->isAdmin())
                                    <option value="distribution_en_cours" @selected($projet->etape === 'distribution_en_cours')>Distribution en cours</option>
                                    <option value="distribution_terminee" @selected($projet->etape === 'distribution_terminee')>Distribution terminée</option>
                                @endif
                            </select>
                            <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">Aucun projet n'est actuellement assigné.</p>
    @endif
</div>
@endsection
