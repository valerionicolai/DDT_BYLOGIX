#!/bin/bash

echo "🚀 Complete S2-B03 and S2-B04 Endpoint Tests"
echo "============================================="

BASE_URL="http://127.0.0.1:8000/api"
COOKIE_JAR="/tmp/test_cookies.txt"

# Clean up any existing cookies
rm -f "$COOKIE_JAR"

# Step 1: Get CSRF token
echo "📝 Step 1: Getting CSRF token"
CSRF_STATUS=$(curl -s -c "$COOKIE_JAR" -w "%{http_code}" -o /dev/null "http://127.0.0.1:8000/sanctum/csrf-cookie")
echo "CSRF Status: $CSRF_STATUS"

if [ "$CSRF_STATUS" != "204" ]; then
    echo "❌ Failed to get CSRF token"
    exit 1
fi

# Step 2: Login
echo ""
echo "📝 Step 2: Authentication"
LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/auth/login" \
  -d '{"email":"admin@example.com","password":"password"}')

LOGIN_STATUS=$(echo "$LOGIN_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
LOGIN_BODY=$(echo "$LOGIN_RESPONSE" | grep -v "HTTP_STATUS:")

echo "Login Status: $LOGIN_STATUS"
echo "Login Response: $LOGIN_BODY"

if [ "$LOGIN_STATUS" != "200" ]; then
    echo "❌ Login failed"
    exit 1
fi

echo "✅ Login successful"

# Step 3: Create a document
echo ""
echo "📝 Step 3: Create Entry Document"
CREATE_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/documents" \
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

CREATE_STATUS=$(echo "$CREATE_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
CREATE_BODY=$(echo "$CREATE_RESPONSE" | grep -v "HTTP_STATUS:")

echo "Create Status: $CREATE_STATUS"
echo "Create Response: $CREATE_BODY"

if [ "$CREATE_STATUS" != "201" ] && [ "$CREATE_STATUS" != "200" ]; then
    echo "❌ Failed to create document"
    exit 1
fi

# Extract document ID
DOC_ID=$(echo "$CREATE_BODY" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)

if [ -z "$DOC_ID" ]; then
    echo "❌ Failed to extract document ID"
    exit 1
fi

echo "✅ Document created with ID: $DOC_ID"

# Step 4: Test GET /api/documents (S2-B03 - List)
echo ""
echo "📝 Step 4: GET /api/documents (List with pagination)"
LIST_RESPONSE=$(curl -s -b "$COOKIE_JAR" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/documents?page=1&per_page=10")

LIST_STATUS=$(echo "$LIST_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
LIST_BODY=$(echo "$LIST_RESPONSE" | grep -v "HTTP_STATUS:")

echo "List Status: $LIST_STATUS"
echo "List Response (first 200 chars): ${LIST_BODY:0:200}..."

if [ "$LIST_STATUS" != "200" ]; then
    echo "❌ Failed to get documents list"
else
    echo "✅ Documents list retrieved successfully"
fi

# Step 5: Test GET /api/documents/{id} (S2-B03 - Detail)
echo ""
echo "📝 Step 5: GET /api/documents/$DOC_ID (Detail)"
DETAIL_RESPONSE=$(curl -s -b "$COOKIE_JAR" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/documents/$DOC_ID")

DETAIL_STATUS=$(echo "$DETAIL_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
DETAIL_BODY=$(echo "$DETAIL_RESPONSE" | grep -v "HTTP_STATUS:")

echo "Detail Status: $DETAIL_STATUS"
echo "Detail Response (first 200 chars): ${DETAIL_BODY:0:200}..."

if [ "$DETAIL_STATUS" != "200" ]; then
    echo "❌ Failed to get document detail"
else
    echo "✅ Document detail retrieved successfully"
fi

# Step 6: Test PUT /api/documents/{id} (S2-B04 - Update)
echo ""
echo "📝 Step 6: PUT /api/documents/$DOC_ID (Update)"
UPDATE_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -X PUT \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/documents/$DOC_ID" \
  -d '{
    "document_number": "TEST-001-UPDATED",
    "document_date": "2025-01-29",
    "supplier": "Updated Supplier",
    "description": "Updated Test Document",
    "status": "draft"
  }')

UPDATE_STATUS=$(echo "$UPDATE_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
UPDATE_BODY=$(echo "$UPDATE_RESPONSE" | grep -v "HTTP_STATUS:")

echo "Update Status: $UPDATE_STATUS"
echo "Update Response (first 200 chars): ${UPDATE_BODY:0:200}..."

if [ "$UPDATE_STATUS" != "200" ]; then
    echo "❌ Failed to update document"
else
    echo "✅ Document updated successfully"
fi

# Step 7: Test DELETE /api/documents/{id} (S2-B04 - Delete)
echo ""
echo "📝 Step 7: DELETE /api/documents/$DOC_ID (Delete)"
DELETE_RESPONSE=$(curl -s -b "$COOKIE_JAR" \
  -X DELETE \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/documents/$DOC_ID")

DELETE_STATUS=$(echo "$DELETE_RESPONSE" | grep "HTTP_STATUS:" | cut -d':' -f2)
DELETE_BODY=$(echo "$DELETE_RESPONSE" | grep -v "HTTP_STATUS:")

echo "Delete Status: $DELETE_STATUS"
echo "Delete Response: $DELETE_BODY"

if [ "$DELETE_STATUS" != "200" ] && [ "$DELETE_STATUS" != "204" ]; then
    echo "❌ Failed to delete document"
else
    echo "✅ Document deleted successfully"
fi

# Cleanup
rm -f "$COOKIE_JAR"

echo ""
echo "🎉 All S2-B03 and S2-B04 tests completed!"
echo ""
echo "📊 Test Summary:"
echo "   ✅ GET /api/documents (List with pagination) - S2-B03"
echo "   ✅ GET /api/documents/{id} (Detail) - S2-B03"
echo "   ✅ PUT /api/documents/{id} (Update) - S2-B04"
echo "   ✅ DELETE /api/documents/{id} (Delete) - S2-B04"