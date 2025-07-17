@extends('layouts.app')

@section('title', 'Dashboard Secrétariat - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Secrétariat</h1>
        <div class="flex space-x-2">
            <a href="{{ route('secretariat.artistes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Nouvel artiste
            </a>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Dashboard principal
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">👥</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total artistes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_artistes'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">✅</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Artistes actifs</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['artistes_actifs'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">⏳</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['artistes_en_attente'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des artistes -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Gestion des artistes</h3>
                <a href="{{ route('secretariat.artistes.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                    + Nouvel artiste
                </a>
            </div>
            
            @if($artistes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artiste</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable studio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($artistes as $artiste)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $artiste->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $artiste->email }}</div>
                                        <div class="text-sm text-gray-500">{{ $artiste->telephone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($artiste->statut === 'valide') bg-green-100 text-green-800
                                            @elseif($artiste->statut === 'suspendu') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($artiste->statut) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $artiste->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($artiste->statut === 'valide')
                                            @if(auth()->user()->isSecretariat() || auth()->user()->isAdmin())
                                                <form action="{{ route('secretariat.artistes.affecter', $artiste) }}" method="POST" class="flex items-center space-x-2">
                                                    @csrf
                                                    <select name="responsable_id" class="rounded border-gray-300 text-sm" onchange="this.form.submit()">
                                                        <option value="">-- Choisir --</option>
                                                        @php
                                                            $responsables = \App\Models\User::whereHas('role', function($q){ $q->where('name', 'responsable_studio'); })->get();
                                                        @endphp
                                                        @foreach($responsables as $responsable)
                                                            <option value="{{ $responsable->id }}" {{ $artiste->responsable_id == $responsable->id ? 'selected' : '' }}>
                                                                {{ $responsable->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if($artiste->responsable)
                                                        <span class="text-xs text-blue-600 ml-2">({{ $artiste->responsable->name }})</span>
                                                    @endif
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Affectation impossible</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('secretariat.artistes.edit', $artiste) }}" 
                                               class="text-blue-600 hover:text-blue-900">Modifier</a>
                                            
                                            @if($artiste->statut === 'valide')
                                                <form action="{{ route('secretariat.artistes.suspend', $artiste) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Suspendre</button>
                                                </form>
                                            @else
                                                <form action="{{ route('secretariat.artistes.activate', $artiste) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Réactiver</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun artiste</h3>
                    <p class="text-gray-500">Commencez par créer votre premier artiste.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('secretariat.artistes.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">➕</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Créer un artiste</h4>
                        <p class="text-sm text-gray-500">Nouveau compte artiste</p>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🏠</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Dashboard principal</h4>
                        <p class="text-sm text-gray-500">Retour au dashboard</p>
                    </div>
                </a>

                <a href="{{ route('projets.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📋</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Voir les projets</h4>
                        <p class="text-sm text-gray-500">Liste des projets</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 