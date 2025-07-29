#!/bin/bash

echo "🧪 Simple Test - Step by Step"
echo "============================="

BASE_URL="http://127.0.0.1:8000/api"
COOKIE_JAR="/tmp/simple_cookies.txt"

# Clean up any existing cookies
rm -f "$COOKIE_JAR"

echo "📝 Step 1: Test simple GET request"
SIMPLE_GET=$(curl -s -w "%{http_code}" -o /dev/null "$BASE_URL/auth/user")
echo "GET /api/auth/user - Status: $SIMPLE_GET"

echo ""
echo "📝 Step 2: Get CSRF token"
CSRF_RESPONSE=$(curl -s -c "$COOKIE_JAR" -w "%{http_code}" -o /dev/null "http://127.0.0.1:8000/sanctum/csrf-cookie")
echo "CSRF token request - Status: $CSRF_RESPONSE"

if [ -f "$COOKIE_JAR" ]; then
    echo "✅ Cookie file created"
    echo "Cookie contents:"
    cat "$COOKIE_JAR"
else
    echo "❌ Cookie file not created"
fi

echo ""
echo "📝 Step 3: Test login"
LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -w "\nHTTP_STATUS:%{http_code}" \
  "$BASE_URL/auth/login" \
  -d '{"email":"admin@example.com","password":"password"}')

echo "Login response:"
echo "$LOGIN_RESPONSE"

# Cleanup
rm -f "$COOKIE_JAR"

echo ""
echo "✅ Simple test completed!"