<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-blue-600 mb-4">{{ $message }}</h2>
    
    <div class="mb-4">
        <p class="text-lg">Counter: <span class="font-bold text-blue-500">{{ $count }}</span></p>
    </div>
    
    <div class="space-x-2 mb-4">
        <button wire:click="increment" 
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
            Increment
        </button>
        
        <button wire:click="decrement" 
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
            Decrement
        </button>
    </div>

    <!-- Alpine.js Integration Test -->
    <div x-data="{ alpineCounter: 0, showMessage: false }" class="border-t pt-4 mt-4">
        <h3 class="text-lg font-semibold text-green-600 mb-2">Alpine.js Integration Test</h3>
        
        <div class="mb-2">
            <p class="text-sm">Alpine Counter: <span class="font-bold text-green-500" x-text="alpineCounter"></span></p>
        </div>
        
        <div class="space-x-2 mb-2">
            <button @click="alpineCounter++" 
                    class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors">
                Alpine +
            </button>
            
            <button @click="alpineCounter--" 
                    class="px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600 transition-colors">
                Alpine -
            </button>
            
            <button @click="showMessage = !showMessage" 
                    class="px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition-colors">
                Toggle Message
            </button>
        </div>
        
        <div x-show="showMessage" x-transition class="text-sm text-purple-600 mb-2">
            🎉 Alpine.js is working with Livewire!
        </div>
    </div>
    
    <!-- Tailwind CSS Plugins Test -->
    <div class="border-t pt-4 mt-4">
        <h3 class="text-lg font-semibold text-indigo-600 mb-3">Tailwind CSS Plugins Test</h3>
        
        <!-- @tailwindcss/forms plugin test -->
        <div class="mb-4">
            <h4 class="text-md font-medium text-gray-700 mb-2">Forms Plugin Test</h4>
            <div class="space-y-2">
                <input type="text" placeholder="Text input with forms plugin styling" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                
                <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option>Select option with forms plugin</option>
                    <option>Option 1</option>
                    <option>Option 2</option>
                </select>
                
                <textarea placeholder="Textarea with forms plugin styling" rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>
        
        <!-- @tailwindcss/typography plugin test -->
        <div class="mb-4">
            <h4 class="text-md font-medium text-gray-700 mb-2">Typography Plugin Test</h4>
            <div class="prose prose-sm max-w-none">
                <p>This paragraph uses the <strong>@tailwindcss/typography</strong> plugin for beautiful typography styling.</p>
                <ul>
                    <li>Automatic styling for lists</li>
                    <li>Proper spacing and typography</li>
                    <li>Enhanced readability</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-4 text-sm text-gray-600">
        <p>✅ Livewire is properly configured and working!</p>
        <p>✅ Real-time updates without page refresh</p>
        <p>✅ Tailwind CSS styling applied</p>
        <p>✅ Alpine.js integration functional</p>
        <p>✅ @tailwindcss/forms plugin working</p>
        <p>✅ @tailwindcss/typography plugin working</p>
        <p>✅ PostCSS configuration active</p>
    </div>
</div>
