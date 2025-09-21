#!/bin/bash

echo "🧪 Setting up CSRF Error Handling Testing Environment"
echo "====================================================="

# Check if Docker is running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Docker containers are not running. Starting them..."
    docker-compose up -d
    sleep 10
fi

# Check if application is accessible
echo "🔍 Checking application accessibility..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001 | grep -q "200\|302"; then
    echo "✅ Application is accessible at http://localhost:8001"
    APP_URL="http://localhost:8001"
else
    echo "❌ Application is not accessible. Please check Docker containers."
    exit 1
fi

# Run the PHP test
echo "🧪 Running CSRF error handling test..."
docker-compose exec app php test-csrf-handling.php

echo ""
echo "🎯 Testing Setup Complete!"
echo "========================="
echo ""
echo "📋 Next Steps:"
echo "1. Open http://localhost:8001/test-browser-csrf.html in your browser"
echo "2. Follow the test instructions on that page"
echo "3. Or manually test by:"
echo "   - Going to http://localhost:8001/login"
echo "   - Clearing cookies in browser dev tools"
echo "   - Trying to log in"
echo "   - You should see the CSRF error message"
echo ""
echo "🔧 Available Test Commands:"
echo "- docker-compose exec app php test-csrf-handling.php"
echo "- Open test-browser-csrf.html in browser"
echo "- Manual testing via browser dev tools"
echo ""
echo "✅ Ready for testing!"
