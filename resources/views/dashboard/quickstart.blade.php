@extends('layouts.app')

@section('title', 'Quick Start Guide')
@section('subtitle', 'Follow these steps to connect your coding tools and start using Apipod.')

@section('content')
    <!-- Main Content Card -->
    <div class="card p-8 lg:p-10 mb-8">
        <div class="max-w-3xl">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Getting Started</h2>

            <div class="space-y-12">
                <!-- Step 1: Subscribe Plan -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        1</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Subscribe to a Plan</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Choose a subscription plan that
                            fits your needs to access our unified AI gateway and token quotas.</p>
                        <a href="{{ route('shop.index') }}" class="btn-secondary text-sm">Browse Plans</a>
                    </div>
                </div>

                <!-- Step 2: Manage API Key -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        2</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Create an API Key</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Generate a secure API key to
                            authenticate your requests from the CLI and other tools.</p>
                        <a href="{{ route('dashboard.api-keys') }}" class="btn-secondary text-sm">Manage API Keys</a>
                    </div>
                </div>

                <!-- Step 3: Provider Key (Conditional for BYOK) -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        3</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Configure Provider Keys <span
                                class="text-[10px] ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 rounded uppercase tracking-widest">BYOK
                                Users</span></h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">If you are on a Free or BYOK plan,
                            connect your own provider keys (OpenRouter, Nvidia, etc.) to enable model orchestration.</p>
                        <a href="{{ route('dashboard.provider-keys') }}" class="btn-secondary text-sm">Set Provider Keys</a>
                    </div>
                </div>

                <!-- Step 4: Install CLI -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        4</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Install the Apipod CLI</h3>
                        <p class="text-sm text-gray-500 mb-6 font-medium leading-relaxed">The CLI allows you to quickly
                            authenticate your environment and connect to your favorite coding tools.</p>

                        <div class="space-y-4">
                            <!-- macOS / Linux -->
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">macOS /
                                    Linux</span>
                                <div
                                    class="bg-gray-50 border border-gray-100 rounded-lg p-4 mono text-xs text-gray-700 flex items-center justify-between mt-1">
                                    <span>curl -fsSL {{ url('/cli/install.sh') }} | bash</span>
                                    <button
                                        onclick="navigator.clipboard.writeText('curl -fsSL {{ url('/cli/install.sh') }} | bash')"
                                        class="text-blue-600 font-bold uppercase tracking-widest text-[10px] hover:underline ml-4 flex-shrink-0">Copy</button>
                                </div>
                            </div>

                            <!-- Windows -->
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Windows
                                    (PowerShell)</span>
                                <div
                                    class="bg-gray-50 border border-gray-100 rounded-lg p-4 mono text-xs text-gray-700 flex items-center justify-between mt-1">
                                    <span>irm {{ url('/cli/install.ps1') }} | iex</span>
                                    <button
                                        onclick="navigator.clipboard.writeText('irm {{ url('/cli/install.ps1') }} | iex')"
                                        class="text-blue-600 font-bold uppercase tracking-widest text-[10px] hover:underline ml-4 flex-shrink-0">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Login via CLI -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        5</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Login via CLI</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Open your terminal and run the
                            following command to link your dashboard account to your local machine.</p>
                        <div
                            class="bg-gray-950 rounded-lg p-4 mono text-xs text-blue-400 flex items-center justify-between">
                            <span>apipod login</span>
                            <button onclick="navigator.clipboard.writeText('apipod login')"
                                class="text-blue-400 font-bold uppercase tracking-widest text-[10px] hover:underline ml-4 flex-shrink-0">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Connect to Tools -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        6</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Connect to Claude Code / OpenCode</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Simply run the proxy command to
                            provide a unified endpoint for your agentic coding tools.</p>
                        <div
                            class="bg-gray-950 rounded-lg p-4 mono text-xs text-blue-400 flex items-center justify-between">
                            <span>apipod connect</span>
                            <button onclick="navigator.clipboard.writeText('apipod connect')"
                                class="text-blue-400 font-bold uppercase tracking-widest text-[10px] hover:underline ml-4 flex-shrink-0">Copy</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-4 leading-relaxed italic">Once connected, you can use Apipod as
                            your base URL in Claude Code, OpenCode, or any OpenAI-compatible tool.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection