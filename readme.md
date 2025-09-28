## AOE Science Platform

Creating and delivering evidence-based talent solutions.

## Development Setup

This project uses Docker for local development. Follow these steps to get started:

### Prerequisites
- Docker and Docker Compose installed
- Git

### Quick Start
```bash
# Clone the repository
git clone <repository-url>
cd talent-assessment

# Build and start containers
docker compose up -d --build

# Install PHP dependencies
docker compose exec app composer install

# Generate application key
docker compose exec app php artisan key:generate

# Run database migrations
docker compose exec app php artisan migrate

# Seed the database
docker compose exec app php artisan db:seed

# Start development server
docker compose exec -d app php artisan serve --host=0.0.0.0 --port=8000
```

### Access the Application
- **Local Development**: http://localhost:8001
- **Staging**: https://talent-staging.cyberworldbuilders.dev
- **Production**: https://talent-aws.cyberworldbuilders.dev

### Common Commands
```bash
# View logs
docker compose logs

# Restart containers
docker compose restart

# Stop containers
docker compose down

# Run artisan commands
docker compose exec app php artisan <command>

# Access container shell
docker compose exec app bash
```

### Services
- **App**: PHP 7.4 Laravel application (port 8001)
- **MySQL**: Database (port 3306)
- **Redis**: Cache (port 6379)
- **Traefik**: Reverse proxy with SSL termination

## Environments

### Development Environment
- **Purpose**: Local development with hot reloading
- **Deployment**: Manual via volume mounts
- **Domain**: `talent-aws.cyberworldbuilders.dev`
- **Resources**: Shared with staging on same EC2 instance

### Staging Environment
- **Purpose**: Pre-production testing and validation
- **Deployment**: Automated via GitHub Actions
- **Domain**: `talent-staging.cyberworldbuilders.dev`
- **Resources**: Separate containers, databases, and S3 bucket
- **CI/CD**: Tests on PR, deploys on merge to `staging` branch

### Production Environment
- **Purpose**: Live production environment
- **Deployment**: Manual via SSH
- **Domain**: `talent.cyberworldbuilders.dev`
- **Resources**: Dedicated infrastructure

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).

## Testing

### Local Testing
Run the test suite locally using SQLite:

```bash
# Run all tests
docker compose exec app ./vendor/bin/phpunit

# Run specific test file
docker compose exec app ./vendor/bin/phpunit tests/IndustryTest.php
```

**Important**: This Laravel 5.1 application does NOT support `php artisan test` - always use `vendor/bin/phpunit` directly.

### CI/CD Pipeline
The GitHub Actions workflow uses MySQL for testing to ensure consistency with the production environment. The pipeline:
- Uses MySQL 5.7 service container
- Creates a fresh test database for each run
- Runs migrations and executes all tests
- Uses the `mysql_testing` database connection

### Test Configuration
- **Local**: Uses SQLite in-memory database (`:memory:`) for fast, isolated testing
- **CI/CD**: Uses MySQL service container with dedicated test database
- **Test Traits**: Uses `DatabaseTransactions` to rollback changes after each test

## AI Assistant Documentation

For AI assistants working on this project, see:
- **Workspace Context**: [.cursor/workspace-context.md](.cursor/workspace-context.md) - Comprehensive project context and constraints
- **Cursor Rules**: [.cursorrules](.cursorrules) - Quick reference for development guidelines

## Official Documentation

Documentation for the platform will be available to admins at [http://aoescience.com/docs](http://aoescience.com/docs).