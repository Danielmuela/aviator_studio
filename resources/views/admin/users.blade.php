@extends('layouts.app')

@section('title', 'Gestion des utilisateurs - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <h2 class="text-2xl font-bold text-blue-400 mb-6">Gestion des utilisateurs</h2>

    <div class="bg-[#101828] shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Utilisateurs</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-900">
                    <thead class="bg-blue-900">
                        <tr>
                            <th class="px-6 py-3 bg-blue-900 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#101828] divide-y divide-blue-900">
                        @foreach($users as $user)
                            <tr class="hover:bg-blue-950 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->photo_path)
                                                <div class="h-10 w-10 rounded-full bg-blue-900 flex items-center justify-center border-2 border-blue-400 shadow overflow-hidden">
                                                    <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Photo de profil" class="w-full h-full object-cover object-center" loading="lazy">
                                                </div>
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-blue-900 flex items-center justify-center border-2 border-blue-400 shadow">
                                                    <span class="text-sm font-medium text-blue-200">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                            <div class="text-sm text-blue-200">{{ $user->email }}</div>
                                            <div class="text-sm text-blue-300">{{ $user->telephone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline">
                                        @csrf
                                        <select name="role_id" onchange="if(confirm('Es-tu sûr de vouloir attribuer ce rôle à cet utilisateur ?')){ this.form.submit(); } else { this.value = this.getAttribute('data-current'); }" 
                                                class="text-sm border-blue-900 bg-black text-white rounded-md focus:ring-blue-400 focus:border-orange-400"
                                                data-current="{{ $user->role_id }}">
                                            <option value="">Aucun rôle</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->statut === 'valide')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-600 text-white">
                                            Validé
                                        </span>
                                    @elseif($user->statut === 'en_attente_validation')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-400 text-black">
                                            En attente
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-white">
                                            Suspendu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($user->statut === 'en_attente_validation')
                                            <form method="POST" action="{{ route('admin.users.validate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-400 hover:text-green-200">
                                                    Valider
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($user->statut === 'suspendu')
                                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-400 hover:text-blue-200">
                                                    Réactiver
                                                </button>
                                            </form>
                                        @elseif($user->statut === 'valide')
                                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-orange-400 hover:text-orange-200">
                                                    Suspendre
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">👥</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-200 truncate">Total utilisateurs</dt>
                            <dd class="text-lg font-medium text-white">{{ $users->count() }}</dd>
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
                            <dt class="text-sm font-medium text-blue-200 truncate">Validés</dt>
                            <dd class="text-lg font-medium text-white">{{ $users->where('statut', 'valide')->count() }}</dd>
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
                            <span class="text-white text-sm font-medium">⏳</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-200 truncate">En attente</dt>
                            <dd class="text-lg font-medium text-white">{{ $users->where('statut', 'en_attente_validation')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🚫</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-200 truncate">Suspendus</dt>
                            <dd class="text-lg font-medium text-white">{{ $users->where('statut', 'suspendu')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 