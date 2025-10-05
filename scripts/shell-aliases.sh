#!/bin/bash
# Shell Aliases for Laravel Development - Talent Assessment Project
# Add this to your ~/.bashrc or ~/.zshrc

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
alias lstart='docker-compose up -d'
alias lstop='docker-compose down'

# Development workflow shortcuts
alias lsetup='cd /opt/talent-assessment && docker-compose exec app composer install && docker-compose exec app php artisan migrate && docker-compose exec app npm run gulp'
alias ltest='cd /opt/talent-assessment && docker-compose exec app vendor/bin/phpunit'
alias lbackup='cd /opt/talent-assessment && docker-compose exec mysql mysqldump -u root -proot talent_assessment > /opt/talent-assessment-backup-$(date +%Y%m%d_%H%M%S).sql'

# Git shortcuts for the project
alias lgit='cd /opt/talent-assessment && git'
alias lcommit='cd /opt/talent-assessment && git add . && git commit'
alias lpush='cd /opt/talent-assessment && git push'
alias lpull='cd /opt/talent-assessment && git pull'

# Docker shortcuts
alias lbuild='cd /opt/talent-assessment && docker-compose build'
alias llogs='cd /opt/talent-assessment && docker-compose logs'
alias lshell='cd /opt/talent-assessment && docker-compose exec app bash'

# Assessment-specific shortcuts
alias lassessment='cd /opt/talent-assessment && docker-compose exec app php artisan'
alias ltest-assessment='cd /opt/talent-assessment && docker-compose exec app vendor/bin/phpunit tests/AssessmentTest.php'

# User management shortcuts
alias lusers='cd /opt/talent-assessment && docker-compose exec app php artisan users:'
alias limport-users='cd /opt/talent-assessment && docker-compose exec app php artisan users:import'
alias lexport-users='cd /opt/talent-assessment && docker-compose exec app php artisan users:export'

# Environment shortcuts
alias ldev-env='cd /opt/talent-assessment && docker-compose exec app php artisan env'
alias lconfig='cd /opt/talent-assessment && docker-compose exec app php artisan config:'
alias lroute='cd /opt/talent-assessment && docker-compose exec app php artisan route:'

# Database shortcuts
alias ldb='cd /opt/talent-assessment && docker-compose exec mysql mysql -u root -proot talent_assessment'
alias lmigrate='cd /opt/talent-assessment && docker-compose exec app php artisan migrate'
alias lseed='cd /opt/talent-assessment && docker-compose exec app php artisan db:seed'

# Asset management shortcuts
alias lassets='cd /opt/talent-assessment && docker-compose exec app npm run gulp'
alias lwatch='cd /opt/talent-assessment && docker-compose exec app npm run watch'

# Quick project navigation
alias lcd='cd /opt/talent-assessment'
alias lviews='cd /opt/talent-assessment/resources/views'
alias lcontrollers='cd /opt/talent-assessment/app/Http/Controllers'
alias lmodels='cd /opt/talent-assessment/app'
alias lroutes='cd /opt/talent-assessment/routes'
alias lconfigs='cd /opt/talent-assessment/config'
alias lpublic='cd /opt/talent-assessment/public'
alias lresources='cd /opt/talent-assessment/resources'

# Help function
lhelp() {
    echo "Laravel Development Aliases for Talent Assessment:"
    echo ""
    echo "Basic Commands:"
    echo "  lc, la, lt, lg, ll, ls    - composer, artisan, test, gulp, logs, shell"
    echo "  lclear, lfresh, lserve    - clear caches, fresh migrate, serve"
    echo ""
    echo "Project Navigation:"
    echo "  ldev, lcd                 - go to project directory"
    echo "  lviews, lcontrollers      - navigate to specific folders"
    echo "  lmodels, lroutes, lconfig - navigate to app components"
    echo ""
    echo "Docker Management:"
    echo "  lstatus, lrestart         - check status, restart containers"
    echo "  lstart, lstop, lbuild      - start, stop, build containers"
    echo "  llogs, lshell             - view logs, open shell"
    echo ""
    echo "Development Workflow:"
    echo "  lsetup                    - full development setup"
    echo "  ltest, lbackup            - run tests, create backup"
    echo "  lgit, lcommit, lpush      - git operations"
    echo ""
    echo "Assessment Specific:"
    echo "  lassessment               - artisan commands for assessments"
    echo "  ltest-assessment          - run assessment tests"
    echo "  lusers, limport-users     - user management"
    echo ""
    echo "Database & Assets:"
    echo "  ldb, lmigrate, lseed      - database operations"
    echo "  lassets, lwatch           - asset compilation"
    echo ""
    echo "Type 'lhelp' to see this help again."
}
