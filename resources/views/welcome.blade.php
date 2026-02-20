<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Apipod | Smart AI API Proxy</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary: #2563eb;
                --primary-hover: #1d4ed8;
                --bg: #ffffff;
                --text-main: #111827;
                --text-muted: #64748b;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: var(--bg);
                color: var(--text-main);
                -webkit-font-smoothing: antialiased;
            }

            .mono {
                font-family: 'IBM Plex Mono', monospace;
            }

            .btn-blue {
                background-color: var(--primary);
                color: white;
                transition: all 0.2s ease;
            }

            .btn-blue:hover {
                background-color: var(--primary-hover);
                transform: translateY(-1px);
            }

            .terminal-window {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
            }

            .feature-marker {
                color: var(--primary);
                font-weight: 700;
                margin-right: 8px;
            }

            section {
                padding-top: 80px;
                padding-bottom: 80px;
            }

            .max-w-hero {
                max-width: 800px;
            }

            /* Minimal focus ring */
            a:focus, button:focus {
                outline: 2px solid var(--primary);
                outline-offset: 2px;
            }
        </style>
    </head>
    <body class="antialiased overflow-x-hidden">
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center font-bold text-white text-[10px]">A</div>
                    <span class="text-lg font-bold tracking-tight text-gray-900 mono uppercase">Apipod</span>
                </div>
                
                <div class="hidden md:flex items-center gap-10 text-[13px] font-semibold text-gray-500 uppercase tracking-wider">
                    <a href="#features" class="hover:text-blue-600 transition-colors">Features</a>
                    <a href="#integrations" class="hover:text-blue-600 transition-colors">Integrations</a>
                    <a href="https://github.com/apipod" class="hover:text-blue-600 transition-colors">GitHub</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/home') }}" class="text-[13px] font-bold text-blue-600 uppercase tracking-wider">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[13px] font-bold text-gray-600 hover:text-gray-900 uppercase tracking-wider">Log in</a>
                        <a href="{{ route('register') }}" class="btn-blue px-4 py-2 rounded font-bold text-[13px] uppercase tracking-wider">Join Free</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="mt-20">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <h1 class="text-4xl lg:text-6xl font-extrabold mb-8 tracking-tighter mono lg:leading-[1.1] max-w-hero mx-auto">
                    The Smart API Proxy for <br>
                    <span class="text-blue-600">Unified AI Orchestration.</span>
                </h1>
                
                <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-12 font-medium leading-relaxed">
                    Intelligently route requests between multiple AI providers including Antigravity, Google AI, and OpenAI. One gateway, endless models.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-20">
                    <a href="{{ route('register') }}" class="btn-blue px-8 py-4 rounded font-bold uppercase tracking-wider text-sm shadow-sm">
                        Get Started for Free
                    </a>
                    <a href="#features" class="bg-gray-100 text-gray-900 px-8 py-4 rounded font-bold uppercase tracking-wider text-sm hover:bg-gray-200 transition-colors">
                        Documentation
                    </a>
                </div>

                <!-- Code/Terminal Placeholder -->
                <div class="max-w-3xl mx-auto terminal-window text-left overflow-hidden shadow-sm">
                    <div class="bg-white border-b border-gray-100 flex items-center justify-between px-4 py-2">
                        <div class="flex gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 mono tracking-widest uppercase">bash</span>
                    </div>
                    <div class="p-8 mono text-xs lg:text-sm text-gray-800 leading-relaxed overflow-x-auto">
                        <div class="flex">
                            <span class="text-blue-600 mr-4">$</span>
                            <span>curl -X POST https://api.apipod.com/v1/chat \</span>
                        </div>
                        <div class="pl-8">
                            -H <span class="text-blue-600">"Authorization: Bearer $KEY"</span> \
                        </div>
                        <div class="pl-8">
                            -d '{ <span class="text-blue-500">"model"</span>: <span class="text-gray-500">"cursor-sonnet"</span> }'
                        </div>
                        <div class="mt-6 text-gray-400 italic font-medium"># Routed to the optimal provider in 124ms</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="bg-gray-50 border-y border-gray-100">
            <div class="max-w-6xl mx-auto px-6">
                <div class="mb-16">
                    <h2 class="text-3xl font-extrabold tracking-tight mono mb-4 uppercase">What is Apipod?</h2>
                    <p class="text-gray-500 font-medium max-w-xl">A minimalist proxy built for performance-critical AI applications.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-16">
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] Smart Routing</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Automatically chooses the best provider based on health, latency, and cost.</p>
                    </div>
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] Multi-Provider</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Unified API for Claude, DeepSeek, OpenAI, Google Gemini, and custom endpoints.</p>
                    </div>
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] Usage Analytics</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Detailed logging and quota management with native PostgreSQL support.</p>
                    </div>
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] SSE Streaming</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Native server-sent events for real-time AI responses with zero latency overhead.</p>
                    </div>
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] Admin Panel</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Easy management of keys, user tiers, and model pool configurations via Filament.</p>
                    </div>
                    <div>
                        <div class="mono text-blue-600 font-bold mb-4">[*] Docker Native</div>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Deploy anywhere in seconds. Built for high-concurrency production workloads.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Integrations -->
        <section id="integrations">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-2xl font-bold tracking-tight mono uppercase">Supported Integrations</h2>
                </div>
                
                <div class="flex flex-wrap justify-center gap-x-12 gap-y-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    @foreach(['Claude', 'DeepSeek', 'Nvidia', 'OpenCode', 'Google', 'OpenAI', 'GitHub'] as $integration)
                        <div class="flex items-center gap-2 group cursor-default">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400 group-hover:text-blue-600 transition-colors">{{ $integration }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-blue-600 py-24 text-center">
            <div class="max-w-4xl mx-auto px-6">
                <h2 class="text-3xl lg:text-5xl font-extrabold text-white mb-8 tracking-tighter mono">Build your AI future.</h2>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-10 py-5 rounded font-extrabold uppercase tracking-wider shadow-xl hover:bg-gray-50 transition-all">
                        Create Free Account
                    </a>
                </div>
                <p class="mt-8 text-blue-100 font-bold text-xs uppercase tracking-[0.2em] opacity-80">Instant activation &middot; No credit card</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-20 px-6 border-t border-gray-100">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-blue-600 rounded flex items-center justify-center font-bold text-white text-[8px]">A</div>
                    <span class="text-sm font-bold tracking-tight text-gray-900 mono uppercase">Apipod</span>
                </div>
                
                <div class="flex flex-wrap justify-center gap-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                    <a href="#" class="hover:text-blue-600 transition-colors">Twitter</a>
                    <a href="#" class="hover:text-blue-600 transition-colors">GitHub</a>
                    <a href="#" class="hover:text-blue-600 transition-colors">Docs</a>
                    <a href="#" class="hover:text-blue-600 transition-colors">Status</a>
                    <a href="#" class="hover:text-blue-600 transition-colors">Privacy</a>
                </div>

                <p class="text-[11px] font-bold text-gray-300 uppercase tracking-widest">&copy; {{ date('Y') }} Apipod Inc.</p>
            </div>
        </footer>
    </body>
</html>