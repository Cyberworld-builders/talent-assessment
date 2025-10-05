# Laravel Development Tools - Quick Start Guide

## Basic Usage

### Using the laravel-dev script:
```bash
# Check status
laravel-dev status

# Install dependencies
laravel-dev install

# Full development setup
laravel-dev setup

# Run tests
laravel-dev test

# Compile assets
laravel-dev gulp

# View logs
laravel-dev logs

# Get help
laravel-dev help
```

### Using shell aliases:
```bash
# Quick commands (after restarting shell or running 'source ~/.bashrc')
ldev                    # Go to project directory
lstatus                 # Check Docker status
lclear                  # Clear caches
ltest                   # Run tests
lg                      # Compile assets
ll                      # View logs
ls                      # Open container shell
```

### Using the MCP server (advanced):
```bash
# Start the MCP server
python3 scripts/mcp_laravel_server.py

# The server provides tools for AI assistants to interact with your Laravel project
```

## Available Commands

### Environment Management:
- `laravel-dev install` - Install dependencies
- `laravel-dev setup` - Full development setup
- `laravel-dev deploy` - Production deployment
- `laravel-dev start/stop/restart` - Container management
- `laravel-dev status` - Check container status

### Development:
- `laravel-dev composer <cmd>` - Run composer commands
- `laravel-dev artisan <cmd>` - Run artisan commands
- `laravel-dev test [filter]` - Run tests
- `laravel-dev gulp` - Compile assets
- `laravel-dev logs [lines]` - View logs
- `laravel-dev shell` - Open container shell

### Maintenance:
- `laravel-dev clear` - Clear all caches
- `laravel-dev fresh` - Fresh migration with seed
- `laravel-dev backup` - Create database backup
- `laravel-dev restore <file>` - Restore database

### Project-specific:
- `laravel-dev assessment <cmd>` - Assessment management
- `laravel-dev user <cmd>` - User management
- `laravel-dev version` - Show version information

## Troubleshooting

1. **Command not found**: Restart your shell or run `source ~/.bashrc`
2. **Docker not running**: Run `laravel-dev start` or `docker-compose up -d`
3. **Permission denied**: Run `chmod +x ~/bin/laravel-dev`
4. **MCP server issues**: Install Python 3 and pip3, then run `pip3 install -r scripts/requirements.txt`

## Getting Help

- Run `laravel-dev help` for command help
- Run `lhelp` for shell alias help
- Check the full documentation in `docs/MCP_DEVELOPMENT_WORKFLOW.md`
