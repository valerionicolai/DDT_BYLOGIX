import { ref } from 'vue'

export default function useNotifications() {
  const notifications = ref([])

  const addNotification = (notification) => {
    const id = Date.now()
    const newNotification = {
      id,
      type: 'info', // info, success, warning, error
      title: '',
      message: '',
      duration: 5000,
      ...notification
    }

    notifications.value.push(newNotification)

    // Auto-remove dopo la durata specificata
    if (newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }

    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    notifications.value = []
  }

  // Metodi di convenienza
  const success = (message, title = 'Successo') => {
    return addNotification({ type: 'success', title, message })
  }

  const error = (message, title = 'Errore') => {
    return addNotification({ type: 'error', title, message, duration: 0 })
  }

  const warning = (message, title = 'Attenzione') => {
    return addNotification({ type: 'warning', title, message })
  }

  const info = (message, title = 'Informazione') => {
    return addNotification({ type: 'info', title, message })
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    clearAll,
    success,
    error,
    warning,
    info
  }
}