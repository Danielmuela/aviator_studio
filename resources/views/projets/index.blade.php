@extends('layouts.app')

@section('title', 'Projets - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-400">Projets musicaux</h1>
        @can('create', App\Models\Projet::class)
            @if(auth()->user()->isAdmin() || auth()->user()->isResponsableStudio() || auth()->user()->isSecretariat())
                <a href="{{ route('projets.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Nouveau projet
                </a>
            @endif
        @endcan
    </div>

    @if($projets->count() > 0)
        <div class="bg-[#101828] shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-blue-900">
                @foreach($projets as $projet)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-orange-400">🎵</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-blue-300 truncate">
                                                <a href="{{ route('projets.show', $projet) }}" class="hover:underline text-blue-400">
                                                    {{ $projet->titre }}
                                                </a>
                                            </p>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full text-black bg-orange-400" style="background-color: {{ $projet->etape_color }}">
                                                {{ $projet->etape_label }}
                                            </span>
                                        </div>
                                        <div class="mt-1 flex items-center text-sm text-blue-200">
                                            <span>Artiste: {{ $projet->artiste->name }}</span>
                                            @if(auth()->user()->isAdmin() || auth()->user()->isResponsableStudio() || auth()->user()->isSecretariat())
                                                <span class="mx-2">•</span>
                                                <span>Responsable: {{ $projet->responsable->name }}</span>
                                            @endif
                                        </div>
                                        @if($projet->description)
                                            <p class="mt-1 text-sm text-white">{{ Str::limit($projet->description, 100) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($projet->date_debut)
                                        <span class="text-sm text-blue-200">Début: {{ $projet->date_debut->format('d/m/Y') }}</span>
                                    @endif
                                    @if($projet->date_fin_prevue)
                                        <span class="text-sm text-blue-200">Fin: {{ $projet->date_fin_prevue->format('d/m/Y') }}</span>
                                    @endif
                                    @can('update', $projet)
                                        @if(auth()->user()->isAdmin() || auth()->user()->isResponsableStudio() || auth()->user()->isSecretariat())
                                            <a href="{{ route('projets.edit', $projet) }}" class="text-orange-400 hover:text-orange-600 text-sm">
                                                Modifier
                                            </a>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-blue-900 text-6xl mb-4">🎵</div>
            <h3 class="text-lg font-medium text-blue-400 mb-2">Aucun projet trouvé</h3>
            <p class="text-blue-200">
                @can('create', App\Models\Projet::class)
                    Commencez par créer votre premier projet.
                @else
                    Aucun projet n'a encore été créé pour vous.
                @endcan
            </p>
        </div>
    @endif
</div>
@endsection 