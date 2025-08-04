<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-blue-600 mb-4">{{ $message }}</h2>
    
    <div class="mb-4">
        <p class="text-lg">Counter: <span class="font-bold text-blue-500">{{ $count }}</span></p>
    </div>
    
    <div class="space-x-2">
        <button wire:click="increment" 
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
            Increment
        </button>
        
        <button wire:click="decrement" 
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
            Decrement
        </button>
    </div>
    
    <div class="mt-4 text-sm text-gray-600">
        <p>✅ Livewire is properly configured and working!</p>
        <p>✅ Real-time updates without page refresh</p>
        <p>✅ Tailwind CSS styling applied</p>
    </div>
</div>
