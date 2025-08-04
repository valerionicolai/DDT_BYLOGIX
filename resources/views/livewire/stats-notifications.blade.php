<div class="hidden">
    {{-- This component handles notifications and doesn't need visible content --}}
    {{-- It listens for events and shows notifications --}}
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('dashboard-stats-refreshed', (event) => {
                console.log('Dashboard stats refreshed at:', event.timestamp);
                
                // Optional: Update any UI elements that show last update time
                const updateElements = document.querySelectorAll('[data-stats-update-time]');
                updateElements.forEach(element => {
                    element.textContent = 'Last updated: ' + new Date(event.timestamp).toLocaleTimeString();
                });
            });
        });
    </script>
</div>