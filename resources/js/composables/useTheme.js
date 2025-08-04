import { ref, computed, watch, onMounted } from 'vue'

const THEME_STORAGE_KEY = 'dtt-theme'

// Reactive theme state
const currentTheme = ref('light')
const systemPrefersDark = ref(false)

export function useTheme() {
  // Computed theme that considers 'auto' setting
  const appliedTheme = computed(() => {
    if (currentTheme.value === 'auto') {
      return systemPrefersDark.value ? 'dark' : 'light'
    }
    return currentTheme.value
  })

  // Check system preference
  const checkSystemPreference = () => {
    if (typeof window !== 'undefined' && window.matchMedia) {
      systemPrefersDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
  }

  // Apply theme to document
  const applyTheme = (theme) => {
    if (typeof document !== 'undefined') {
      const root = document.documentElement
      
      // Remove existing theme classes
      root.classList.remove('light', 'dark')
      
      // Add new theme class
      root.classList.add(theme)
      
      // Update data attribute for CSS targeting
      root.setAttribute('data-theme', theme)
    }
  }

  // Set theme
  const setTheme = (theme) => {
    currentTheme.value = theme
    
    // Save to localStorage
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem(THEME_STORAGE_KEY, theme)
    }
  }

  // Load theme from localStorage
  const loadTheme = () => {
    if (typeof localStorage !== 'undefined') {
      const savedTheme = localStorage.getItem(THEME_STORAGE_KEY)
      if (savedTheme && ['light', 'dark', 'auto'].includes(savedTheme)) {
        currentTheme.value = savedTheme
      }
    }
  }

  // Initialize theme system
  const initTheme = () => {
    checkSystemPreference()
    loadTheme()
    
    // Listen for system preference changes
    if (typeof window !== 'undefined' && window.matchMedia) {
      const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
      mediaQuery.addEventListener('change', (e) => {
        systemPrefersDark.value = e.matches
      })
    }
  }

  // Watch for theme changes and apply them
  watch(appliedTheme, (newTheme) => {
    applyTheme(newTheme)
  }, { immediate: true })

  // Theme options for UI
  const themeOptions = [
    { value: 'light', label: 'Chiaro' },
    { value: 'dark', label: 'Scuro' },
    { value: 'auto', label: 'Automatico' }
  ]

  // Initialize on mount
  onMounted(() => {
    initTheme()
  })

  return {
    currentTheme,
    appliedTheme,
    systemPrefersDark,
    themeOptions,
    setTheme,
    initTheme
  }
}