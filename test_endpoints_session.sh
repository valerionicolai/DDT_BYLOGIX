#!/bin/bash

echo "🚀 Testing S2-B03 and S2-B04 Endpoints (Session-based)"
echo "======================================================"

BASE_URL="http://127.0.0.1:8000/api"
COOKIE_JAR="/tmp/cookies.txt"

# Step 1: Get CSRF token
echo "📝 Step 1: Getting CSRF token"
curl -s -c "$COOKIE_JAR" "$BASE_URL/../sanctum/csrf-cookie"

# Step 2: Login
echo "📝 Step 2: Authentication"
LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d '{"email":"admin@example.com","password":"password"}')

echo "Login Response: $LOGIN_RESPONSE"

# Check if login was successful
if echo "$LOGIN_RESPONSE" | grep -q '"success":true'; then
    echo "✅ Login successful"
else
    echo "❌ Login failed"
    exit 1
fi

# Step 3: Create a document first
echo ""
echo "📝 Step 3: Create Entry Document"
CREATE_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST "$BASE_URL/documents" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d '{
    "document_number": "TEST-001",
    "document_date": "2025-01-29",
    "supplier": "Test Supplier",
    "description": "Test Document",
    "status": "draft",
    "materials": [
      {
        "material_type_id": 16,
        "quantity": 10,
        "unit_price": 15.50,
        "description": "Test Material"
      }
    ]
  }')

echo "Create Response: $CREATE_RESPONSE"

# Extract document ID
DOC_ID=$(echo $CREATE_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)

if [ -z "$DOC_ID" ]; then
    echo "❌ Failed to create document"
    exit 1
fi

echo "✅ Document created with ID: $DOC_ID"

# Step 4: Test GET /api/v1/documents (S2-B03)
echo ""
echo "📝 Step 4: GET /api/documents (List with pagination)"
LIST_RESPONSE=$(curl -s -b "$COOKIE_JAR" -X GET "$BASE_URL/documents?page=1&per_page=10" \
  -H "X-Requested-With: XMLHttpRequest")

echo "List Response: $LIST_RESPONSE"

# Step 5: Test GET /api/v1/documents/{id} (S2-B03)
echo ""
echo "📝 Step 5: GET /api/documents/$DOC_ID (Detail)"
DETAIL_RESPONSE=$(curl -s -b "$COOKIE_JAR" -X GET "$BASE_URL/documents/$DOC_ID" \
  -H "X-Requested-With: XMLHttpRequest")

echo "Detail Response: $DETAIL_RESPONSE"

# Step 6: Test PUT /api/v1/documents/{id} (S2-B04)
echo ""
echo "📝 Step 6: PUT /api/documents/$DOC_ID (Update)"
UPDATE_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X PUT "$BASE_URL/documents/$DOC_ID" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d '{
    "document_number": "TEST-001-UPDATED",
    "document_date": "2025-01-29",
    "supplier": "Updated Supplier",
    "description": "Updated Test Document",
    "status": "draft"
  }')

echo "Update Response: $UPDATE_RESPONSE"

# Step 7: Test DELETE /api/v1/documents/{id} (S2-B04)
echo ""
echo "📝 Step 7: DELETE /api/documents/$DOC_ID (Delete)"
DELETE_RESPONSE=$(curl -s -b "$COOKIE_JAR" -X DELETE "$BASE_URL/documents/$DOC_ID" \
  -H "X-Requested-With: XMLHttpRequest")

echo "Delete Response: $DELETE_RESPONSE"

# Cleanup
rm -f "$COOKIE_JAR"

echo ""
echo "✅ All tests completed!"