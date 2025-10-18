# Laravel Scheduler Setup for Production

This document describes how to set up the Laravel task scheduler to run on the production server.

## Overview

The Laravel scheduler allows you to schedule tasks to run at specific intervals (e.g., sending reminder emails, cleaning up old data, etc.). Instead of defining multiple cron jobs, you define scheduled tasks in `app/Console/Kernel.php` and run a single cron job that executes the Laravel scheduler every minute.

## Setup Instructions

### 1. SSH into the Production Server

```bash
ssh user@your-production-server
cd /opt/talent-assessment
```

### 2. Run the Setup Script

```bash
sudo chmod +x setup-scheduler-cron.sh
sudo ./setup-scheduler-cron.sh
```

This will add a cron job that runs every minute and executes the Laravel scheduler inside the production Docker container.

### 3. Verify Installation

Check that the cron job was installed:

```bash
crontab -l
```

You should see:
```
* * * * * cd /opt/talent-assessment && docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1
```

## Monitoring

### View Scheduler Logs

```bash
# View live logs
tail -f /var/log/laravel-scheduler.log

# View last 100 lines
tail -n 100 /var/log/laravel-scheduler.log
```

### Test Manually

You can test the scheduler manually:

```bash
docker-compose -f docker-compose.production.yml exec app-production php artisan schedule:run
```

## Adding Scheduled Tasks

Edit `app/Console/Kernel.php` to add your scheduled tasks:

```php
protected function schedule(Schedule $schedule)
{
    // Send reminder emails every day at 9 AM
    $schedule->command('reminders:send')
             ->dailyAt('09:00');
    
    // Clean up old assignments weekly
    $schedule->command('assignments:cleanup')
             ->weekly()
             ->sundays()
             ->at('01:00');
    
    // Generate reports every hour
    $schedule->command('reports:generate')
             ->hourly();
}
```

## Available Schedule Frequencies

- `->everyMinute()` - Run every minute
- `->everyFiveMinutes()` - Run every 5 minutes
- `->hourly()` - Run every hour
- `->dailyAt('13:00')` - Run daily at 1:00 PM
- `->twiceDaily(1, 13)` - Run at 1:00 AM and 1:00 PM
- `->weekly()` - Run weekly on Sunday at 00:00
- `->monthly()` - Run monthly on the 1st at 00:00
- `->weekdays()` - Run Monday through Friday
- `->weekends()` - Run Saturday and Sunday

See [Laravel Scheduling Documentation](https://laravel.com/docs/5.1/scheduling) for more options.

## Troubleshooting

### Scheduler Not Running

1. **Check if cron is running:**
   ```bash
   sudo service cron status
   ```

2. **Check cron logs:**
   ```bash
   tail -f /var/log/laravel-scheduler.log
   sudo tail -f /var/log/syslog | grep CRON
   ```

3. **Verify container is running:**
   ```bash
   docker-compose -f docker-compose.production.yml ps
   ```

4. **Test manually:**
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production php artisan schedule:run
   ```

### Permission Issues

If you encounter permission issues with the log file:

```bash
sudo touch /var/log/laravel-scheduler.log
sudo chmod 666 /var/log/laravel-scheduler.log
```

## Uninstalling

To remove the cron job:

```bash
crontab -l | grep -v 'artisan schedule:run' | crontab -
```

## Notes

- The cron job runs on the **host machine**, not inside the container
- It uses `docker-compose exec` to run commands inside the container
- Logs are stored on the host at `/var/log/laravel-scheduler.log`
- The `-T` flag in the command disables pseudo-TTY allocation (required for cron)
- The scheduler only runs tasks that are due at the time it executes

## Example Scheduled Tasks for This Application

Here are some common scheduled tasks you might want to add:

```php
protected function schedule(Schedule $schedule)
{
    // Send reminder emails for incomplete assignments
    $schedule->command('reminders:send')
             ->dailyAt('09:00')
             ->timezone('America/New_York');
    
    // Clean up expired assignments
    $schedule->command('assignments:cleanup-expired')
             ->daily();
    
    // Generate daily reports
    $schedule->command('reports:generate-daily')
             ->dailyAt('23:00');
    
    // Send weekly summary emails
    $schedule->command('emails:weekly-summary')
             ->weekly()
             ->mondays()
             ->at('08:00');
}
```

