@extends('layouts.app')

@section('title', 'Dashboard Admin - Aviatrax Studio')

@section('content')
<h1 class="text-3xl font-bold text-blue-400 mb-6">Dashboard Administrateur</h1>
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-orange-400 mb-4">Statistiques générales</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_users'] }}</div>
                    <div class="text-sm text-blue-500">Utilisateurs total</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['users_en_attente'] }}</div>
                    <div class="text-sm text-yellow-500">En attente de validation</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['total_projets'] }}</div>
                    <div class="text-sm text-green-500">Projets total</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['projets_actifs'] }}</div>
                    <div class="text-sm text-purple-500">Projets actifs</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Utilisateurs en attente -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-blue-300 mb-4">Utilisateurs en attente de validation</h3>
                @if($usersEnAttente->count() > 0)
                    <div class="space-y-3">
                        @foreach($usersEnAttente as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->telephone }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.users.validate', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                        Valider
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun utilisateur en attente de validation</p>
                @endif
            </div>
        </div>

        <!-- Projets récents -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-blue-300 mb-4">Projets récents</h3>
                @if($projetsRecents->count() > 0)
                    <div class="space-y-3">
                        @foreach($projetsRecents as $projet)
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium">{{ $projet->titre }}</div>
                                        <div class="text-sm text-gray-500">{{ $projet->artiste->name }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded text-white" style="background-color: {{ $projet->etape_color }}">
                                        {{ $projet->etape_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun projet récent</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Actions rapides</h3>
            <div class="flex space-x-4">
                <a href="{{ route('admin.users') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Gérer les utilisateurs
                </a>
                <a href="{{ route('projets.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Créer un projet
                </a>
                <a href="{{ route('admin.logs') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Voir les logs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 