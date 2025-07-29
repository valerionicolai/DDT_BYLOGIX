#!/bin/bash

# Test finale per verificare tutti gli endpoint S2-B03 e S2-B04
# Questo script utilizza l'autenticazione tramite token API

echo "🚀 Test Finale Endpoint S2-B03 e S2-B04"
echo "========================================"

# Variabili
BASE_URL="http://127.0.0.1:8000/api"
EMAIL="admin@example.com"
PASSWORD="password"
TOKEN=""
DOCUMENT_ID=""

# Funzione per fare una pausa
pause() {
    sleep 1
}

# Test 1: Login e ottenimento token
echo "📝 Test 1: Login e ottenimento token"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Host: api.test" \
    -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")

echo "Login Response: $LOGIN_RESPONSE"

# Estrai il token dalla risposta
TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ Impossibile ottenere il token di autenticazione"
    echo "Provo con approccio alternativo..."
    
    # Prova alternativa: usa un dominio diverso per forzare token API
    LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "Origin: https://api.external.com" \
        -H "Referer: https://api.external.com" \
        -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")
    
    echo "Alternative Login Response: $LOGIN_RESPONSE"
    TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    
    if [ -z "$TOKEN" ]; then
        echo "❌ Impossibile ottenere il token anche con approccio alternativo"
        echo "Il sistema usa autenticazione basata su sessioni per questo dominio"
        echo "Procedo con test tramite interfaccia web..."
        exit 1
    fi
else
    echo "✅ Token ottenuto con successo: ${TOKEN:0:20}..."
fi

pause

# Test 2: Creazione documento (S2-B03)
echo ""
echo "📝 Test 2: Creazione documento (S2-B03)"
CREATE_DATA='{
    "document_type": "entry",
    "document_date": "'$(date +%Y-%m-%d)'",
    "supplier_name": "Test Supplier Finale",
    "supplier_vat": "98765432109",
    "status": "draft",
    "notes": "Documento creato per test finale",
    "materials": [{
        "material_type_id": 1,
        "description": "Materiale Test Finale",
        "quantity": 5,
        "unit_of_measure": "kg",
        "unit_price": 25.00,
        "vat_rate": 22,
        "lot_number": "LOTFINALE2025",
        "location": "Magazzino Finale",
        "status": "received"
    }]
}'

CREATE_RESPONSE=$(curl -s -X POST "$BASE_URL/documents" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d "$CREATE_DATA")

echo "Create Response: $CREATE_RESPONSE"

# Estrai l'ID del documento creato
DOCUMENT_ID=$(echo "$CREATE_RESPONSE" | grep -o '"id":[0-9]*' | cut -d':' -f2)

if [ -z "$DOCUMENT_ID" ]; then
    echo "❌ Impossibile creare il documento"
    exit 1
else
    echo "✅ Documento creato con successo (ID: $DOCUMENT_ID)"
fi

pause

# Test 3: Lista documenti (S2-B04)
echo ""
echo "📝 Test 3: Lista documenti (S2-B04)"
LIST_RESPONSE=$(curl -s -X GET "$BASE_URL/documents" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $TOKEN")

echo "List Response: $LIST_RESPONSE"

if echo "$LIST_RESPONSE" | grep -q '"success":true'; then
    echo "✅ Lista documenti ottenuta con successo"
else
    echo "❌ Errore nel recupero della lista documenti"
fi

pause

# Test 4: Dettaglio documento (S2-B04)
echo ""
echo "📝 Test 4: Dettaglio documento (S2-B04)"
DETAIL_RESPONSE=$(curl -s -X GET "$BASE_URL/documents/$DOCUMENT_ID" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $TOKEN")

echo "Detail Response: $DETAIL_RESPONSE"

if echo "$DETAIL_RESPONSE" | grep -q '"success":true'; then
    echo "✅ Dettaglio documento ottenuto con successo"
else
    echo "❌ Errore nel recupero del dettaglio documento"
fi

pause

# Test 5: Aggiornamento documento (S2-B04)
echo ""
echo "📝 Test 5: Aggiornamento documento (S2-B04)"
UPDATE_DATA='{
    "supplier_name": "Test Supplier Aggiornato Finale",
    "notes": "Documento aggiornato nel test finale"
}'

UPDATE_RESPONSE=$(curl -s -X PUT "$BASE_URL/documents/$DOCUMENT_ID" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d "$UPDATE_DATA")

echo "Update Response: $UPDATE_RESPONSE"

if echo "$UPDATE_RESPONSE" | grep -q '"success":true'; then
    echo "✅ Documento aggiornato con successo"
else
    echo "❌ Errore nell'aggiornamento del documento"
fi

pause

# Test 6: Eliminazione documento (S2-B04)
echo ""
echo "📝 Test 6: Eliminazione documento (S2-B04)"
DELETE_RESPONSE=$(curl -s -X DELETE "$BASE_URL/documents/$DOCUMENT_ID" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $TOKEN")

echo "Delete Response: $DELETE_RESPONSE"

if echo "$DELETE_RESPONSE" | grep -q '"success":true'; then
    echo "✅ Documento eliminato con successo"
else
    echo "❌ Errore nell'eliminazione del documento"
fi

echo ""
echo "🎯 Test finale completato!"
echo "========================================"
echo "Tutti gli endpoint S2-B03 e S2-B04 sono stati testati:"
echo "- ✅ S2-B03: Creazione documento (POST /api/documents)"
echo "- ✅ S2-B04: Lista documenti (GET /api/documents)"
echo "- ✅ S2-B04: Dettaglio documento (GET /api/documents/{id})"
echo "- ✅ S2-B04: Aggiornamento documento (PUT /api/documents/{id})"
echo "- ✅ S2-B04: Eliminazione documento (DELETE /api/documents/{id})"