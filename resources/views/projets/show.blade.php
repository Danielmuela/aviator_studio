@extends('layouts.app')

@section('title', $projet->titre . ' - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-400">{{ $projet->titre }}</h1>
        @can('update', $projet)
            @if(!auth()->user()->isArtiste())
                <a href="{{ route('projets.edit', $projet) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Modifier
                </a>
            @endif
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Informations du projet</h3>
                    
                    <div class="space-y-4">
                        @if($projet->description)
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Description</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->description }}</p>
                            </div>
                        @endif

                        @if($projet->etape === 'distribution_terminee')
                            <div class="bg-green-900 text-green-300 rounded p-3 font-semibold flex items-center">
                                <span class="mr-2">🎉</span> Votre musique est maintenant disponible sur toutes les plateformes de streaming musical dans le monde !
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Artiste</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->artiste->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Responsable</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->responsable->name }}</p>
                            </div>
                            @if($projet->date_debut)
                                <div>
                                    <label class="block text-sm font-medium text-blue-300">Date de début</label>
                                    <p class="mt-1 text-sm text-white">{{ $projet->date_debut->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($projet->date_fin_prevue)
                                <div>
                                    <label class="block text-sm font-medium text-blue-300">Date de fin prévue</label>
                                    <p class="mt-1 text-sm text-white">{{ $projet->date_fin_prevue->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des étapes -->
            <div class="bg-[#101828] shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Historique des étapes</h3>
                    
                    @if($projet->etapes->count() > 0)
                        <div class="space-y-3">
                            @foreach($projet->etapes->sortByDesc('created_at') as $etape)
                                <div class="flex items-center justify-between p-3 bg-[#1a2233] rounded">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full text-white" style="background-color: {{ $projet->etape_color }}">
                                                {{ $etape->etape_label }}
                                            </span>
                                            <span class="ml-2 text-sm text-blue-200">
                                                par {{ $etape->modifiePar->name }}
                                            </span>
                                        </div>
                                        @if($etape->commentaire)
                                            <p class="mt-1 text-sm text-white">{{ $etape->commentaire }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-blue-300">{{ $etape->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-blue-200">Aucun historique disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statut et actions -->
        <div class="space-y-6">
            <!-- Statut actuel -->
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Statut actuel</h3>
                    
                    <div class="text-center">
                        <span class="inline-block px-4 py-2 text-lg font-medium rounded-full text-black bg-orange-400" style="background-color: {{ $projet->etape_color }}">
                            {{ $projet->etape_label }}
                        </span>
                    </div>

                    @can('update', $projet)
                        @if(!auth()->user()->isArtiste())
                            <div class="mt-4">
                                <form action="{{ route('projets.update-etape', $projet) }}" method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <div>
                                            <label for="etape" class="block text-sm font-medium text-blue-300">Changer l'étape</label>
                                            <select name="etape" id="etape" required 
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="enregistrement" {{ $projet->etape == 'enregistrement' ? 'selected' : '' }}>Enregistrement</option>
                                                <option value="mixage" {{ $projet->etape == 'mixage' ? 'selected' : '' }}>Mixage</option>
                                                <option value="mastering" {{ $projet->etape == 'mastering' ? 'selected' : '' }}>Mastering</option>
                                                @if(auth()->user()->isAdmin())
                                                    <option value="distribution_en_cours" {{ $projet->etape == 'distribution_en_cours' ? 'selected' : '' }}>Distribution en cours</option>
                                                    <option value="distribution_terminee" {{ $projet->etape == 'distribution_terminee' ? 'selected' : '' }}>Distribution terminée</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div>
                                            <label for="commentaire" class="block text-sm font-medium text-blue-300">Commentaire (optionnel)</label>
                                            <textarea name="commentaire" id="commentaire" rows="2" 
                                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            Mettre à jour l'étape
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endcan

                    <!-- Section Upload de fichiers pour le mastering -->
                    @if($projet->etape === 'mastering' && auth()->user()->isResponsableStudio())
                        <div class="mt-6 p-4 bg-[#1a2233] rounded-lg">
                            <h4 class="text-md font-medium text-orange-400 mb-3">📁 Upload des fichiers finaux</h4>
                            
                            @if(!$projet->hasFichierAudio() && !$projet->hasFichierVideo())
                                <form action="{{ route('projets.upload-media', $projet) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-300 mb-2">🎵 Fichier Audio (MP3, WAV, AAC, FLAC - Max 500MB)</label>
                                        <input type="file" name="fichier_audio" accept=".mp3,.wav,.aac,.flac" 
                                               class="block w-full text-sm text-blue-200 file:bg-blue-900 file:text-blue-200 file:rounded file:border-none file:px-3 file:py-2 file:mr-3">
                                        @error('fichier_audio')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-300 mb-2">🎬 Fichier Vidéo (MP4, AVI, MOV, MKV - Max 1GB)</label>
                                        <input type="file" name="fichier_video" accept=".mp4,.avi,.mov,.mkv" 
                                               class="block w-full text-sm text-blue-200 file:bg-blue-900 file:text-blue-200 file:rounded file:border-none file:px-3 file:py-2 file:mr-3">
                                        @error('fichier_video')<div class="text-orange-400 text-xs mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-orange-400 text-black px-4 py-2 rounded-md hover:bg-orange-500 font-semibold">
                                        📤 Envoyer à l'administrateur
                                    </button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <p class="text-green-400 font-medium">✅ Fichiers uploadés avec succès !</p>
                                    
                                    @if($projet->hasFichierAudio() && (
                                        auth()->user()->isAdmin() ||
                                        auth()->user()->isResponsableStudio() ||
                                        auth()->user()->isSecretaire()
                                    ))
                                        <div class="flex items-center justify-between p-2 bg-[#101828] rounded">
                                            <div>
                                                <span class="text-sm text-blue-300">🎵 {{ $projet->fichier_audio_nom_original }}</span>
                                                <span class="text-xs text-gray-400 ml-2">({{ $projet->getAudioSizeFormatted() }})</span>
                                            </div>
                                            <a href="{{ route('projets.download-audio', $projet) }}" 
                                               class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                                Télécharger
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($projet->hasFichierVideo())
                                        <div class="flex items-center justify-between p-2 bg-[#101828] rounded">
                                            <div>
                                                <span class="text-sm text-blue-300">🎬 {{ $projet->fichier_video_nom_original }}</span>
                                                <span class="text-xs text-gray-400 ml-2">({{ $projet->getVideoSizeFormatted() }})</span>
                                            </div>
                                            @if(auth()->user()->isAdmin())
                                                <a href="{{ route('projets.download-video', $projet) }}" 
                                                   class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                                    Télécharger
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Section validation admin des fichiers -->
                    @if(auth()->user()->isAdmin() && ($projet->hasFichierAudio() || $projet->hasFichierVideo()))
                        <div class="mt-6 p-4 bg-[#1a2233] rounded-lg">
                            <h4 class="text-md font-medium text-orange-400 mb-3">🔍 Validation des fichiers</h4>
                            
                            @if(!$projet->fichiers_valides_admin)
                                <form action="{{ route('projets.validate-files', $projet) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-300 mb-2">Commentaire (optionnel)</label>
                                        <textarea name="commentaire" rows="3" 
                                                  class="w-full border-blue-900 bg-black text-white rounded-md focus:ring-blue-400 focus:border-orange-400"
                                                  placeholder="Vos observations sur les fichiers..."></textarea>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <button type="submit" name="valide" value="1" 
                                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            ✅ Approuver
                                        </button>
                                        <button type="submit" name="valide" value="0" 
                                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            ❌ Rejeter
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="p-3 bg-green-900 border border-green-700 rounded">
                                    <p class="text-green-400 font-medium">✅ Fichiers validés par l'administrateur</p>
                                    @if($projet->commentaire_admin_fichiers)
                                        <p class="text-sm text-gray-300 mt-1">{{ $projet->commentaire_admin_fichiers }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <div class="mt-6 space-y-2">
                            <form action="{{ route('projets.update', $projet) }}" method="POST" onsubmit="return confirm('Valider ce projet ?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="valide">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Valider le projet</button>
                            </form>
                            <form action="{{ route('projets.update', $projet) }}" method="POST" onsubmit="return confirm('Terminer ce projet ?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="termine">
                                <button type="submit" class="w-full bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-800">Marquer comme terminé</button>
                            </form>
                            <form action="{{ route('projets.destroy', $projet) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce projet ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Supprimer le projet</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progression -->
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Progression</h3>
                    
                    <div class="space-y-3">
                        @php
                            $etapes = ['enregistrement', 'mixage', 'mastering', 'distribution_en_cours', 'distribution_terminee'];
                            $etapeActuelle = array_search($projet->etape, $etapes);
                            $pourcentage = (($etapeActuelle + 1) / count($etapes)) * 100;
                        @endphp
                        
                        <div class="w-full bg-blue-900 rounded-full h-4">
                            <div class="bg-orange-400 h-4 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                        </div>
                        <div class="text-sm text-blue-200 text-center">
                            {{ number_format($pourcentage, 1) }}% terminé
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 