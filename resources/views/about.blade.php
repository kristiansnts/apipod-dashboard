@extends('layouts.guest')

@section('title', 'About')

@section('content')
    <div class="w-full max-w-4xl glass p-8 lg:p-12 rounded-[40px] shadow-2xl animate-fade-in">
        <div class="mb-12">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-6">About Apipod</h1>
            <p class="text-xl text-gray-400 leading-relaxed max-w-2xl">
                Apipod is a production-ready smart API proxy designed to unify the fragmented AI landscape. We provide a
                single gateway to the world's most powerful models.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span
                            class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center text-indigo-400 text-sm">01</span>
                        Our Mission
                    </h2>
                    <p class="text-gray-400 leading-relaxed">
                        To simplify AI integration for developers by providing an intelligent orchestration layer that
                        handles routing, fallback, and cost optimization automatically.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span
                            class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-400 text-sm">02</span>
                        What We Offer
                    </h2>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Unified Multi-provider API
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Smart Model Routing & Fallbacks
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Real-time Usage Analytics
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Enterprise-grade Security
                        </li>
                    </ul>
                </div>
            </div>

            <div class="space-y-8">
                <div class="glass p-6 rounded-3xl border-indigo-500/10">
                    <h3 class="text-xl font-bold text-white mb-2">Build once. Route anywhere.</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Stop rewriting your integration every time a new model is released. Apipod handles the complexity of
                        different provider formats so you can focus on building features.
                    </p>
                    <div class="flex gap-4">
                        <div class="flex-1 h-1 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 w-3/4"></div>
                        </div>
                        <div class="flex-1 h-1 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 w-1/2"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <a href="{{ route('register') }}"
                        class="btn-primary py-4 rounded-2xl text-center text-white font-bold transition-all">
                        Register Now
                    </a>
                    <a href="{{ url('/') }}"
                        class="bg-white/5 border border-white/10 hover:bg-white/10 py-4 rounded-2xl text-center text-gray-300 font-bold transition-all">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
    </style>
@endsection