/**
 * Utility per gestire il localStorage in modo sicuro
 */
const storage = {
  /**
   * Salva un valore nel localStorage
   */
  set(key, value) {
    try {
      const serializedValue = JSON.stringify(value)
      localStorage.setItem(key, serializedValue)
      return true
    } catch (error) {
      console.error('Errore nel salvare nel localStorage:', error)
      return false
    }
  },

  /**
   * Recupera un valore dal localStorage
   */
  get(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(key)
      return item ? JSON.parse(item) : defaultValue
    } catch (error) {
      console.error('Errore nel leggere dal localStorage:', error)
      return defaultValue
    }
  },

  /**
   * Rimuove un valore dal localStorage
   */
  remove(key) {
    try {
      localStorage.removeItem(key)
      return true
    } catch (error) {
      console.error('Errore nel rimuovere dal localStorage:', error)
      return false
    }
  },

  /**
   * Pulisce tutto il localStorage
   */
  clear() {
    try {
      localStorage.clear()
      return true
    } catch (error) {
      console.error('Errore nel pulire il localStorage:', error)
      return false
    }
  },

  /**
   * Verifica se una chiave esiste nel localStorage
   */
  has(key) {
    return localStorage.getItem(key) !== null
  },

  /**
   * Ottiene tutte le chiavi del localStorage
   */
  keys() {
    return Object.keys(localStorage)
  }
}

export default storage