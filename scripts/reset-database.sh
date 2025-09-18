#!/bin/bash

# Script to reset the database with proper permissions
# This will destroy existing data and recreate with correct permissions

echo "🔄 Resetting database with proper permissions..."

# Stop containers
echo "📦 Stopping containers..."
docker-compose down

# Remove MySQL volume to ensure clean initialization
echo "🗑️  Removing existing MySQL data..."
docker volume rm talent-assessment_mysql_data 2>/dev/null || true

# Start containers (this will trigger the init.sql script)
echo "🚀 Starting containers with fresh database..."
docker-compose up -d mysql

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to initialize..."
sleep 10

# Check if MySQL is ready
echo "🔍 Checking MySQL status..."
until docker-compose exec mysql mysqladmin ping -h localhost --silent; do
    echo "Waiting for MySQL to be ready..."
    sleep 2
done

echo "✅ MySQL is ready!"

# Run migrations
echo "📊 Running Laravel migrations..."
docker-compose exec app php artisan migrate --force

# Run seeders
echo "🌱 Running database seeders..."
docker-compose exec app php artisan db:seed --force

# Start all services
echo "🚀 Starting all services..."
docker-compose up -d

echo "✅ Database reset complete with proper permissions!"
echo "🔧 The talent_user now has CREATE privileges for reseller database operations."
