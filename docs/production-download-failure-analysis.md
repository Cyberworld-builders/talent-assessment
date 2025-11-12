# Production Download Failure Analysis

**Date:** October 12, 2025  
**Issue:** Data export downloads failing on production environment  
**Status:** Root cause identified

## Problem Summary

User reported that data export downloads were failing on production. Investigation was needed to determine why the download functionality was not working.

## Investigation Process

### 1. Initial Log Review

Attempted to check production logs using:
```bash
docker-compose -f docker-compose.production.yml logs app-production --tail=200
```

Found nginx access logs showing successful requests:
- `23:40:20` - First download attempt for type 1
- `23:40:30` - Second download attempt for type 2
- `23:40:31` - Successful file download of CSV

### 2. Laravel Application Logs

Retrieved Laravel error logs from production:
```bash
docker-compose -f docker-compose.production.yml exec app-production tail -100 storage/logs/laravel.log
```

## Root Cause Identified

**Error:** `ErrorException: Trying to access array offset on value of type int in /var/www/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DefaultValueBinder.php:82`

**Location:** 
- File: `/var/www/app/Http/Controllers/AssignmentsController.php`
- Line: `1826`
- Method: `excelTemplateAssignmentAnswers()`
- Called from: `download_all_assignments_for_client()` at line `1631`

### Technical Details

The error occurs when PHPExcel attempts to bind an integer value of `0` to a cell. This is a **PHP 7.4 compatibility issue** with the legacy PHPExcel library.

**Stack Trace Key Points:**
```
#1 /var/www/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DefaultValueBinder.php(61): 
    PHPExcel_Cell_DefaultValueBinder::dataTypeForValue(0)
#2 /var/www/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell.php(206): 
    PHPExcel_Cell_DefaultValueBinder->bindValue(Object(PHPExcel_Cell), 0)
#3 /var/www/vendor/phpoffice/phpexcel/Classes/PHPExcel/Worksheet.php(1085): 
    PHPExcel_Cell->setValue(0)
#4 /var/www/vendor/maatwebsite/excel/src/Maatwebsite/Excel/Classes/LaravelExcelWorksheet.php(209): 
    PHPExcel_Worksheet->setCellValue('I9', 0)
#5 /var/www/app/Http/Controllers/AssignmentsController.php(1826): 
    Maatwebsite\Excel\Classes\LaravelExcelWorksheet->row(9, Array)
```

## Context: Recent Changes

**Important Note:** Production is currently running **v1.6.5**, not v1.6.6. The recent S3 upload changes (v1.6.6) have not been deployed yet, so this issue is **unrelated to the S3 upload feature**.

Recent branch work includes:
- Branch: `84-final-round-of-v1-bugfixes`
- Latest commit: "Handle S3 URLs in download completion JavaScript"
- Changes focused on S3/CloudFront URL handling for exports

## The Real Issue

When generating Excel exports with PHPExcel, if a user hasn't answered a question or a value is empty, the code is passing integer `0` values to the spreadsheet cells. PHPExcel in PHP 7.4 has known issues with integer zero values causing type errors.

### Likely Scenario

In the `excelTemplateAssignmentAnswers()` method at line 1826:
```php
$sheet->row(9, Array)
```

The array being passed contains integer `0` values for unanswered questions, which triggers the PHPExcel bug.

## Recommended Fix

The issue needs to be addressed by ensuring all cell values are properly cast to strings or null values instead of integer zeros:

### Option 1: Cast to String
```php
// Before adding to row array
$value = $answer ? $answer->value : '0';  // String zero instead of int
```

### Option 2: Use Empty String
```php
// Before adding to row array
$value = $answer ? $answer->value : '';  // Empty string instead of int zero
```

### Option 3: Use Null
```php
// Before adding to row array
$value = $answer ? $answer->value : null;  // Null instead of int zero
```

## Next Steps

1. Review `AssignmentsController::excelTemplateAssignmentAnswers()` method around line 1826
2. Identify where integer `0` values are being added to row arrays
3. Cast all numeric values to strings before adding to Excel rows
4. Test fix in development environment
5. Deploy fix to production as hotfix
6. Consider migrating from PHPExcel to PhpSpreadsheet (modern, actively maintained alternative)

## Additional Notes

- This is a **pre-existing bug**, not introduced by recent changes
- The issue affects all download types (type 1 and type 2 exports)
- Downloads work when there are no zero/empty values in the data
- PHPExcel is deprecated; consider migration to PhpSpreadsheet for long-term solution

## Files to Review

- `/opt/talent-assessment/app/Http/Controllers/AssignmentsController.php` (lines 1620-1850)
- Focus on: `download_all_assignments_for_client()` and `excelTemplateAssignmentAnswers()`

## Related Documentation

- [PHPExcel Known Issues](https://github.com/PHPOffice/PHPExcel/issues)
- [PhpSpreadsheet Migration Guide](https://phpspreadsheet.readthedocs.io/en/latest/)
- See `docs/why-staging-worked-but-production-didnt.md` for related deployment debugging patterns

