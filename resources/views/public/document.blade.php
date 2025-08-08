@extends('public.layout')

@section('title', 'Document: ' . $document->name)

@section('content')
<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $document->name }}</h1>
                <p class="text-green-100 mt-1">Document ID: #{{ $document->id }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ $document->documentType->name }}
                </span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Document Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Document Information</h2>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->name }}</dd>
                    </div>
                    
                    @if($document->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->description }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Document Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->documentType->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->documentCategory->name }}</dd>
                    </div>
                    
                    @if($document->barcode)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $document->barcode }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created on</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Project and Materials Information -->
            <div>
                <!-- Project Information -->
                @if($document->project)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Associated Project</h2>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="space-y-2">
                            <p class="text-sm">
                                <span class="font-medium">Name:</span> {{ $document->project->name }}
                            </p>
                            @if($document->project->description)
                            <p class="text-sm">
                                <span class="font-medium">Description:</span> 
                                {{ Str::limit($document->project->description, 150) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Materials Information -->
                @if($document->materials->count() > 0)
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        Associated Materials ({{ $document->materials->count() }})
                    </h2>
                    <div class="space-y-3">
                        @foreach($document->materials as $material)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('public.material', $material->id) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $material->name }}
                                        </a>
                                    </h3>
                                    @if($material->description)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit($material->description, 80) }}
                                    </p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
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
                        @endforeach
                    </div>
                </div>
                @else
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Associated Materials</h2>
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No materials associated with this document</p>
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
                Back
            </button>
            
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                
                <a href="{{ route('public.scanner') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                    </svg>
                    Scan Another
                </a>
            </div>
        </div>
    </div>
</div>
@endsection