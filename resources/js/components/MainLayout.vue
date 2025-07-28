<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    <NavigationBar />

    <!-- Main Content Area -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-500">
            © 2024 DTT by Logix. Tutti i diritti riservati.
          </p>
          <p class="text-sm text-gray-400">
            v1.0.0 - Sprint 0
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import NavigationBar from './NavigationBar.vue'

export default {
  name: 'MainLayout',
  components: {
    NavigationBar
  }
}
</script>