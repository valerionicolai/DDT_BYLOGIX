#!/bin/bash

echo "🚀 Testing S2-B03 and S2-B04 Endpoints"
echo "======================================="

BASE_URL="http://127.0.0.1:8000/api"

# Step 1: Login
echo "📝 Step 1: Authentication"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}')

echo "Login Response: $LOGIN_RESPONSE"

# Extract token (assuming it's in 'access_token' field)
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    # Try 'token' field
    TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
fi

if [ -z "$TOKEN" ]; then
    echo "❌ Failed to extract token"
    exit 1
fi

echo "✅ Token extracted: ${TOKEN:0:20}..."

# Step 2: Create a document first
echo ""
echo "📝 Step 2: Create Entry Document"
CREATE_RESPONSE=$(curl -s -X POST "$BASE_URL/v1/documents" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
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

# Step 3: Test GET /api/v1/documents (S2-B03)
echo ""
echo "📝 Step 3: GET /api/v1/documents (List with pagination)"
LIST_RESPONSE=$(curl -s -X GET "$BASE_URL/v1/documents?page=1&per_page=10" \
  -H "Authorization: Bearer $TOKEN")

echo "List Response: $LIST_RESPONSE"

# Step 4: Test GET /api/v1/documents/{id} (S2-B03)
echo ""
echo "📝 Step 4: GET /api/v1/documents/$DOC_ID (Detail)"
DETAIL_RESPONSE=$(curl -s -X GET "$BASE_URL/v1/documents/$DOC_ID" \
  -H "Authorization: Bearer $TOKEN")

echo "Detail Response: $DETAIL_RESPONSE"

# Step 5: Test PUT /api/v1/documents/{id} (S2-B04)
echo ""
echo "📝 Step 5: PUT /api/v1/documents/$DOC_ID (Update)"
UPDATE_RESPONSE=$(curl -s -X PUT "$BASE_URL/v1/documents/$DOC_ID" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "document_number": "TEST-001-UPDATED",
    "document_date": "2025-01-29",
    "supplier": "Updated Supplier",
    "description": "Updated Test Document",
    "status": "draft"
  }')

echo "Update Response: $UPDATE_RESPONSE"

# Step 6: Test DELETE /api/v1/documents/{id} (S2-B04)
echo ""
echo "📝 Step 6: DELETE /api/v1/documents/$DOC_ID (Delete)"
DELETE_RESPONSE=$(curl -s -X DELETE "$BASE_URL/v1/documents/$DOC_ID" \
  -H "Authorization: Bearer $TOKEN")

echo "Delete Response: $DELETE_RESPONSE"

echo ""
echo "✅ All tests completed!"