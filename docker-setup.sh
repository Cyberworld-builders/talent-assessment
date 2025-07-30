#!/bin/bash

# Docker setup script for Talent Assessment Application

echo "Setting up Docker environment for Talent Assessment Application..."

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cat > .env << 'EOF'
APP_NAME=TalentAssessment
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=talent_assessment
DB_USERNAME=talent_user
DB_PASSWORD=talent_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
EOF
fi

# Set proper permissions for storage and bootstrap/cache directories
echo "Setting up directories and permissions..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Build and start the containers
echo "Building and starting Docker containers..."
docker compose up -d --build

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 30

# Generate application key
echo "Generating application key..."
docker compose exec app php artisan key:generate

# Run database migrations
echo "Running database migrations..."
docker compose exec app php artisan migrate

# Seed the database
echo "Seeding the database..."
docker compose exec app php artisan db:seed --class=DatabaseSeeder

# Set proper permissions
echo "Setting proper permissions..."
docker compose exec app chmod -R 755 storage
docker compose exec app chmod -R 755 bootstrap/cache

# Now start the server
echo "Starting the application server..."
docker compose exec -d app php artisan serve --host=0.0.0.0 --port=8000

echo "Setup complete! The application should be available at http://localhost:8000"
echo "Default test user: test (password: test)" 