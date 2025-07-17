@extends('layouts.app')

@section('title', 'Tableau de bord - Responsable Studio')

@section('content')
<div class="space-y-6 text-white">
    <h1 class="text-2xl font-bold text-blue-400">Tableau de bord - Responsable Studio</h1>

    <!-- Projets assignés -->
    <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Responsables</h3>

            @if($projets->count())
                <div class="space-y-4">
                    @foreach($projets as $projet)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h5><strong>{{ $projet->titre }}</strong></h5>
                            <p>Artiste : {{ $projet->artiste?->name ?? 'Non assigné' }}</p>
                            <p>
                                Étape : 
                                <span style="color: {{ $projet->etape_color }};">
                                    {{ $projet->etape_label }}
                                </span>
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Aucun projet n'est actuellement assigné.</p>
            @endif
        </div>
    </div>
</div>
@endsection