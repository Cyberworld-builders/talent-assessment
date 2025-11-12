# Prepare Release Command

1. Get the latest tag number across all environments to determine the next version number.
2. Update the changelog on the dashboard using the new version number.
3. Commit and push.
4. Create a staging tag using the latest tag number.

echo "Prepare a release by following these steps:"

echo "Get the latest tag number across all environments to determine the next version number."

echo "Update the changelog on the dashboard (resources/views/dashboard/index.blade.php) using the new version number. Make sure to update the version number in the .env.dev file as well. Keep the changelog in the same format as the existing changelog."

echo "Commit and push the changes."

echo "Create a staging tag using the latest tag number. Use the convention v1.5.28-staging."

echo "Deploy the staging environment by pushing the staging tag.
