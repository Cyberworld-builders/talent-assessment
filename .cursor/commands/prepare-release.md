# Prepare Release Command

This command automates the release preparation process by following these steps:

## Steps:
1. Find the latest tag number between dev, staging and release
2. Update the version number in the development environment file
3. Update the changelog in the dashboard/index.blade.php file to reflect the latest commits since the last release
4. Commit the changes and push to the remote repository
5. Create a new development tag

## Usage:
Run this command when preparing a new release to ensure all version numbers and changelogs are properly updated.

---

## Command Implementation:

```bash
#!/bin/bash

# Prepare Release Command
# This script automates the release preparation process

set -e  # Exit on any error

echo "🚀 Starting Release Preparation Process..."

# Step 1: Find the latest tag number between dev, staging and release
echo "📋 Step 1: Finding latest tag numbers..."

# Get all tags and sort them
LATEST_DEV=$(git tag --sort=-v:refname | grep -E "v[0-9]+\.[0-9]+\.[0-9]+-dev" | head -1 || echo "")
LATEST_STAGING=$(git tag --sort=-v:refname | grep -E "v[0-9]+\.[0-9]+\.[0-9]+-staging" | head -1 || echo "")
LATEST_RELEASE=$(git tag --sort=-v:refname | grep -E "v[0-9]+\.[0-9]+\.[0-9]+-release" | head -1 || echo "")

echo "Latest dev tag: $LATEST_DEV"
echo "Latest staging tag: $LATEST_STAGING"
echo "Latest release tag: $LATEST_RELEASE"

# Determine the next version number
if [[ -n "$LATEST_RELEASE" ]]; then
    # Extract version number from release tag (e.g., v1.6.7-release -> 1.6.7)
    CURRENT_VERSION=$(echo "$LATEST_RELEASE" | sed 's/v\([0-9]\+\.[0-9]\+\.[0-9]\+\).*/\1/')
    echo "Current release version: $CURRENT_VERSION"
    
    # Increment patch version
    IFS='.' read -r major minor patch <<< "$CURRENT_VERSION"
    NEW_VERSION="$major.$minor.$((patch + 1))"
else
    # If no release tag exists, start with 1.0.0
    NEW_VERSION="1.0.0"
fi

echo "Next version will be: $NEW_VERSION"

# Step 2: Update the version number in the development environment file
echo "📝 Step 2: Updating version number in development environment file..."

# Check if .env.dev file exists and update version
if [[ -f ".env.dev" ]]; then
    # Update or add APP_VERSION in .env.dev file
    if grep -q "APP_VERSION" .env.dev; then
        sed -i "s/APP_VERSION=.*/APP_VERSION=$NEW_VERSION/" .env.dev
    else
        echo "APP_VERSION=$NEW_VERSION" >> .env.dev
    fi
    echo "✅ Updated .env.dev with version $NEW_VERSION"
else
    echo "⚠️  .env.dev file not found, skipping version update"
fi

# Step 3: Generate changelog data for manual review
echo "📋 Step 3: Generating changelog data..."

# Find the last release tag to get commits since then
LAST_RELEASE_TAG=""
if [[ -n "$LATEST_RELEASE" ]]; then
    LAST_RELEASE_TAG="$LATEST_RELEASE"
elif [[ -n "$LATEST_STAGING" ]]; then
    LAST_RELEASE_TAG="$LATEST_STAGING"
else
    # If no release tags, get commits from the beginning
    LAST_RELEASE_TAG=""
fi

# Get commits since last release
if [[ -n "$LAST_RELEASE_TAG" ]]; then
    COMMITS=$(git log --oneline --pretty=format:"- %s" "$LAST_RELEASE_TAG..HEAD" | head -10)
else
    COMMITS=$(git log --oneline --pretty=format:"- %s" | head -10)
fi

echo "Recent commits since $LAST_RELEASE_TAG:"
echo "$COMMITS"
echo ""

# Step 3.5: Update dashboard changelog automatically
echo "📋 Step 3.5: Updating dashboard changelog table..."

# Check if the dashboard update command exists
DASHBOARD_UPDATE_SCRIPT=".cursor/commands/update-dashboard-changelog.md"
if [[ -f "$DASHBOARD_UPDATE_SCRIPT" ]]; then
    echo "🔄 Running dashboard changelog update..."
    bash "$DASHBOARD_UPDATE_SCRIPT"
    echo "✅ Dashboard changelog updated successfully"
else
    echo "⚠️  Dashboard update script not found at $DASHBOARD_UPDATE_SCRIPT"
    echo "📝 Manual dashboard update required"
fi

# Step 4: Commit the changes and push to remote repository
echo "💾 Step 4: Committing changes..."

# Add all changes
git add .

# Check if there are changes to commit
if [[ -n $(git status --porcelain) ]]; then
    git commit -m "Prepare release v$NEW_VERSION

- Updated version number to $NEW_VERSION
- Updated changelog with recent commits
- Automated release preparation

Recent changes:
$COMMITS"
    
    echo "✅ Changes committed"
    
    # Push to remote
    echo "📤 Pushing to remote repository..."
    git push origin HEAD
    
    echo "✅ Changes pushed to remote"
else
    echo "ℹ️  No changes to commit"
fi

# Step 5: Create a new development tag
echo "🏷️  Step 5: Creating new development tag..."

NEW_DEV_TAG="v$NEW_VERSION-dev"
git tag "$NEW_DEV_TAG"
echo "✅ Created development tag: $NEW_DEV_TAG"

# Push the tag
git push origin "$NEW_DEV_TAG"
echo "✅ Pushed development tag to remote"

echo ""
echo "🎉 Release preparation complete!"
echo "📋 Summary:"
echo "  - Next version: $NEW_VERSION"
echo "  - Development tag: $NEW_DEV_TAG"
echo "  - Changes committed and pushed"
echo ""
echo "Next steps:"
echo "  1. Test the development version"
echo "  2. Create staging tag: git tag v$NEW_VERSION-staging && git push origin v$NEW_VERSION-staging"
echo "  3. After staging tests, create release tag: git tag v$NEW_VERSION-release && git push origin v$NEW_VERSION-release"
```

## Quick Commands:

### Run the full release preparation:
```bash
bash .cursor/commands/prepare-release.md
```

### Manual steps after running the command:
```bash
# 1. Test development version
# 2. Create and push staging tag
git tag v1.6.8-staging && git push origin v1.6.8-staging

# 3. After staging tests, create and push release tag  
git tag v1.6.8-release && git push origin v1.6.8-release
```

### Optional: Run dashboard update separately
```bash
# If you need to update dashboard changelog independently
bash .cursor/commands/update-dashboard-changelog.md
```

## Features:
- ✅ Automatically finds latest version numbers
- ✅ Increments version number appropriately
- ✅ Updates environment files
- ✅ Generates changelog from git commits
- ✅ Commits and pushes changes
- ✅ Creates development tag
- ✅ Provides clear next steps

## Requirements:
- Git repository with proper tagging
- .env file for version tracking
- Proper git remote configuration