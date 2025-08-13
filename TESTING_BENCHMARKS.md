# Testing Benchmark Uploads

## Test Files Created

1. **`test_benchmarks.csv`** - Simple CSV file with sample benchmark data
2. **`create_test_excel.php`** - PHP script to generate an Excel file for testing

## How to Use

### Option 1: Generate Excel File (if zip extension is working)
```bash
docker compose exec app php create_test_excel.php
```
This will create `storage/app/test_benchmarks_template.xlsx`

### Option 2: Use CSV File
You can convert the CSV to Excel format using any spreadsheet application:
1. Open `test_benchmarks.csv` in Excel, Google Sheets, or LibreOffice
2. Save as `.xlsx` format
3. Use the resulting file for testing

## Test Data Structure

The test file contains:
- **Dimension Name**: Common assessment dimensions
- **Benchmark Value**: Sample benchmark scores (0-100 scale)

## Testing Steps

1. Go to the benchmarks page for an assessment
2. Select an industry
3. Upload the test Excel file
4. Verify that benchmarks are created/updated in the database

## Expected Results

- 10 benchmark records should be created/updated
- Each dimension should have a benchmark value
- The system should handle the upload without errors

## Troubleshooting

If upload fails:
- Check that dimension names match exactly with those in the assessment
- Ensure the Excel file has the correct column headers
- Verify the file format is `.xlsx` or `.xls`
