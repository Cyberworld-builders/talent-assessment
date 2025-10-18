#!/bin/bash

# Setup Laravel Scheduler Cron Job for Production
# This script adds a cron job to the host machine that runs the Laravel scheduler
# inside the production Docker container every minute.

set -e

CRON_JOB='* * * * * cd /opt/talent-assessment && docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1'

echo "Setting up Laravel Scheduler cron job..."
echo ""

# Check if cron job already exists
if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
    echo "⚠️  Cron job already exists. Updating..."
    # Remove old cron job
    (crontab -l 2>/dev/null | grep -v "artisan schedule:run") | crontab -
else
    echo "No existing cron job found. Installing new one..."
fi

# Add new cron job (handles "no crontab" case gracefully)
(crontab -l 2>/dev/null || true; echo "$CRON_JOB") | crontab -

# Create log file with proper permissions
sudo touch /var/log/laravel-scheduler.log 2>/dev/null || touch /var/log/laravel-scheduler.log
sudo chmod 666 /var/log/laravel-scheduler.log 2>/dev/null || chmod 666 /var/log/laravel-scheduler.log

echo "✓ Cron job installed successfully!"
echo ""
echo "The Laravel scheduler will now run every minute."
echo "Logs will be written to: /var/log/laravel-scheduler.log"
echo ""
echo "To view logs:"
echo "  tail -f /var/log/laravel-scheduler.log"
echo ""
echo "To verify cron job:"
echo "  crontab -l"
echo ""
echo "To remove cron job:"
echo "  crontab -l | grep -v 'artisan schedule:run' | crontab -"

