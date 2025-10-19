# Laravel Scheduler Setup (Host-Level Cron)

This document describes how to set up the Laravel scheduler using a host-level cron job.

## Overview

The Laravel scheduler runs scheduled tasks defined in `app/Console/Kernel.php`. Rather than running cron inside a Docker container (anti-pattern), we use a host-level cron job that executes commands inside the container using `docker exec`.

## Benefits of Host-Level Cron

- ✅ **Simpler containers** - Follows Docker best practice of one service per container
- ✅ **No container rebuild** - Change schedule without rebuilding images
- ✅ **Easier debugging** - Logs accessible on host filesystem
- ✅ **More reliable** - Cron managed by systemd on host
- ✅ **Better monitoring** - Can send logs to CloudWatch from host

## Installation

### Automated Setup

Run the provided script:

```bash
cd /opt/talent-assessment
./setup-scheduler-cron.sh
```

The script will:
1. Check for existing cron jobs
2. Add/update the scheduler cron job
3. Create the log file with correct permissions
4. Verify the cron job is installed

### Manual Setup

If you prefer to set it up manually:

```bash
# Edit the crontab
crontab -e

# Add this line:
* * * * * cd /opt/talent-assessment && docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1 # CloudWatch: /talent-assessment/production/laravel-scheduler

# Save and exit

# Create the log file
sudo touch /var/log/laravel-scheduler.log
sudo chmod 666 /var/log/laravel-scheduler.log
```

## How It Works

1. **Cron runs every minute** on the host
2. **Executes `docker exec`** to run Laravel's scheduler inside the container
3. **Laravel scheduler** checks `app/Console/Kernel.php` for tasks to run
4. **Output logged** to `/var/log/laravel-scheduler.log`
5. **CloudWatch agent** (if installed) sends logs to AWS CloudWatch

## Scheduled Tasks

Current scheduled tasks (defined in `app/Console/Kernel.php`):

### Reminder Notifications
- **Command**: `reminders:send`
- **Schedule**: Every 30 minutes
- **Description**: Sends email reminders for pending assessments

### Clean Expired Assignments
- **Command**: `assignments:clean-expired`
- **Schedule**: Daily at 02:00 AM
- **Description**: Marks expired assignments as complete

## Verify Installation

### Check Cron Job

```bash
crontab -l | grep schedule:run
```

You should see:
```
* * * * * cd /opt/talent-assessment && docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1 # CloudWatch: /talent-assessment/production/laravel-scheduler
```

### Monitor Logs

```bash
# Tail the log file
tail -f /var/log/laravel-scheduler.log

# View recent entries
tail -n 50 /var/log/laravel-scheduler.log

# Search for specific command
grep "reminders:send" /var/log/laravel-scheduler.log
```

### Test Scheduler Manually

```bash
# Run the scheduler once
cd /opt/talent-assessment
docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:run
```

## Troubleshooting

### Cron Not Running

1. **Check cron service**:
   ```bash
   sudo systemctl status cron
   ```

2. **Restart cron**:
   ```bash
   sudo systemctl restart cron
   ```

3. **Check system logs**:
   ```bash
   sudo grep CRON /var/log/syslog
   ```

### No Log Output

1. **Check file permissions**:
   ```bash
   ls -la /var/log/laravel-scheduler.log
   ```

2. **Verify cron user** has write access:
   ```bash
   sudo touch /var/log/laravel-scheduler.log
   sudo chmod 666 /var/log/laravel-scheduler.log
   ```

### Commands Not Running

1. **Test Laravel scheduler**:
   ```bash
   docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:list
   ```

2. **Check task schedules**:
   ```bash
   docker-compose -f docker-compose.production.yml exec -T app-production php artisan schedule:list
   ```

3. **Verify container is running**:
   ```bash
   docker-compose -f docker-compose.production.yml ps
   ```

### Docker Command Fails

1. **Test docker exec**:
   ```bash
   docker-compose -f docker-compose.production.yml exec -T app-production php artisan --version
   ```

2. **Check container name**:
   ```bash
   docker ps | grep production
   ```

