# GitHub Actions Workflows

This directory contains GitHub Actions workflows for automated testing and deployment.

## Workflows

### `tests.yml`
Runs the Laravel test suite using MySQL service container.

**Features:**
- Uses MySQL 5.7 service for consistent database testing
- Fresh database for each test run
- Automatic migration and seeding
- Comprehensive test coverage

**Configuration:**
- **Database**: MySQL service container with `testing` database
- **Connection**: Uses `mysql_testing` connection from `config/database.php`
- **Environment**: `APP_ENV=testing` with proper cache clearing

## Troubleshooting

### Common Issues

1. **Database Connection Issues**
   - Ensure MySQL service is healthy before running tests
   - Check that `mysql_testing` connection is properly configured
   - Verify environment variables are set correctly

2. **Test Failures**
   - Tests use `DatabaseTransactions` trait for automatic rollback
   - Each test should be independent and not rely on previous test data
   - Use factories for test data creation

3. **Environment Issues**
   - Clear all caches before running tests
   - Ensure proper file permissions on storage and bootstrap/cache
   - Check that all required PHP extensions are installed

### Local vs CI Differences

- **Local**: Uses SQLite for faster development testing
- **CI**: Uses MySQL for production-like environment testing
- Both environments should produce the same test results
