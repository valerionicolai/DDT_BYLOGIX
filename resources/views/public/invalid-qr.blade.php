@extends('public.layout')

@section('title', 'Invalid QR Code')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <!-- Error Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>

        <!-- Error Message -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Invalid QR Code</h1>
        
        <p class="text-gray-600 mb-6">
            {{ $message ?? 'The scanned QR code is invalid or no longer available.' }}
        </p>

        @if(isset($error) && $error)
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error Details</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Possible Causes -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6 text-left">
            <h3 class="text-sm font-medium text-yellow-800 mb-2">Possible Causes:</h3>
            <ul class="text-sm text-yellow-700 space-y-1">
                <li>• The QR code is damaged or unreadable</li>
                <li>• The material or document has been removed from the system</li>
                <li>• The QR code has expired or is no longer valid</li>
                <li>• Scanning or reading error of the code</li>
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
                    Contact the system administrator
                </a>
            </p>
        </div>
    </div>
</div>
@endsection