# Utilities Directory

This directory contains various utility scripts and tools for the Talent Assessment project.

## Directory Structure

### `/domain-checking/`
Domain availability checking utilities and results.

**Files:**
- `check_domains_dns.py` - Python script to check domain availability using DNS lookups
- `check_domains_bulk.sh` - Bash script for bulk domain checking using AWS CLI
- `check_domains_public.py` - Python script using public APIs for domain checking
- `check_domain_availability.py` - Original AWS SES domain checking script
- `domain_availability_results.json` - Results from domain availability checks
- `available-domains-summary.md` - Summary of available domains for staging

**Usage:**
```bash
cd utilities/domain-checking/
python3 check_domains_dns.py
```

### `/email-testing/`
Email functionality testing utilities.

**Files:**
- `test_send_assignment_email.php` - Test script for sending assignment emails
- `create_assignment_manually.php` - Script to manually create assignments and send emails
- `test_email_send.php` - Basic email sending test script

**Usage:**
```bash
cd utilities/email-testing/
docker-compose exec app php test_send_assignment_email.php
```

## Purpose

These utilities help with:
1. **Domain Management**: Finding available domains for staging/production
2. **Email Testing**: Testing email functionality before production deployment
3. **Development**: Quick testing and validation of features

## Notes

- All utilities are designed to work with the current development environment
- Results and outputs are saved in their respective directories
- Scripts include error handling and logging for debugging
