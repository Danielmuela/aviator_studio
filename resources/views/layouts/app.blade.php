<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aviatrax Studio')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'etape-enregistrement': '#3B82F6',
                        'etape-mixage': '#EC4899',
                        'etape-mastering': '#EAB308',
                        'etape-distribution-cours': '#F97316',
                        'etape-distribution-terminee': '#22C55E',
                    }
                }
            }
        }
    </script>
</head>
<body class="dark bg-black text-white">
    <script>document.documentElement.classList.add('dark');</script>
    @auth
    <nav class="bg-black shadow-lg border-b-4 border-orange-500">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-blue-400">Aviatrax Studio</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="text-blue-400 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-orange-500">
                            Dashboard
                        </a>
                        <a href="{{ route('projets.index') }}" class="text-blue-400 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-orange-500">
                            Projets
                        </a>
                        <a href="{{ route('profile.show') }}" class="text-blue-400 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-orange-500">
                            Profil
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-400 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-orange-500">
                            Administration
                        </a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('profile.show') }}" class="flex items-center group mr-2">
                        @php $user = auth()->user(); @endphp
                        @if($user->photo_path)
                            <span class="w-11 h-11 rounded-full bg-blue-900 flex items-center justify-center border-2 border-blue-400 shadow overflow-hidden">
                                <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Photo de profil" class="w-full h-full object-cover object-center" loading="lazy">
                            </span>
                        @else
                            <span class="w-11 h-11 rounded-full bg-blue-900 flex items-center justify-center border-2 border-blue-400 shadow">
                                <span class="text-2xl text-blue-200 w-full text-center flex items-center justify-center h-full">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </span>
                        @endif
                        <span class="ml-3 text-sm text-gray-200 group-hover:underline">{{ $user->name }}</span>
                    </a>
                    <span class="ml-2 px-2 py-1 text-xs bg-orange-500 text-black rounded">{{ $user->role->name ?? 'Aucun rôle' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="ml-4">
                        @csrf
                        <button type="submit" class="text-orange-400 hover:text-orange-600">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 bg-black text-white">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html> 