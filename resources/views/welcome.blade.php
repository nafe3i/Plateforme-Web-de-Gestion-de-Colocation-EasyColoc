<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyColoc — La colocation simplifiée</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh {
            background-color: #ffffff;
            background-image: radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.12) 0px, transparent 50%);
        }
    </style>
</head>
<body class="antialiased bg-mesh text-slate-900">
    <div class="min-h-screen">
        <header class="sticky top-0 z-50 border-b border-slate-200/60 bg-white/80 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-600 to-emerald-500 shadow-lg shadow-blue-200"></div>
                    <span class="text-xl font-bold tracking-tight text-slate-800">EasyColoc</span>
                </div>

                <div class="flex items-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="group relative inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 transition hover:text-blue-600">Connexion</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-200 transition-all hover:bg-blue-600 hover:shadow-lg">
                            Démarrer gratuitement
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <section class="relative overflow-hidden px-6 pt-16 pb-24 lg:pt-32">
                <div class="mx-auto max-w-7xl">
                    <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                        
                        <div class="relative z-10">
                            <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50/50 px-3 py-1 text-sm font-medium text-blue-700">
                                <span class="mr-2 flex h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                                Nouveau : Gestion des factures simplifiée
                            </div>
                            
                            <h1 class="mt-8 text-5xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-6xl">
                                Gérez vos dépenses de coloc <span class="bg-gradient-to-r from-blue-600 to-emerald-500 bg-clip-text text-transparent">sans l'ombre d'un stress.</span>
                            </h1>
                            
                            <p class="mt-6 text-lg leading-8 text-slate-600 max-w-xl">
                                EasyColoc automatise vos comptes en temps réel. Finis les tableurs complexes et les oublis, concentrez-vous sur l'essentiel : bien vivre ensemble.
                            </p>

                            <div class="mt-10 flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('register') }}" class="flex items-center justify-center rounded-2xl bg-slate-900 px-8 py-4 text-base font-bold text-white transition-all hover:scale-105 hover:bg-slate-800 shadow-xl shadow-slate-200">
                                    Créer ma colocation
                                </a>
                                <a href="#features" class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-8 py-4 text-base font-bold text-slate-700 transition-all hover:bg-slate-50">
                                    Voir les avantages
                                </a>
                            </div>
                        </div>

                        <div class="relative lg:ml-4">
                            <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-tr from-blue-100 to-emerald-100 opacity-50 blur-2xl"></div>
                            <div class="relative rounded-[2rem] border border-white/60 bg-white/40 p-2 shadow-2xl backdrop-blur-sm">
                                <div class="overflow-hidden rounded-[1.8rem] bg-white p-8 shadow-inner">
                                    <h3 class="text-xl font-bold text-slate-800">Fonctionnalités clés</h3>
                                    <div class="mt-8 space-y-6">
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base">Soldes automatiques</p>
                                                <p class="text-sm text-slate-500">Calcul en temps réel de "qui doit combien à qui".</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base">Invitations sécurisées</p>
                                                <p class="text-sm text-slate-500">Ajoutez vos colocataires en un clic via un lien unique.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base">Historique complet</p>
                                                <p class="text-sm text-slate-500">Visualisez toutes les dépenses par catégorie.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

