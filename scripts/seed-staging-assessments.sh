#!/bin/bash

# Script to seed new assessments on staging environment
# This script loads environment variables from AWS Secrets Manager like the deployment script

set -e

echo "Starting staging assessment seeding..."

# Check if we're on the staging server
if [ ! -f "/opt/talent-assessment/docker-compose.staging.yml" ]; then
    echo "ERROR: This script must be run on the staging server"
    exit 1
fi

cd /opt/talent-assessment

# Export AWS region
export AWS_DEFAULT_REGION=us-east-2

# Fetch secrets and set server environment variables
echo "Fetching secrets from AWS Secrets Manager..."
aws secretsmanager get-secret-value --secret-id talent-assessment-staging-secrets --region us-east-2 --query SecretString --output text > secrets.json

# Set server environment variables for docker-compose
echo "Setting server environment variables..."
export STAGING_DB_DATABASE=$(jq -r '.STAGING_DB_DATABASE' secrets.json)
export STAGING_DB_USERNAME=$(jq -r '.STAGING_DB_USERNAME' secrets.json)
export STAGING_DB_PASSWORD=$(jq -r '.STAGING_DB_PASSWORD' secrets.json)
export STAGING_DB_ROOT_PASSWORD=$(jq -r '.STAGING_DB_ROOT_PASSWORD' secrets.json)
export STAGING_REDIS_PASSWORD=$(jq -r '.STAGING_REDIS_PASSWORD' secrets.json)
export STAGING_S3_BUCKET=$(jq -r '.STAGING_S3_BUCKET' secrets.json)
export STAGING_APP_KEY=$(jq -r '.STAGING_APP_KEY' secrets.json)

# Set mail configuration for email functionality
echo "Setting mail configuration for email..."
export STAGING_SES_REGION=$(jq -r '.STAGING_SES_REGION' secrets.json)
export STAGING_SES_FROM_ADDRESS=$(jq -r '.STAGING_SES_FROM_ADDRESS' secrets.json)
export STAGING_MAIL_FROM_ADDRESS=$STAGING_SES_FROM_ADDRESS
export STAGING_MAIL_FROM_NAME="Talent Assessment Staging"

# Generate APP_KEY properly (32-byte key encoded in base64, but shorter format like Laravel artisan)
export STAGING_APP_KEY=$(openssl rand -base64 24 | tr -d '\n')
echo "Generated APP_KEY: $STAGING_APP_KEY"

# Clean up secrets file
rm secrets.json

# Set image environment variable (use current running image)
echo "Setting image environment variable..."
CURRENT_IMAGE=$(docker inspect talent-assessment-app-staging --format='{{.Config.Image}}' 2>/dev/null || echo "")
if [ -z "$CURRENT_IMAGE" ]; then
    echo "ERROR: Could not determine current app image. Is the staging app container running?"
    exit 1
fi
export STAGING_APP_IMAGE="$CURRENT_IMAGE"
echo "Using current image: $STAGING_APP_IMAGE"

# Check if services are running
echo "Checking if staging services are running..."
if ! docker-compose -f docker-compose.staging.yml ps | grep -q "Up"; then
    echo "ERROR: Staging services are not running. Please start them first."
    exit 1
fi

# Check current database state
echo "Checking current database state..."
echo "Current assessments:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Assessments: ' . \App\Assessment::count() . PHP_EOL;
"

echo "Current dimensions:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Dimensions: ' . \App\Dimension::count() . PHP_EOL;
"

echo "Current questions:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Questions: ' . \App\Question::count() . PHP_EOL;
"

# Seed Involved-Leader Assessment
echo "Seeding Involved-Leader Assessment..."
docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan db:seed --class=InvolvedLeaderAssessmentSeeder

# Seed Involved-Blockers Assessment
echo "Seeding Involved-Blockers Assessment..."
docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan db:seed --class=InvolvedBlockersAssessmentSeeder

# Verify final state
echo "Verifying final database state..."
echo "Final assessments:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Assessments: ' . \App\Assessment::count() . PHP_EOL;
"

echo "Final dimensions:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Dimensions: ' . \App\Dimension::count() . PHP_EOL;
"

echo "Final questions:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Questions: ' . \App\Question::count() . PHP_EOL;
"

echo "Assessment names:"
docker-compose -f docker-compose.staging.yml exec -T app-staging php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\App\Assessment::all(['id', 'name'])->each(function(\$a) { 
    echo \$a->id . ': ' . \$a->name . PHP_EOL; 
});
"

echo "Staging assessment seeding completed successfully!"
echo "All three assessments should now be available on staging."
