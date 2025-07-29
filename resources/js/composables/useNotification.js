import { ref } from 'vue'

// Global notification state
const notifications = ref([])
let notificationId = 0

export function useNotification() {
  const showNotification = (message, type = 'info', duration = 5000) => {
    const id = ++notificationId
    const notification = {
      id,
      message,
      type, // 'success', 'error', 'warning', 'info'
      duration,
      timestamp: Date.now()
    }
    
    notifications.value.push(notification)
    
    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, duration)
    }
    
    return id
  }
  
  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }
  
  const clearAllNotifications = () => {
    notifications.value = []
  }
  
  const getNotificationClass = (type) => {
    const baseClasses = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden'
    
    switch (type) {
      case 'success':
        return `${baseClasses} border-l-4 border-green-400`
      case 'error':
        return `${baseClasses} border-l-4 border-red-400`
      case 'warning':
        return `${baseClasses} border-l-4 border-yellow-400`
      case 'info':
      default:
        return `${baseClasses} border-l-4 border-blue-400`
    }
  }
  
  const getNotificationIcon = (type) => {
    switch (type) {
      case 'success':
        return {
          class: 'h-6 w-6 text-green-400',
          path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        }
      case 'error':
        return {
          class: 'h-6 w-6 text-red-400',
          path: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        }
      case 'warning':
        return {
          class: 'h-6 w-6 text-yellow-400',
          path: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z'
        }
      case 'info':
      default:
        return {
          class: 'h-6 w-6 text-blue-400',
          path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        }
    }
  }
  
  return {
    notifications,
    showNotification,
    removeNotification,
    clearAllNotifications,
    getNotificationClass,
    getNotificationIcon
  }
}