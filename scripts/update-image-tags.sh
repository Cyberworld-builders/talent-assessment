#!/bin/bash

# Script to update Docker image tags in environment files
# Usage: ./scripts/update-image-tags.sh <environment> <image_tag>
# Example: ./scripts/update-image-tags.sh production v1.3.5-release

set -e

ENVIRONMENT=$1
IMAGE_TAG=$2
ECR_REGISTRY="068732175988.dkr.ecr.us-east-2.amazonaws.com"

if [ -z "$ENVIRONMENT" ] || [ -z "$IMAGE_TAG" ]; then
    echo "Usage: $0 <environment> <image_tag>"
    echo "Example: $0 production v1.3.5-release"
    echo "Example: $0 staging v1.3.5-staging"
    exit 1
fi

if [ "$ENVIRONMENT" = "production" ]; then
    ENV_FILE=".env.production"
    IMAGE_VAR="PRODUCTION_APP_IMAGE"
elif [ "$ENVIRONMENT" = "staging" ]; then
    ENV_FILE=".env.staging"
    IMAGE_VAR="STAGING_APP_IMAGE"
else
    echo "Error: Environment must be 'production' or 'staging'"
    exit 1
fi

if [ ! -f "$ENV_FILE" ]; then
    echo "Error: $ENV_FILE not found"
    exit 1
fi

NEW_IMAGE="$ECR_REGISTRY/talent-assessment-app:$IMAGE_TAG"

echo "Updating $IMAGE_VAR in $ENV_FILE to: $NEW_IMAGE"

# Update the image variable in the environment file
if grep -q "^$IMAGE_VAR=" "$ENV_FILE"; then
    sed -i "s|^$IMAGE_VAR=.*|$IMAGE_VAR=$NEW_IMAGE|" "$ENV_FILE"
else
    echo "$IMAGE_VAR=$NEW_IMAGE" >> "$ENV_FILE"
fi

echo "Successfully updated $ENV_FILE"
echo "New image: $NEW_IMAGE"

# Verify the update
echo "Verification:"
grep "^$IMAGE_VAR=" "$ENV_FILE"
