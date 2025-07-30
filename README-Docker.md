# Docker Setup for Talent Assessment Application

This document provides instructions for setting up the Talent Assessment Application using Docker.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Clone the repository** (if not already done)
   ```bash
   git clone <repository-url>
   cd talent-assessment
   ```

2. **Run the setup script**
   ```bash
   ./docker-setup.sh
   ```

   This script will:
   - Create a `.env` file from `.env.example`
   - Build and start Docker containers
   - Install PHP and Node.js dependencies
   - Run database migrations and seeders
   - Set proper permissions

3. **Access the application**
   - Main application: http://localhost:8000
   - Default test user: `test` (password: `test`)

## Manual Setup

If you prefer to set up manually:

1. **Create environment file**
   ```bash
   cp .env.example .env
   ```

2. **Build and start containers**
   ```bash
   docker compose up -d --build
   ```

3. **Install dependencies**
   ```bash
   docker compose exec app composer install
   docker compose exec app npm install
   ```

4. **Build assets**
   ```bash
   docker compose exec app npm run gulp
   ```

5. **Generate application key**
   ```bash
   docker compose exec app php artisan key:generate
   ```

6. **Run migrations and seeders**
   ```bash
   docker compose exec app php artisan migrate
   docker compose exec app php artisan db:seed --class=DatabaseSeeder
   ```

## Development

### Hot Reload
The application is configured for hot reloading. Any changes to PHP files will be reflected immediately due to the volume mounting.

### Running Commands
All Laravel commands should be run inside the container:
```bash
docker compose exec app php artisan <command>
```

### Database Access
- **Host**: localhost
- **Port**: 3306
- **Database**: talent_assessment
- **Username**: talent_user
- **Password**: talent_password

### Redis Access
- **Host**: localhost
- **Port**: 6379

## Services

The Docker Compose setup includes:

- **app**: Laravel application (PHP 7.4)
- **mysql**: MySQL 5.7 database
- **redis**: Redis 6 cache/session store

## Troubleshooting

### Permission Issues
If you encounter permission issues:
```bash
docker compose exec app chmod -R 755 storage
docker compose exec app chmod -R 755 bootstrap/cache
```

### Database Connection Issues
Ensure the database is ready before running migrations:
```bash
docker compose logs mysql
```

### Container Access
To access the container shell:
```bash
docker compose exec app bash
```

### Rebuilding
To rebuild the containers:
```bash
docker compose down
docker compose up -d --build
```

## Environment Variables

Key environment variables in `.env`:
- `DB_HOST=mysql`
- `DB_DATABASE=talent_assessment`
- `DB_USERNAME=talent_user`
- `DB_PASSWORD=talent_password`
- `REDIS_HOST=redis`
- `REDIS_PORT=6379`

## Notes

- The application uses wkhtmltopdf for PDF generation
- Node.js and npm are included for frontend asset compilation
- All development tools (vim, nano) are available in the container
- The setup includes all necessary PHP extensions for Laravel 5.1 