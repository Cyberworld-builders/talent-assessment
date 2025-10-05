# MCP Development Workflow Guide

## Table of Contents
- [What is MCP?](#what-is-mcp)
- [Current MCP Capabilities](#current-mcp-capabilities)
- [Setting Up Development Tools](#setting-up-development-tools)
- [Custom MCP Server (Advanced)](#custom-mcp-server-advanced)
- [Practical Implementation](#practical-implementation)
- [Example Workflows](#example-workflows)
- [Troubleshooting](#troubleshooting)

## What is MCP?

MCP (Model Context Protocol) is a protocol that allows AI assistants to connect to external tools and services. Think of it as a way to give AI assistants "superpowers" by connecting them to your development environment, databases, APIs, and other tools.

### Key Benefits
- **Automated workflows**: AI can run complex development tasks
- **Context awareness**: AI understands your project structure
- **Tool integration**: Connect to databases, APIs, file systems
- **Custom commands**: Create project-specific tools

## Current MCP Capabilities

The AI assistant already has these built-in capabilities:
- **File operations**: Read, write, search files
- **Terminal commands**: Execute commands in your environment
- **Web search**: Get real-time information
- **Code analysis**: Search, lint, understand codebases
- **Docker integration**: Run containerized commands

## Setting Up Development Tools

### Option 1: Simple Development Script

Create a comprehensive development helper script:

```bash
#!/bin/bash
# ~/bin/laravel-dev
# Laravel Development Helper for Talent Assessment Project

# Change to project directory
cd /opt/talent-assessment

case "$1" in
    "install")
        echo "Installing dependencies..."
        docker-compose exec app composer install
        docker-compose exec app npm install
        echo "Dependencies installed successfully!"
        ;;
    "setup")
        echo "Setting up development environment..."
        docker-compose exec app composer install
        docker-compose exec app php artisan migrate
        docker-compose exec app npm run gulp
        echo "Development environment ready!"
        ;;
    "deploy")
        echo "Preparing for production deployment..."
        docker-compose exec app composer install --no-dev --optimize-autoloader
        docker-compose exec app php artisan config:cache
        docker-compose exec app php artisan route:cache
        docker-compose exec app npm run gulp
        echo "Production deployment ready!"
        ;;
    "composer")
        docker-compose exec app composer "${@:2}"
        ;;
    "artisan")
        docker-compose exec app php artisan "${@:2}"
        ;;
    "test")
        docker-compose exec app vendor/bin/phpunit "${@:2}"
        ;;
    "gulp")
        docker-compose exec app npm run gulp
        ;;
    "logs")
        docker-compose logs app --tail=50
        ;;
    "shell")
        docker-compose exec app bash
        ;;
    "clear")
        echo "Clearing caches..."
        docker-compose exec app php artisan view:clear
        docker-compose exec app php artisan cache:clear
        docker-compose exec app php artisan config:clear
        echo "Caches cleared!"
        ;;
    "fresh")
        echo "Running fresh migration with seed..."
        docker-compose exec app php artisan migrate:fresh --seed
        echo "Database refreshed!"
        ;;
    "backup")
        echo "Creating database backup..."
        docker-compose exec mysql mysqldump -u root -proot talent_assessment > /opt/talent-assessment-backup-$(date +%Y%m%d_%H%M%S).sql
        echo "Backup created!"
        ;;
    "restore")
        if [ -z "$2" ]; then
            echo "Usage: laravel-dev restore <backup-file>"
            exit 1
        fi
        echo "Restoring database from $2..."
        docker-compose exec -T mysql mysql -u root -proot talent_assessment < "$2"
        echo "Database restored!"
        ;;
    "status")
        echo "Checking container status..."
        docker-compose ps
        ;;
    "restart")
        echo "Restarting containers..."
        docker-compose down
        docker-compose up -d
        echo "Containers restarted!"
        ;;
    *)
        echo "Laravel Development Helper for Talent Assessment"
        echo "Usage: laravel-dev [command]"
        echo ""
        echo "Environment Commands:"
        echo "  install     - Install dependencies (composer + npm)"
        echo "  setup       - Full development setup"
        echo "  deploy      - Production deployment preparation"
        echo "  restart     - Restart all containers"
        echo "  status      - Check container status"
        echo ""
        echo "Development Commands:"
        echo "  composer <cmd>    - Run composer commands"
        echo "  artisan <cmd>     - Run artisan commands"
        echo "  test [filter]     - Run PHPUnit tests"
        echo "  gulp             - Compile assets"
        echo "  logs             - Show application logs"
        echo "  shell            - Open container shell"
        echo ""
        echo "Maintenance Commands:"
        echo "  clear            - Clear all caches"
        echo "  fresh            - Fresh migration with seed"
        echo "  backup           - Create database backup"
        echo "  restore <file>   - Restore database from backup"
        ;;
esac
```

### Option 2: Shell Aliases

Add to your `~/.bashrc` or `~/.zshrc`:

```bash
# Laravel Development Shortcuts for Talent Assessment
alias lc='docker-compose exec app composer'
alias la='docker-compose exec app php artisan'
alias lt='docker-compose exec app vendor/bin/phpunit'
alias lg='docker-compose exec app npm run gulp'
alias ll='docker-compose logs app --tail=50'
alias ls='docker-compose exec app bash'

# Quick Laravel commands
alias lclear='docker-compose exec app php artisan view:clear && docker-compose exec app php artisan cache:clear'
alias lfresh='docker-compose exec app php artisan migrate:fresh --seed'
alias lserve='docker-compose exec app php artisan serve --host=0.0.0.0'

# Project-specific shortcuts
alias ldev='cd /opt/talent-assessment'
alias lstatus='docker-compose ps'
alias lrestart='docker-compose down && docker-compose up -d'
```

## Custom MCP Server (Advanced)

For advanced integration, create a Python MCP server:

```python
# mcp_laravel_server.py
import asyncio
import subprocess
import json
import os
from mcp.server import Server
from mcp.types import Tool, TextContent

app = Server("laravel-dev-tools")

@app.list_tools()
async def list_tools() -> list[Tool]:
    return [
        Tool(
            name="composer",
            description="Run composer commands in Docker container",
            inputSchema={
                "type": "object",
                "properties": {
                    "command": {"type": "string", "description": "Composer command to run"}
                },
                "required": ["command"]
            }
        ),
        Tool(
            name="artisan",
            description="Run Laravel artisan commands",
            inputSchema={
                "type": "object", 
                "properties": {
                    "command": {"type": "string", "description": "Artisan command to run"}
                },
                "required": ["command"]
            }
        ),
        Tool(
            name="test",
            description="Run PHPUnit tests",
            inputSchema={
                "type": "object",
                "properties": {
                    "filter": {"type": "string", "description": "Test filter (optional)"}
                }
            }
        ),
        Tool(
            name="gulp",
            description="Compile assets with Gulp",
            inputSchema={
                "type": "object",
                "properties": {}
            }
        ),
        Tool(
            name="logs",
            description="Show application logs",
            inputSchema={
                "type": "object",
                "properties": {
                    "lines": {"type": "integer", "description": "Number of lines to show", "default": 50}
                }
            }
        ),
        Tool(
            name="backup",
            description="Create database backup",
            inputSchema={
                "type": "object",
                "properties": {
                    "filename": {"type": "string", "description": "Backup filename (optional)"}
                }
            }
        )
    ]

@app.call_tool()
async def call_tool(name: str, arguments: dict) -> list[TextContent]:
    # Change to project directory
    os.chdir('/opt/talent-assessment')
    
    if name == "composer":
        cmd = f"docker-compose exec app composer {arguments['command']}"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=result.stdout + result.stderr)]
    
    elif name == "artisan":
        cmd = f"docker-compose exec app php artisan {arguments['command']}"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=result.stdout + result.stderr)]
    
    elif name == "test":
        filter_arg = f"--filter {arguments.get('filter', '')}" if arguments.get('filter') else ""
        cmd = f"docker-compose exec app vendor/bin/phpunit {filter_arg}"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=result.stdout + result.stderr)]
    
    elif name == "gulp":
        cmd = "docker-compose exec app npm run gulp"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=result.stdout + result.stderr)]
    
    elif name == "logs":
        lines = arguments.get('lines', 50)
        cmd = f"docker-compose logs app --tail={lines}"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=result.stdout)]
    
    elif name == "backup":
        filename = arguments.get('filename', f'talent-assessment-backup-{datetime.now().strftime("%Y%m%d_%H%M%S")}.sql')
        cmd = f"docker-compose exec mysql mysqldump -u root -proot talent_assessment > /opt/{filename}"
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        return [TextContent(type="text", text=f"Backup created: {filename}")]

if __name__ == "__main__":
    asyncio.run(app.run())
```

## Practical Implementation

### Step 1: Create Development Script

```bash
# Create the script
cat > ~/bin/laravel-dev << 'EOF'
#!/bin/bash
# [Script content from above]
EOF

# Make it executable
chmod +x ~/bin/laravel-dev

# Add to PATH (if not already)
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

### Step 2: Test the Script

```bash
# Test basic functionality
laravel-dev status
laravel-dev composer --version
laravel-dev artisan --version
```

### Step 3: Create Project-Specific Commands

```bash
# Add to your development script
"assessment")
    echo "Assessment-specific commands:"
    echo "  laravel-dev assessment create    - Create new assessment"
    echo "  laravel-dev assessment test       - Run assessment tests"
    echo "  laravel-dev assessment seed       - Seed assessment data"
    ;;
"user")
    echo "User management commands:"
    echo "  laravel-dev user import <file>    - Import users from CSV"
    echo "  laravel-dev user export           - Export users to CSV"
    ;;
```

## Example Workflows

### Daily Development Workflow

```bash
# Morning setup
laravel-dev status          # Check if containers are running
laravel-dev logs            # Check for any errors
laravel-dev test            # Run tests to ensure everything works

# During development
laravel-dev artisan make:controller NewController
laravel-dev gulp            # Compile assets after changes
laravel-dev test            # Run tests after changes

# End of day
laravel-dev backup          # Create backup
laravel-dev clear           # Clear caches
```

### Deployment Workflow

```bash
# Staging deployment
laravel-dev deploy          # Prepare for production
git tag v1.5.41-staging     # Create staging tag
git push origin v1.5.41-staging

# Production deployment
laravel-dev backup          # Backup current database
laravel-dev deploy          # Prepare for production
git tag v1.5.41-release     # Create release tag
git push origin v1.5.41-release
```

### Debugging Workflow

```bash
# When issues arise
laravel-dev logs            # Check application logs
laravel-dev shell           # Open container for debugging
laravel-dev test            # Run tests to isolate issues
laravel-dev clear           # Clear caches if needed
```

## AI Assistant Integration

The AI assistant can now use these tools:

```bash
# AI can run these commands for you
laravel-dev composer install
laravel-dev artisan migrate
laravel-dev test
laravel-dev gulp
laravel-dev logs
laravel-dev backup
```

### Example AI Workflow

1. **User**: "I need to add a new feature"
2. **AI**: Runs `laravel-dev status` to check environment
3. **AI**: Creates the necessary files and code
4. **AI**: Runs `laravel-dev test` to ensure tests pass
5. **AI**: Runs `laravel-dev gulp` to compile assets
6. **AI**: Provides summary of changes made

## Troubleshooting

### Common Issues

1. **Permission denied**
   ```bash
   chmod +x ~/bin/laravel-dev
   ```

2. **Command not found**
   ```bash
   echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
   source ~/.bashrc
   ```

3. **Docker containers not running**
   ```bash
   laravel-dev restart
   ```

4. **Database connection issues**
   ```bash
   laravel-dev status
   laravel-dev logs
   ```

### Debugging Commands

```bash
# Check container status
laravel-dev status

# Check logs for errors
laravel-dev logs

# Open shell for manual debugging
laravel-dev shell

# Clear all caches
laravel-dev clear

# Fresh start
laravel-dev fresh
```

## Best Practices

1. **Always backup before major changes**
2. **Run tests frequently during development**
3. **Use version control with meaningful commit messages**
4. **Keep development and production environments separate**
5. **Monitor logs for errors and performance issues**

## Advanced Features

### Custom Commands for Your Project

```bash
# Add to laravel-dev script
"assessment")
    case "$2" in
        "create")
            docker-compose exec app php artisan make:assessment
            ;;
        "test")
            docker-compose exec app vendor/bin/phpunit tests/AssessmentTest.php
            ;;
        "seed")
            docker-compose exec app php artisan db:seed --class=AssessmentSeeder
            ;;
        *)
            echo "Assessment commands: create, test, seed"
            ;;
    esac
    ;;
```

### Environment-Specific Commands

```bash
# Development
laravel-dev dev:setup
laravel-dev dev:test
laravel-dev dev:debug

# Staging
laravel-dev staging:deploy
laravel-dev staging:test

# Production
laravel-dev prod:backup
laravel-dev prod:deploy
laravel-dev prod:monitor
```

This documentation provides a comprehensive guide for setting up and using MCP capabilities with your Laravel development workflow. The tools and scripts can be customized further based on your specific project needs.
