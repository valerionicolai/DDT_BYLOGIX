@extends('public.layout')

@section('title', $type . ' Not Found')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <!-- Error Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-6">
            <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>

        <!-- Error Message -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $type }} Not Found</h1>
        
        <p class="text-gray-600 mb-6">
            {{ $message ?? 'The requested item was not found in the system.' }}
        </p>

        <!-- Possible Actions -->
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 text-left">
            <h3 class="text-sm font-medium text-blue-800 mb-2">What you can do:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Verify that the QR code is correct</li>
                <li>• Try scanning the code again</li>
                <li>• Check that the item has not been removed</li>
                <li>• Contact the administrator if the problem persists</li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('public.scanner') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                </svg>
                Scan New QR Code
            </a>
            
            <button onclick="window.history.back()" 
                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </button>
        </div>

        <!-- Help Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                Need help? 
                <a href="/admin" class="text-blue-600 hover:text-blue-800 font-medium">
                    Access the admin panel
                </a>
            </p>
        </div>
    </div>
</div>
@endsection