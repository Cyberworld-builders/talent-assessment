# Database Initialization

This directory contains database initialization scripts and migrations for the talent assessment application.

## Files

- `init.sql` - MySQL initialization script that runs when the container starts for the first time
- `migrations/` - Laravel database migrations
- `seeds/` - Database seeders

## Database Permissions

The `init.sql` file automatically grants the necessary permissions to the `talent_user` database user, including:

- `CREATE` - For creating reseller databases
- `DROP` - For dropping reseller databases  
- `ALTER` - For modifying database schemas
- `INDEX` - For creating indexes
- Standard CRUD permissions (`INSERT`, `SELECT`, `UPDATE`, `DELETE`)
- Additional permissions for views, routines, triggers, etc.

## How It Works

1. When the MySQL container starts for the first time, it automatically runs all `.sql` files in `/docker-entrypoint-initdb.d/`
2. The `init.sql` file is mounted as `01-init.sql` to ensure it runs first
3. The script grants comprehensive permissions to `talent_user`
4. A test is performed to verify permissions work correctly

## Docker Compose Configuration

```yaml
volumes:
  - mysql_data:/var/lib/mysql
  - ./database/init.sql:/docker-entrypoint-initdb.d/01-init.sql
  - ./database/migrations:/docker-entrypoint-initdb.d/migrations
```

## Scripts

### Reset Database (Destroys existing data)
```bash
./scripts/reset-database.sh
```

### Test Permissions (Non-destructive)
```bash
./scripts/test-permissions.sh
```

## Important Notes

- The initialization script only runs when the MySQL data volume is empty (first startup)
- To re-run the initialization, you must remove the MySQL volume first
- The script includes a test that creates and drops a test database to verify permissions
- All permissions are granted to `talent_user@%` (any host)

## Troubleshooting

If you encounter permission errors:

1. Check if the initialization script ran: Look for "Database permissions initialized successfully" in MySQL logs
2. Test permissions: Run `./scripts/test-permissions.sh`
3. Reset database: Run `./scripts/reset-database.sh` (⚠️ This destroys existing data)
4. Check MySQL logs: `docker-compose logs mysql`