3. **Verify Docker Compose file exists**:
   ```bash
   ls -la /opt/talent-assessment/docker-compose.production.yml
   ```

## Log Management

### Log Rotation

The log file will grow over time. Set up log rotation:

```bash
# Create logrotate config
sudo nano /etc/logrotate.d/laravel-scheduler

# Add this content:
/var/log/laravel-scheduler.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 0666 root root
}

# Test rotation
sudo logrotate -f /etc/logrotate.d/laravel-scheduler
```

### CloudWatch Integration

If you have CloudWatch agent installed (see `scripts/CLOUDWATCH_SETUP.md`), logs are automatically sent to:
- **Log Group**: `/talent-assessment/production/laravel-scheduler`
- **Retention**: 30 days

View in CloudWatch:
```bash
aws logs tail /talent-assessment/production/laravel-scheduler --follow
```

## Adding New Scheduled Tasks

1. **Edit the Kernel file**:
   ```bash
   nano /opt/talent-assessment/app/Console/Kernel.php
   ```

2. **Add your schedule** in the `schedule()` method:
   ```php
   protected function schedule(Schedule $schedule)
   {
       // Example: Run backup daily at 3 AM
       $schedule->command('backup:run')
                ->dailyAt('03:00');
   }
   ```

3. **No need to restart** - The scheduler will pick it up automatically

4. **Test your command**:
   ```bash
   docker-compose -f docker-compose.production.yml exec -T app-production php artisan your:command
   ```

## Available Schedule Frequencies

Laravel provides many scheduling options:

```php
->cron('* * * * *');          // Custom cron expression
->everyMinute();               // Every minute
->everyFiveMinutes();          // Every 5 minutes
->everyTenMinutes();           // Every 10 minutes
->everyThirtyMinutes();        // Every 30 minutes
->hourly();                    // Every hour
->hourlyAt(17);                // Every hour at 17 minutes past
->daily();                     // Daily at midnight
->dailyAt('13:00');            // Daily at 1 PM
->twiceDaily(1, 13);           // Daily at 1 AM and 1 PM
->weekly();                    // Every Sunday at midnight
->weeklyOn(1, '8:00');         // Every Monday at 8 AM
->monthly();                   // First day of month at midnight
->monthlyOn(4, '15:00');       // 4th of month at 3 PM
->quarterly();                 // First day of quarter at midnight
->yearly();                    // First day of year at midnight
->timezone('America/New_York');// Set timezone
->weekdays();                  // Monday through Friday
->weekends();                  // Saturday and Sunday
->sundays();                   // Every Sunday
->mondays();                   // Every Monday
->tuesdays();                  // Every Tuesday
->wednesdays();                // Every Wednesday
->thursdays();                 // Every Thursday
->fridays();                   // Every Friday
->saturdays();                 // Every Saturday
->between('7:00', '22:00');    // Between 7 AM and 10 PM
->unlessBetween('23:00', '4:00'); // Unless between 11 PM and 4 AM
->when(Closure);               // Based on truth test
->environments(['production']);// Only in production
```

## Environment-Specific Setup

### Staging Environment

For staging, use:
```bash
* * * * * cd /opt/talent-assessment && docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan schedule:run >> /var/log/laravel-scheduler-staging.log 2>&1
```

### Development Environment

For local development, use:
```bash
* * * * * cd /opt/talent-assessment && docker-compose exec -T app php artisan schedule:run >> /var/log/laravel-scheduler-dev.log 2>&1
```

Or run manually when needed:
```bash
docker-compose exec app php artisan schedule:run
```

## Security Considerations

- Cron runs as the host user (typically `ubuntu`)
- Log file is world-writable (666) to allow cron to write
- Container commands run as the container's default user
- No sensitive data should be logged (passwords, tokens, etc.)

## References

- [Laravel Task Scheduling Documentation](https://laravel.com/docs/5.1/scheduling)
- [Cron Expression Generator](https://crontab.guru/)
- Docker Best Practices: [One Process Per Container](https://docs.docker.com/develop/dev-best-practices/)
