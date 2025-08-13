# GitHub Actions Workflows

This directory contains GitHub Actions workflows for continuous integration.

## Available Workflows

### `tests.yml` - Test Suite
- **Trigger**: Push to main branch or pull requests
- **Database**: SQLite (file-based)
- **PHP Version**: 7.4
- **Features**: Fast, reliable test environment

## Workflow Steps

The workflow follows these steps:

1. **Checkout**: Clone the repository
2. **Setup PHP**: Install PHP 7.4 with required extensions
3. **Environment**: Copy .env.example and configure
4. **Dependencies**: Install Composer dependencies
5. **Laravel Setup**: Generate app key and set permissions
6. **Database**: Configure SQLite and run migrations
7. **Seeding**: Populate database with test data
8. **Testing**: Run PHPUnit test suite

## Test Results

- ✅ **Passing**: All tests pass, PR can be merged
- ❌ **Failing**: Tests fail, review required before merge

## Local Testing

To test workflows locally before pushing:

```bash
# Run tests in Docker (same as CI)
docker compose exec app ./vendor/bin/phpunit

# Test with SQLite (like CI workflow)
docker compose exec app php artisan config:cache
docker compose exec app ./vendor/bin/phpunit
```

## Troubleshooting

### Common Issues

1. **Database Connection**: Ensure SQLite file is created and writable
2. **Permissions**: Check storage and bootstrap/cache permissions
3. **Dependencies**: Verify composer.json is up to date
4. **Environment**: Confirm .env.example exists and is valid

### Debugging

- Check GitHub Actions logs for detailed error messages
- Test locally with Docker before pushing
- Verify SQLite database file exists and has proper permissions
