# Update Dashboard Changelog Command

This command updates the changelog table in the dashboard with recent commits and version information.

## Purpose:
Automatically insert new changelog entries into the HTML table in `resources/views/dashboard/index.blade.php` with proper row tags and formatting.

## Usage:
Run this command after preparing a release to update the dashboard changelog table with the latest changes.

---

## Command Implementation:

```bash
#!/bin/bash

# Update Dashboard Changelog Command
# This script updates the changelog table in the dashboard

set -e  # Exit on any error

echo "📋 Starting Dashboard Changelog Update..."

# Get the current version from .env.dev
if [[ -f ".env.dev" ]]; then
    CURRENT_VERSION=$(grep "APP_VERSION" .env.dev | cut -d'=' -f2 | tr -d ' ')
    echo "Current version from .env.dev: $CURRENT_VERSION"
else
    echo "⚠️  .env.dev file not found, cannot determine current version"
    exit 1
fi

# Get the release date
RELEASE_DATE=$(date +"%Y-%m-%d")
echo "Release date: $RELEASE_DATE"

# Find the last release tag to get commits since then
LATEST_RELEASE=$(git tag --sort=-v:refname | grep -E "v[0-9]+\.[0-9]+\.[0-9]+-release" | head -1 || echo "")
LATEST_STAGING=$(git tag --sort=-v:refname | grep -E "v[0-9]+\.[0-9]+\.[0-9]+-staging" | head -1 || echo "")

LAST_RELEASE_TAG=""
if [[ -n "$LATEST_RELEASE" ]]; then
    LAST_RELEASE_TAG="$LATEST_RELEASE"
elif [[ -n "$LATEST_STAGING" ]]; then
    LAST_RELEASE_TAG="$LATEST_STAGING"
fi

# Get commits since last release
if [[ -n "$LAST_RELEASE_TAG" ]]; then
    COMMITS=$(git log --oneline --pretty=format:"%s" "$LAST_RELEASE_TAG..HEAD" | head -10)
    echo "Commits since $LAST_RELEASE_TAG:"
else
    COMMITS=$(git log --oneline --pretty=format:"%s" | head -10)
    echo "Recent commits:"
fi

echo "$COMMITS"
echo ""

# Dashboard file path
DASHBOARD_FILE="resources/views/dashboard/index.blade.php"

if [[ ! -f "$DASHBOARD_FILE" ]]; then
    echo "❌ Dashboard file not found at $DASHBOARD_FILE"
    exit 1
fi

echo "📝 Updating dashboard changelog table..."

# Create a backup
cp "$DASHBOARD_FILE" "$DASHBOARD_FILE.backup"
echo "✅ Created backup: $DASHBOARD_FILE.backup"

# Generate the changelog entry
CHANGELOG_ENTRY=""
CHANGELOG_ENTRY+="                <tr>"$'\n'
CHANGELOG_ENTRY+="                    <td>$CURRENT_VERSION</td>"$'\n'
CHANGELOG_ENTRY+="                    <td>$RELEASE_DATE</td>"$'\n'
CHANGELOG_ENTRY+="                    <td>"$'\n'

# Add commits as list items
while IFS= read -r commit; do
    if [[ -n "$commit" ]]; then
        CHANGELOG_ENTRY+="                        <li>$commit</li>"$'\n'
    fi
done <<< "$COMMITS"

CHANGELOG_ENTRY+="                    </td>"$'\n'
CHANGELOG_ENTRY+="                </tr>"$'\n'

echo "Generated changelog entry:"
echo "$CHANGELOG_ENTRY"

# Find the changelog table in the file
# Look for the table header or first row to insert after
if grep -q "<thead>" "$DASHBOARD_FILE"; then
    # Insert after the thead section
    sed -i "/<\/thead>/a\\$CHANGELOG_ENTRY" "$DASHBOARD_FILE"
    echo "✅ Inserted changelog entry after table header"
elif grep -q "<tbody>" "$DASHBOARD_FILE"; then
    # Insert after tbody opening tag
    sed -i "/<tbody>/a\\$CHANGELOG_ENTRY" "$DASHBOARD_FILE"
    echo "✅ Inserted changelog entry after tbody opening"
elif grep -q "<tr>" "$DASHBOARD_FILE"; then
    # Insert after the first table row
    sed -i "0,/<tr>/! {0,/<tr>/s//<tr>\\n$CHANGELOG_ENTRY/}" "$DASHBOARD_FILE"
    echo "✅ Inserted changelog entry after first table row"
else
    echo "❌ Could not find suitable insertion point in the changelog table"
    echo "📋 Manual insertion required. Here's the entry to add:"
    echo ""
    echo "$CHANGELOG_ENTRY"
    exit 1
fi

echo ""
echo "🎉 Dashboard changelog updated successfully!"
echo "📋 Summary:"
echo "  - Version: $CURRENT_VERSION"
echo "  - Date: $RELEASE_DATE"
echo "  - Commits: $(echo "$COMMITS" | wc -l) entries added"
echo "  - Backup created: $DASHBOARD_FILE.backup"
echo ""
echo "Next steps:"
echo "  1. Review the changes in $DASHBOARD_FILE"
echo "  2. Test the dashboard to ensure the table displays correctly"
echo "  3. Commit the changes: git add $DASHBOARD_FILE && git commit -m 'Update dashboard changelog for v$CURRENT_VERSION'"
```

## Quick Commands:

### Run the dashboard changelog update:
```bash
bash .cursor/commands/update-dashboard-changelog.md
```

### After running, commit the changes:
```bash
git add resources/views/dashboard/index.blade.php
git commit -m "Update dashboard changelog for v1.6.8"
git push
```

## Features:
- ✅ **Automatic Version Detection**: Gets version from `.env.dev`
- ✅ **Smart Commit Detection**: Finds commits since last release
- ✅ **Proper HTML Structure**: Inserts with correct `<tr>`, `<td>`, `<li>` tags
- ✅ **Backup Creation**: Creates backup before making changes
- ✅ **Flexible Insertion**: Finds the best insertion point in the table
- ✅ **Error Handling**: Graceful handling of missing files or insertion points

## Requirements:
- `.env.dev` file with `APP_VERSION` set
- `resources/views/dashboard/index.blade.php` file with changelog table
- Git repository with proper commit history

## Table Structure Expected:
The command expects a changelog table with this structure:
```html
<table>
    <thead>
        <tr>
            <th>Version</th>
            <th>Date</th>
            <th>Changes</th>
        </tr>
    </thead>
    <tbody>
        <!-- New entries will be inserted here -->
    </tbody>
</table>
```

## Generated Entry Format:
```html
<tr>
    <td>1.6.8</td>
    <td>2025-10-15</td>
    <td>
        <li>Fix bulk user upload: handle missing job_id field</li>
        <li>Fix Laravel 5.1 lists() compatibility</li>
        <li>Add release notes for v1.6.7</li>
    </td>
</tr>
```
