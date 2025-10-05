#!/usr/bin/env python3
"""
MCP Laravel Development Server for Talent Assessment Project
Advanced integration for AI assistants with Laravel development workflow
"""

import asyncio
import subprocess
import json
import os
import sys
from datetime import datetime
from pathlib import Path
from typing import List, Dict, Any

# Try to import MCP components
try:
    from mcp.server import Server
    from mcp.types import Tool, TextContent
except ImportError:
    print("MCP not installed. Install with: pip install mcp")
    sys.exit(1)

# Initialize the server
app = Server("laravel-dev-tools")

# Project configuration
PROJECT_DIR = "/opt/talent-assessment"
DOCKER_COMPOSE_FILE = "docker-compose.yml"

def run_command(cmd: str, cwd: str = PROJECT_DIR) -> Dict[str, Any]:
    """Run a command and return the result"""
    try:
        result = subprocess.run(
            cmd,
            shell=True,
            cwd=cwd,
            capture_output=True,
            text=True,
            timeout=300  # 5 minute timeout
        )
        return {
            "success": result.returncode == 0,
            "stdout": result.stdout,
            "stderr": result.stderr,
            "returncode": result.returncode
        }
    except subprocess.TimeoutExpired:
        return {
            "success": False,
            "stdout": "",
            "stderr": "Command timed out after 5 minutes",
            "returncode": -1
        }
    except Exception as e:
        return {
            "success": False,
            "stdout": "",
            "stderr": str(e),
            "returncode": -1
        }

def check_docker_status() -> bool:
    """Check if Docker containers are running"""
    result = run_command("docker-compose ps")
    return result["success"] and "Up" in result["stdout"]

@app.list_tools()
async def list_tools() -> List[Tool]:
    """List available tools"""
    return [
        Tool(
            name="composer",
            description="Run composer commands in Docker container",
            inputSchema={
                "type": "object",
                "properties": {
                    "command": {
                        "type": "string", 
                        "description": "Composer command to run (e.g., 'install', 'update', 'require package')"
                    }
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
                    "command": {
                        "type": "string", 
                        "description": "Artisan command to run (e.g., 'migrate', 'make:controller Name')"
                    }
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
                    "filter": {
                        "type": "string", 
                        "description": "Test filter (optional, e.g., 'AssessmentTest')"
                    },
                    "file": {
                        "type": "string",
                        "description": "Specific test file to run (optional)"
                    }
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
                    "lines": {
                        "type": "integer", 
                        "description": "Number of lines to show", 
                        "default": 50
                    },
                    "service": {
                        "type": "string",
                        "description": "Docker service to show logs for (app, mysql, redis)",
                        "default": "app"
                    }
                }
            }
        ),
        Tool(
            name="backup",
            description="Create database backup",
            inputSchema={
                "type": "object",
                "properties": {
                    "filename": {
                        "type": "string", 
                        "description": "Backup filename (optional, will auto-generate if not provided)"
                    }
                }
            }
        ),
        Tool(
            name="restore",
            description="Restore database from backup",
            inputSchema={
                "type": "object",
                "properties": {
                    "filename": {
                        "type": "string",
                        "description": "Backup filename to restore from"
                    }
                },
                "required": ["filename"]
            }
        ),
        Tool(
            name="docker_status",
            description="Check Docker container status",
            inputSchema={
                "type": "object",
                "properties": {}
            }
        ),
        Tool(
            name="docker_restart",
            description="Restart Docker containers",
            inputSchema={
                "type": "object",
                "properties": {}
            }
        ),
        Tool(
            name="clear_cache",
            description="Clear Laravel caches",
            inputSchema={
                "type": "object",
                "properties": {
                    "type": {
                        "type": "string",
                        "description": "Type of cache to clear (all, config, route, view, cache)",
                        "default": "all"
                    }
                }
            }
        ),
        Tool(
            name="migrate",
            description="Run database migrations",
            inputSchema={
                "type": "object",
                "properties": {
                    "fresh": {
                        "type": "boolean",
                        "description": "Run fresh migration (drops all tables first)",
                        "default": False
                    },
                    "seed": {
                        "type": "boolean",
                        "description": "Run seeders after migration",
                        "default": False
                    }
                }
            }
        ),
        Tool(
            name="create_controller",
            description="Create a new Laravel controller",
            inputSchema={
                "type": "object",
                "properties": {
                    "name": {
                        "type": "string",
                        "description": "Controller name (e.g., 'UserController')"
                    },
                    "resource": {
                        "type": "boolean",
                        "description": "Create resource controller",
                        "default": False
                    }
                },
                "required": ["name"]
            }
        ),
        Tool(
            name="create_model",
            description="Create a new Laravel model",
            inputSchema={
                "type": "object",
                "properties": {
                    "name": {
                        "type": "string",
                        "description": "Model name (e.g., 'User')"
                    },
                    "migration": {
                        "type": "boolean",
                        "description": "Create migration file",
                        "default": True
                    }
                },
                "required": ["name"]
            }
        ),
        Tool(
            name="git_operations",
            description="Perform Git operations",
            inputSchema={
                "type": "object",
                "properties": {
                    "action": {
                        "type": "string",
                        "description": "Git action (status, add, commit, push, pull, branch)",
                        "enum": ["status", "add", "commit", "push", "pull", "branch", "checkout"]
                    },
                    "message": {
                        "type": "string",
                        "description": "Commit message (required for commit action)"
                    },
                    "branch": {
                        "type": "string",
                        "description": "Branch name (required for branch and checkout actions)"
                    }
                },
                "required": ["action"]
            }
        )
    ]

