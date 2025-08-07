@extends('public.layout')

@section('title', 'Materiale: ' . $material->name)

@section('content')
<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $material->name }}</h1>
                <p class="text-blue-100 mt-1">Materiale ID: #{{ $material->id }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($material->state->value === 'available') bg-green-100 text-green-800
                    @elseif($material->state->value === 'in_use') bg-yellow-100 text-yellow-800
                    @elseif($material->state->value === 'maintenance') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ $material->state->getLabel() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Material Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Materiale</h2>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->name }}</dd>
                    </div>
                    
                    @if($material->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Descrizione</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->description }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo Materiale</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->materialType->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stato</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($material->state->value === 'available') bg-green-100 text-green-800
                                @elseif($material->state->value === 'in_use') bg-yellow-100 text-yellow-800
                                @elseif($material->state->value === 'maintenance') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $material->state->getLabel() }}
                            </span>
                        </dd>
                    </div>
                    
                    @if($material->qr_code)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Codice QR</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $material->qr_code }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Creato il</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ultimo aggiornamento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- QR Code and Related Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">QR Code</h2>
                
                @if($material->qr_code_url)
                <div class="text-center mb-6">
                    <img src="{{ $material->qr_code_url }}" 
                         alt="QR Code per {{ $material->name }}" 
                         class="mx-auto border border-gray-200 rounded-lg shadow-sm"
                         style="max-width: 200px;">
                    <p class="mt-2 text-sm text-gray-500">Scansiona per accedere rapidamente</p>
                </div>
                @endif

                <!-- Document Information -->
                @if($material->document)
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Documento Associato</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Nome:</span> 
                            <a href="{{ route('public.document', $material->document->id) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                {{ $material->document->name }}
                            </a>
                        </p>
                        @if($material->document->description)
                        <p class="text-sm">
                            <span class="font-medium">Descrizione:</span> 
                            {{ Str::limit($material->document->description, 100) }}
                        </p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Project Information -->
                @if($material->project)
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Progetto</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Nome:</span> {{ $material->project->name }}
                        </p>
                        @if($material->project->description)
                        <p class="text-sm">
                            <span class="font-medium">Descrizione:</span> 
                            {{ Str::limit($material->project->description, 100) }}
                        </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <button onclick="window.history.back()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Indietro
            </button>
            
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Stampa
                </button>
                
                <a href="{{ route('public.scanner') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                    </svg>
                    Scansiona Altro
                </a>
            </div>
        </div>
    </div>
</div>
@endsection