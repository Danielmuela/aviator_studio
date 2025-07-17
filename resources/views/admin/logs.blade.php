@extends('layouts.app')

@section('title', 'Logs - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-blue-400 mb-6">Logs d'activité</h2>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Retour au dashboard
        </a>
    </div>

    <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Historique des actions</h3>
            
            @if(count($logs) > 0)
                <div class="rounded-lg overflow-x-auto border border-blue-900">
                    <table class="min-w-full text-sm font-mono">
                        <thead>
                            <tr>
                                <th class="bg-blue-900 text-blue-200 px-4 py-2 text-left">Horodatage</th>
                                <th class="bg-blue-900 text-blue-200 px-4 py-2 text-left">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($logs as $log)
                            @php
                                // Extraction simple de l'horodatage (si format standard Laravel)
                                preg_match('/^\[(.*?)\]/', $log, $matches);
                                $timestamp = $matches[1] ?? '';
                                $message = $timestamp ? trim(str_replace("[$timestamp]", '', $log)) : $log;
                            @endphp
                            <tr class="@if($loop->even) bg-[#101828] @else bg-[#18213a] @endif hover:bg-blue-950 transition">
                                <td class="px-4 py-2 text-blue-300 w-48 align-top">{{ $timestamp }}</td>
                                <td class="px-4 py-2 text-green-200 align-top">{{ $message }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    <p>Fichier : {{ storage_path('logs/laravel.log') }}</p>
                    <p>Dernière mise à jour : {{ file_exists(storage_path('logs/laravel.log')) ? date('d/m/Y H:i:s', filemtime(storage_path('logs/laravel.log'))) : 'Fichier non trouvé' }}</p>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-blue-900 text-4xl mb-4">📋</div>
                    <h3 class="text-lg font-medium text-blue-400 mb-2">Aucun log disponible</h3>
                    <p class="text-blue-200">Les logs apparaîtront ici une fois que des actions auront été effectuées dans l'application.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistiques des logs -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📊</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total des lignes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ count($logs) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">✅</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Connexions</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ count(array_filter($logs, function($log) { return strpos($log, 'Connexion utilisateur') !== false; })) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🎵</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Projets créés</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ count(array_filter($logs, function($log) { return strpos($log, 'Nouveau projet créé') !== false; })) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-[#101828] shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Actions</h3>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Dashboard Admin
                </a>
                <a href="{{ route('admin.users') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Gérer les utilisateurs
                </a>
                <a href="{{ route('projets.index') }}" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                    Voir les projets
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 