@app.call_tool()
async def call_tool(name: str, arguments: Dict[str, Any]) -> List[TextContent]:
    """Handle tool calls"""
    
    # Check Docker status for most operations
    if name not in ["docker_status", "docker_restart"]:
        if not check_docker_status():
            return [TextContent(
                type="text", 
                text="ERROR: Docker containers are not running. Please start them first with 'docker-compose up -d'"
            )]
    
    if name == "composer":
        cmd = f"docker-compose exec app composer {arguments['command']}"
        result = run_command(cmd)
        output = f"Command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "artisan":
        cmd = f"docker-compose exec app php artisan {arguments['command']}"
        result = run_command(cmd)
        output = f"Command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "test":
        filter_arg = f"--filter {arguments.get('filter', '')}" if arguments.get('filter') else ""
        file_arg = f"{arguments.get('file', '')}" if arguments.get('file') else ""
        cmd = f"docker-compose exec app vendor/bin/phpunit {filter_arg} {file_arg}".strip()
        result = run_command(cmd)
        output = f"Command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "gulp":
        cmd = "docker-compose exec app npm run gulp"
        result = run_command(cmd)
        output = f"Command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "logs":
        lines = arguments.get('lines', 50)
        service = arguments.get('service', 'app')
        cmd = f"docker-compose logs {service} --tail={lines}"
        result = run_command(cmd)
        return [TextContent(type="text", text=result['stdout'])]
    
    elif name == "backup":
        filename = arguments.get('filename')
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"talent-assessment-backup-{timestamp}.sql"
        
        cmd = f"docker-compose exec mysql mysqldump -u root -proot talent_assessment > /opt/{filename}"
        result = run_command(cmd)
        output = f"Backup command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        if result['success']:
            output += f"Backup created: /opt/{filename}\n"
        else:
            output += f"Error: {result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "restore":
        filename = arguments['filename']
        if not os.path.exists(f"/opt/{filename}"):
            return [TextContent(type="text", text=f"ERROR: Backup file not found: /opt/{filename}")]
        
        cmd = f"docker-compose exec -T mysql mysql -u root -proot talent_assessment < /opt/{filename}"
        result = run_command(cmd)
        output = f"Restore command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        if not result['success']:
            output += f"Error: {result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "docker_status":
        cmd = "docker-compose ps"
        result = run_command(cmd)
        return [TextContent(type="text", text=result['stdout'])]
    
    elif name == "docker_restart":
        cmd = "docker-compose down && docker-compose up -d"
        result = run_command(cmd)
        output = f"Restart command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "clear_cache":
        cache_type = arguments.get('type', 'all')
        if cache_type == 'all':
            commands = [
                "docker-compose exec app php artisan view:clear",
                "docker-compose exec app php artisan cache:clear",
                "docker-compose exec app php artisan config:clear",
                "docker-compose exec app php artisan route:clear"
            ]
        else:
            commands = [f"docker-compose exec app php artisan {cache_type}:clear"]
        
        output = f"Clearing {cache_type} cache...\n"
        for cmd in commands:
            result = run_command(cmd)
            output += f"Command: {cmd}\n"
            output += f"Success: {result['success']}\n"
            if result['stderr']:
                output += f"Error: {result['stderr']}\n"
        
        return [TextContent(type="text", text=output)]
    
    elif name == "migrate":
        fresh = arguments.get('fresh', False)
        seed = arguments.get('seed', False)
        
        if fresh:
            cmd = "docker-compose exec app php artisan migrate:fresh"
            if seed:
                cmd += " --seed"
        else:
            cmd = "docker-compose exec app php artisan migrate"
            if seed:
                cmd += " && docker-compose exec app php artisan db:seed"
        
        result = run_command(cmd)
        output = f"Migration command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "create_controller":
        name = arguments['name']
        resource = arguments.get('resource', False)
        cmd = f"docker-compose exec app php artisan make:controller {name}"
        if resource:
            cmd += " --resource"
        
        result = run_command(cmd)
        output = f"Controller creation command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "create_model":
        name = arguments['name']
        migration = arguments.get('migration', True)
        cmd = f"docker-compose exec app php artisan make:model {name}"
        if migration:
            cmd += " --migration"
        
        result = run_command(cmd)
        output = f"Model creation command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    elif name == "git_operations":
        action = arguments['action']
        
        if action == "status":
            cmd = "git status"
        elif action == "add":
            cmd = "git add ."
        elif action == "commit":
            message = arguments.get('message', '')
            if not message:
                return [TextContent(type="text", text="ERROR: Commit message is required for commit action")]
            cmd = f"git commit -m '{message}'"
        elif action == "push":
            cmd = "git push"
        elif action == "pull":
            cmd = "git pull"
        elif action == "branch":
            branch = arguments.get('branch', '')
            if not branch:
                return [TextContent(type="text", text="ERROR: Branch name is required for branch action")]
            cmd = f"git branch {branch}"
        elif action == "checkout":
            branch = arguments.get('branch', '')
            if not branch:
                return [TextContent(type="text", text="ERROR: Branch name is required for checkout action")]
            cmd = f"git checkout {branch}"
        else:
            return [TextContent(type="text", text=f"ERROR: Unknown git action: {action}")]
        
        result = run_command(cmd)
        output = f"Git command: {cmd}\n"
        output += f"Success: {result['success']}\n"
        output += f"Output:\n{result['stdout']}\n"
        if result['stderr']:
            output += f"Errors:\n{result['stderr']}\n"
        return [TextContent(type="text", text=output)]
    
    else:
        return [TextContent(type="text", text=f"ERROR: Unknown tool: {name}")]

if __name__ == "__main__":
    print("Starting MCP Laravel Development Server...")
    print(f"Project directory: {PROJECT_DIR}")
    print("Available tools: composer, artisan, test, gulp, logs, backup, restore, docker_status, docker_restart, clear_cache, migrate, create_controller, create_model, git_operations")
    asyncio.run(app.run())
