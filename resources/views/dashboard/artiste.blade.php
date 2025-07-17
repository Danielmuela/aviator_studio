@extends('layouts.app')

@section('title', 'Dashboard Artiste - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <!-- Bloc vidéo d'accueil -->
    <div class="bg-gradient-to-r from-pink-100 via-yellow-50 to-green-100 rounded-xl shadow-lg overflow-hidden flex flex-col items-center justify-center p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Bienvenue sur votre espace artiste !</h2>
        <div class="w-full max-w-2xl aspect-video rounded-lg overflow-hidden shadow-lg border-4 border-white">
            <video autoplay loop muted playsinline poster="{{ asset('logo.jpg') }}" class="w-full h-full object-contain rounded-lg bg-black">
                <source src="{{ asset('Vidéo label aviator.mp4') }}" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div>
        <p class="mt-4 text-gray-600 text-center max-w-xl">Découvrez l'univers Aviatrax Studio et lancez-vous dans vos projets musicaux avec passion !</p>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Mes projets</h2>
            
            <!-- Statistiques par étape -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-5 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $projetsParEtape['enregistrement'] }}</div>
                    <div class="text-sm text-blue-500">Enregistrement</div>
                </div>
                <div class="bg-pink-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-pink-600">{{ $projetsParEtape['mixage'] }}</div>
                    <div class="text-sm text-pink-500">Mixage</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $projetsParEtape['mastering'] }}</div>
                    <div class="text-sm text-yellow-500">Mastering</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $projetsParEtape['distribution_en_cours'] }}</div>
                    <div class="text-sm text-orange-500">Distribution</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $projetsParEtape['distribution_terminee'] }}</div>
                    <div class="text-sm text-green-500">Terminé</div>
                </div>
            </div>

            <!-- Liste des projets -->
            @if($projets->count() > 0)
                <div class="space-y-4">
                    @foreach($projets as $projet)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $projet->titre }}</h3>
                                    @if($projet->description)
                                        <p class="text-gray-600 mt-1">{{ Str::limit($projet->description, 100) }}</p>
                                    @endif
                                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                                        <span>Responsable: {{ $projet->responsable->name }}</span>
                                        @if($projet->date_debut)
                                            <span>Début: {{ $projet->date_debut->format('d/m/Y') }}</span>
                                        @endif
                                        @if($projet->date_fin_prevue)
                                            <span>Fin prévue: {{ $projet->date_fin_prevue->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-sm font-medium rounded-full text-white" style="background-color: {{ $projet->etape_color }}">
                                        {{ $projet->etape_label }}
                                    </span>
                                    <a href="{{ route('projets.show', $projet) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-6xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun projet pour le moment</h3>
                    <p class="text-gray-500">Vos projets apparaîtront ici une fois qu'ils seront créés par le studio.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Progression globale -->
    @if($projets->count() > 0)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Progression globale</h3>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    @php
                        $totalProjets = $projets->count();
                        $projetsTermines = $projets->where('etape', 'distribution_terminee')->count();
                        $pourcentage = $totalProjets > 0 ? ($projetsTermines / $totalProjets) * 100 : 0;
                    @endphp
                    <div class="bg-green-600 h-4 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    {{ $projetsTermines }} sur {{ $totalProjets }} projets terminés ({{ number_format($pourcentage, 1) }}%)
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 