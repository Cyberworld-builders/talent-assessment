#!/bin/bash
# Installation script for Laravel Development Tools
# Run this script to set up all development tools

set -e

echo "🚀 Installing Laravel Development Tools for Talent Assessment Project"
echo "=================================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "docker-compose.yml" ]; then
    print_error "Please run this script from the project root directory (/opt/talent-assessment)"
    exit 1
fi

# Create scripts directory if it doesn't exist
mkdir -p scripts

# Make scripts executable
print_status "Making scripts executable..."
chmod +x scripts/laravel-dev
chmod +x scripts/shell-aliases.sh
chmod +x scripts/mcp_laravel_server.py
chmod +x scripts/install.sh

print_success "Scripts made executable!"

# Install laravel-dev to system PATH
print_status "Installing laravel-dev script to system PATH..."

# Create ~/bin directory if it doesn't exist
mkdir -p ~/bin

# Copy laravel-dev script to ~/bin
cp scripts/laravel-dev ~/bin/laravel-dev
chmod +x ~/bin/laravel-dev

# Add ~/bin to PATH if not already there
if ! echo "$PATH" | grep -q "$HOME/bin"; then
    print_status "Adding ~/bin to PATH..."
    echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
    echo 'export PATH="$HOME/bin:$PATH"' >> ~/.zshrc 2>/dev/null || true
    print_success "Added ~/bin to PATH in shell configuration files"
else
    print_success "~/bin already in PATH"
fi

# Install shell aliases
print_status "Installing shell aliases..."
if [ -f ~/.bashrc ]; then
    if ! grep -q "Laravel Development Aliases" ~/.bashrc; then
        echo "" >> ~/.bashrc
        echo "# Laravel Development Aliases for Talent Assessment" >> ~/.bashrc
        cat scripts/shell-aliases.sh >> ~/.bashrc
        print_success "Shell aliases added to ~/.bashrc"
    else
        print_warning "Shell aliases already exist in ~/.bashrc"
    fi
fi

if [ -f ~/.zshrc ]; then
    if ! grep -q "Laravel Development Aliases" ~/.zshrc; then
        echo "" >> ~/.zshrc
        echo "# Laravel Development Aliases for Talent Assessment" >> ~/.zshrc
        cat scripts/shell-aliases.sh >> ~/.zshrc
        print_success "Shell aliases added to ~/.zshrc"
    else
        print_warning "Shell aliases already exist in ~/.zshrc"
    fi
fi

# Check for required dependencies
print_status "Checking system dependencies..."

# Check for Python 3
if command -v python3 &> /dev/null; then
    print_success "Python 3 is available"
    PYTHON_AVAILABLE=true
else
    print_warning "Python 3 not found. MCP server will not be available."
    print_warning "To install Python 3:"
    print_warning "  Ubuntu/Debian: sudo apt update && sudo apt install python3 python3-pip"
    print_warning "  CentOS/RHEL: sudo yum install python3 python3-pip"
    print_warning "  macOS: brew install python3"
    PYTHON_AVAILABLE=false
fi

# Check for pip3
if [ "$PYTHON_AVAILABLE" = true ]; then
    if command -v pip3 &> /dev/null; then
        print_success "pip3 is available"
        PIP_AVAILABLE=true
    else
        print_warning "pip3 not found. Installing pip3..."
        if command -v apt &> /dev/null; then
            print_status "Installing pip3 via apt..."
            sudo apt update && sudo apt install -y python3-pip
            if command -v pip3 &> /dev/null; then
                print_success "pip3 installed successfully"
                PIP_AVAILABLE=true
            else
                print_error "Failed to install pip3"
                PIP_AVAILABLE=false
            fi
        elif command -v yum &> /dev/null; then
            print_status "Installing pip3 via yum..."
            sudo yum install -y python3-pip
            if command -v pip3 &> /dev/null; then
                print_success "pip3 installed successfully"
                PIP_AVAILABLE=true
            else
                print_error "Failed to install pip3"
                PIP_AVAILABLE=false
            fi
        else
            print_warning "Cannot auto-install pip3. Please install manually:"
            print_warning "  Ubuntu/Debian: sudo apt install python3-pip"
            print_warning "  CentOS/RHEL: sudo yum install python3-pip"
            print_warning "  macOS: brew install python3"
            PIP_AVAILABLE=false
        fi
    fi
    
    # Install MCP dependencies if pip3 is available
    if [ "$PIP_AVAILABLE" = true ]; then
        print_status "Installing MCP server dependencies..."
        if pip3 install -r scripts/requirements.txt; then
            print_success "MCP server dependencies installed!"
        else
            print_warning "Failed to install MCP server dependencies."
            print_warning "You can install them manually later with:"
            print_warning "  pip3 install -r scripts/requirements.txt"
        fi
    else
        print_warning "MCP server dependencies not installed due to missing pip3."
        print_warning "Install pip3 and run: pip3 install -r scripts/requirements.txt"
    fi
fi

# Test the installation
print_status "Testing installation..."

# Test laravel-dev script
if command -v laravel-dev &> /dev/null; then
    print_success "laravel-dev command is available"
else
    print_warning "laravel-dev command not found in PATH."
    print_status "Trying to source ~/.bashrc to update PATH..."
    source ~/.bashrc 2>/dev/null || true
    
    if command -v laravel-dev &> /dev/null; then
        print_success "laravel-dev command is now available"
    else
        print_warning "laravel-dev command still not found. You may need to restart your shell."
        print_status "You can test manually with: ~/bin/laravel-dev help"
    fi
fi

# Test Docker
if command -v docker-compose &> /dev/null; then
    print_success "Docker Compose is available"
else
    print_error "Docker Compose not found. Please install Docker and Docker Compose."
fi

# Create a quick start guide
print_status "Creating quick start guide..."
cat > scripts/QUICK_START.md << 'EOF'
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
EOF

print_success "Quick start guide created at scripts/QUICK_START.md"

# Final instructions
echo ""
echo "🎉 Installation Complete!"
echo "========================"
echo ""
echo "Next steps:"
echo "1. Restart your shell or run: source ~/.bashrc"
echo "2. Test the installation: laravel-dev help"
echo "3. Check Docker status: laravel-dev status"
echo "4. Set up development environment: laravel-dev setup"
echo ""
echo "Quick start guide: scripts/QUICK_START.md"
echo "Full documentation: docs/MCP_DEVELOPMENT_WORKFLOW.md"
echo ""
echo "Happy coding! 🚀"
