@props(['count' => 4])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $count }} gap-6">
    @for($i = 0; $i < $count; $i++)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 animate-pulse">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full mr-3"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
            </div>
            
            <div class="space-y-3">
                @for($j = 0; $j < 4; $j++)
                    <div class="flex justify-between items-center">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-8"></div>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
</div>