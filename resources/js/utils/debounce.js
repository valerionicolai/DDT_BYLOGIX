/**
 * Debounce function - ritarda l'esecuzione di una funzione
 * fino a quando non sono passati `delay` millisecondi dall'ultima chiamata
 */
export default function debounce(func, delay = 300) {
  let timeoutId
  
  return function (...args) {
    clearTimeout(timeoutId)
    
    timeoutId = setTimeout(() => {
      func.apply(this, args)
    }, delay)
  }
}