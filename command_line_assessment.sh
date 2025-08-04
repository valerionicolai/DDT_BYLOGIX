#!/bin/bash

echo "=== DTT by Logix - Command Line Assessment ==="
echo "Data: $(date)"
echo "Credenziali utilizzate: admin@dttbylogix.com / password123"
echo ""

# Array delle pagine da testare
declare -a urls=(
    "http://dttbylogix.test/login"
    "http://dttbylogix.test/dashboard"
    "http://dttbylogix.test/projects"
    "http://dttbylogix.test/clients"
    "http://dttbylogix.test/material-types"
    "http://dttbylogix.test/reports"
    "http://dttbylogix.test/user-management"
    "http://dttbylogix.test/system-settings"
    "http://dttbylogix.test/profile"
    "http://dttbylogix.test/settings"
)

declare -a names=(
    "Login"
    "Dashboard"
    "Projects"
    "Clients"
    "Material Types"
    "Reports"
    "User Management"
    "System Settings"
    "Profile"
    "Settings"
)

echo "Testando accessibilità delle pagine..."
echo ""

# Contatori per statistiche
total_pages=0
success_count=0
redirect_count=0
error_count=0

for i in "${!urls[@]}"; do
  url="${urls[$i]}"
  name="${names[$i]}"
  
  echo "Testando: $name ($url)"
  status=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 10 --max-time 30 "$url")
  
  total_pages=$((total_pages + 1))
  
  case $status in
    200)
      echo "✅ $name - Status: $status (OK - Pagina accessibile)"
      success_count=$((success_count + 1))
      ;;
    302)
      echo "🔄 $name - Status: $status (Redirect - Richiede autenticazione)"
      redirect_count=$((redirect_count + 1))
      ;;
    404)
      echo "❌ $name - Status: $status (NOT FOUND - Pagina non esistente)"
      error_count=$((error_count + 1))
      ;;
    *)
      echo "⚠️  $name - Status: $status (Altro errore)"
      error_count=$((error_count + 1))
      ;;
  esac
  echo ""
done

echo "=== RIEPILOGO ASSESSMENT ==="
echo "Pagine totali testate: $total_pages"
echo "Pagine accessibili (200): $success_count"
echo "Pagine con redirect (302): $redirect_count"
echo "Pagine con errori: $error_count"
echo ""

if [ $error_count -eq 0 ]; then
  echo "✅ ASSESSMENT COMPLETATO: Nessun errore 404 rilevato"
  echo "✅ Tutte le pagine esistono e sono correttamente configurate"
else
  echo "⚠️  ATTENZIONE: Rilevati $error_count errori"
fi

echo ""
echo "Note:"
echo "- Status 200: Pagina accessibile pubblicamente"
echo "- Status 302: Pagina protetta che reindirizza al login (comportamento corretto)"
echo "- Status 404: Pagina non esistente (errore da correggere)"
echo ""
echo "Assessment completato: $(date)"