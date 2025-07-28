/**
 * Formatta una data in base al locale e al formato specificato
 */
export default function formatDate(date, format = 'DD/MM/YYYY', locale = 'it-IT') {
  if (!date) return ''
  
  const dateObj = new Date(date)
  
  if (isNaN(dateObj.getTime())) return ''

  const options = {
    'DD/MM/YYYY': { day: '2-digit', month: '2-digit', year: 'numeric' },
    'MM/DD/YYYY': { month: '2-digit', day: '2-digit', year: 'numeric' },
    'YYYY-MM-DD': { year: 'numeric', month: '2-digit', day: '2-digit' },
    'DD MMM YYYY': { day: '2-digit', month: 'short', year: 'numeric' },
    'DD MMMM YYYY': { day: '2-digit', month: 'long', year: 'numeric' },
    'relative': { relative: true }
  }

  if (format === 'relative') {
    return formatRelativeTime(dateObj, locale)
  }

  const formatOptions = options[format] || options['DD/MM/YYYY']
  
  return dateObj.toLocaleDateString(locale, formatOptions)
}

/**
 * Formatta il tempo relativo (es. "2 ore fa", "ieri")
 */
function formatRelativeTime(date, locale = 'it-IT') {
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)
  
  const intervals = {
    anno: 31536000,
    mese: 2592000,
    settimana: 604800,
    giorno: 86400,
    ora: 3600,
    minuto: 60
  }

  for (const [unit, seconds] of Object.entries(intervals)) {
    const interval = Math.floor(diffInSeconds / seconds)
    
    if (interval >= 1) {
      return new Intl.RelativeTimeFormat(locale, { numeric: 'auto' })
        .format(-interval, unit)
    }
  }
  
  return 'ora'
}