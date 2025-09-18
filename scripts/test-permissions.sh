#!/bin/bash

# Script to test database permissions without destroying data

echo "🔍 Testing database permissions..."

# Test CREATE permission
echo "📊 Testing CREATE DATABASE permission..."
docker-compose exec mysql mysql -u talent_user -ptalent_password -e "CREATE DATABASE IF NOT EXISTS test_create_permission; DROP DATABASE IF EXISTS test_create_permission;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ CREATE DATABASE permission: OK"
else
    echo "❌ CREATE DATABASE permission: FAILED"
fi

# Test DROP permission
echo "📊 Testing DROP DATABASE permission..."
docker-compose exec mysql mysql -u talent_user -ptalent_password -e "CREATE DATABASE IF NOT EXISTS test_drop_permission; DROP DATABASE IF EXISTS test_drop_permission;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ DROP DATABASE permission: OK"
else
    echo "❌ DROP DATABASE permission: FAILED"
fi

# Test ALTER permission
echo "📊 Testing ALTER TABLE permission..."
docker-compose exec mysql mysql -u talent_user -ptalent_password talent_assessment -e "CREATE TABLE IF NOT EXISTS test_alter (id INT); ALTER TABLE test_alter ADD COLUMN name VARCHAR(50); DROP TABLE IF EXISTS test_alter;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ ALTER TABLE permission: OK"
else
    echo "❌ ALTER TABLE permission: FAILED"
fi

# Show current user privileges
echo "📋 Current user privileges:"
docker-compose exec mysql mysql -u talent_user -ptalent_password -e "SHOW GRANTS FOR 'talent_user'@'%';"

echo "✅ Permission test complete!"
