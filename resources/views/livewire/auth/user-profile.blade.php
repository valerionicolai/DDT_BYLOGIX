<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">User Profile</h1>
        <p class="mt-2 text-gray-600">Manage your account settings and personal information</p>
    </div>

    <!-- Success Message -->
    @if($successMessage)
        <div class="mb-6 rounded-md bg-green-50 p-4" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ $successMessage }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" @click="show = false" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if($errorMessage)
        <div class="mb-6 rounded-md bg-red-50 p-4" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ $errorMessage }}</h3>
                    @if(!empty($errors))
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors as $field => $fieldErrors)
                                    @foreach($fieldErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" @click="show = false" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update your account's profile information and email address.</p>
                </div>

                <form wire:submit="updateProfile" class="px-6 py-4 space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1">
                            <input 
                                wire:model.live="name"
                                type="text" 
                                id="name" 
                                name="name" 
                                autocomplete="name"
                                required
                                class="block w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :disabled="$loading"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input 
                                wire:model.live="email"
                                type="email" 
                                id="email" 
                                name="email" 
                                autocomplete="email"
                                required
                                class="block w-full px-3 py-2 border @error('email') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :disabled="$loading"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="$loading"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <div wire:loading.remove wire:target="updateProfile">
                                Save Changes
                            </div>
                            <div wire:loading wire:target="updateProfile" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- User Info Sidebar -->
        <div class="space-y-6">
            <!-- User Avatar Card -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="h-12 w-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $name ?: 'User' }}</h3>
                    <p class="text-sm text-gray-500">{{ $email }}</p>
                    @if($user && isset($user['role']))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                            {{ ucfirst($user['role']) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Account Stats -->
            @if($user)
                <div class="bg-white shadow rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Account Information</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Member Since</dt>
                            <dd class="text-sm text-gray-900">
                                {{ isset($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('M d, Y') : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Updated</dt>
                            <dd class="text-sm text-gray-900">
                                {{ isset($user['updated_at']) ? \Carbon\Carbon::parse($user['updated_at'])->format('M d, Y') : 'N/A' }}
                            </dd>
                        </div>
                        @if(isset($user['email_verified_at']))
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email Status</dt>
                                <dd class="text-sm">
                                    @if($user['email_verified_at'])
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pending
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>

    <!-- Password Update Section -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                <p class="mt-1 text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <form wire:submit="updatePassword" class="px-6 py-4 space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <div class="mt-1">
                        <input 
                            wire:model.live="current_password"
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            autocomplete="current-password"
                            class="block w-full px-3 py-2 border @error('current_password') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :disabled="$loading"
                        >
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <div class="mt-1">
                        <input 
                            wire:model.live="new_password"
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border @error('new_password') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :disabled="$loading"
                        >
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <div class="mt-1">
                        <input 
                            wire:model.live="new_password_confirmation"
                            type="password" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border @error('new_password_confirmation') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :disabled="$loading"
                        >
                        @error('new_password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        :disabled="$loading"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <div wire:loading.remove wire:target="updatePassword">
                            Update Password
                        </div>
                        <div wire:loading wire:target="updatePassword" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@script
<script>
    // Listen for profile update events
    $wire.on('profile-updated', (event) => {
        console.log('Profile updated successfully:', event);
        
        // Show success notification
        if (window.showNotification) {
            window.showNotification('Profile updated successfully!', 'success');
        }
    });

    $wire.on('password-updated', (event) => {
        console.log('Password updated successfully:', event);
        
        // Show success notification
        if (window.showNotification) {
            window.showNotification('Password updated successfully!', 'success');
        }
        
        // Clear password fields
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('new_password_confirmation').value = '';
    });

    $wire.on('profile-error', (event) => {
        console.log('Profile update error:', event);
        
        // Show error notification
        if (window.showNotification) {
            window.showNotification(event.message || 'Profile update failed', 'error');
        }
    });
</script>
@endscript