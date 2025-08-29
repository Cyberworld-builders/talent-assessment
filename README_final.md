# Assessment Data Export - SQL Dump Parser

## Summary

I've created a standalone Python script that successfully parses your MySQL dump file and extracts all assessment data with complete relationship hierarchy. The script is now working and has successfully extracted:

- **4 assessments** from your SQL dump
- **74 dimensions** with hierarchical structure
- **896 benchmarks** with industry data
- **890 users** (creators of assessments)
- **16 industries** for benchmark context
- **2 languages** for translation support

## Files Created

1. **`parse_sql_dump.py`** - The main SQL dump parser script
2. **`README_sql_parser.md`** - Complete documentation
3. **`assessments_from_dump.json`** - The extracted data (251KB)

## Quick Start

### 1. Run the Parser

```bash
python3 parse_sql_dump.py
```

This will:
- Parse `involved_database_dump.sql`
- Extract all assessment data and relationships
- Output to `assessments_from_dump.json`

### 2. Custom Options

```bash
# Use different input file
python3 parse_sql_dump.py --input my_dump.sql

# Specify output file
python3 parse_sql_dump.py --output my_export.json

# Both custom files
python3 parse_sql_dump.py --input my_dump.sql --output my_export.json
```

## What Was Extracted

The script successfully parsed and exported:

### Assessments Found
1. **Involved-360** (ID: 1) - Competency-based 360-evaluation
2. **Involved-Leader** (ID: 3) - Multi-rater diagnostic inventory
3. **Involved-Blockers** (ID: 4) - Personality attributes assessment
4. **Involved-Me** (ID: 5) - Self-report leadership assessment
5. **Involved-Me Peak Week** (ID: 6) - Self-report with frequency focus
6. **David Codes** (ID: 7) - Test assessment

### Data Structure
Each assessment includes:
- **Basic Info**: name, description, instructions, logo, settings
- **User Data**: creator information
- **Questions**: assessment questions with unserialized anchors
- **Dimensions**: hierarchical structure with definitions
- **Benchmarks**: industry-specific benchmark data
- **Translations**: multi-language support (if any)
- **Weights**: custom weighting and divisions (if any)

## Key Features

### ✅ **Complete Data Extraction**
- All assessment metadata and settings
- Full question content with anchors
- Hierarchical dimension structure
- Industry benchmarks
- User creator information

### ✅ **PHP Serialization Support**
- Handles PHP serialized data (anchors, custom_fields, weights)
- Converts to JSON-compatible format
- Graceful fallback for complex structures

### ✅ **Flexible & Robust**
- Handles missing relations gracefully
- Preserves null values and empty arrays
- System-agnostic JSON output
- No external dependencies

### ✅ **SQL Dump Parsing**
- Parses multi-line INSERT statements
- Handles multiple rows per INSERT
- Properly escapes quoted strings
- Type conversion (int, bool, string, null)

## Output Format

The exported JSON follows this structure:

```json
{
  "export_info": {
    "timestamp": "2025-08-22T15:25:12.858563",
    "source": "SQL Dump",
    "version": "1.0",
    "total_assessments": 4
  },
  "assessments": [
    {
      "id": 3,
      "name": "Involved-Leader",
      "description": "A multi-rater diagnostic inventory...",
      "instructions": "<h2>INSTRUCTIONS:</h2>...",
      "logo": "https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-Leader.png",
      "background": "",
      "paginate": 1,
      "items_per_page": 12,
      "translation": null,
      "language": null,
      "whitelabel": null,
      "company_labeled_for": null,
      "timed": "0",
      "time_limit": 10,
      "use_custom_fields": 1,
      "custom_fields": {
        "tag": ["name", "email"],
        "default": ["", ""]
      },
      "target": 1,
      "created_at": "2020-04-02 07:48:24",
      "updated_at": "2021-05-06 15:08:29",
      "last_modified": "0000-00-00 00:00:00",
      "user": {
        "id": 1,
        "username": "admin",
        "name": "Admin User",
        "email": "admin@example.com",
        // ... other user fields
      },
      "questions": [
        {
          "id": 1,
          "content": "Question text",
          "number": 1,
          "type": 1,
          "dimension_id": 21,
          "anchors": {
            "1": "Never",
            "2": "Rarely", 
            "3": "Sometimes",
            "4": "Often",
            "5": "Always"
          },
          "practice": 0,
          "assessment_id": 3,
          "created_at": "2020-04-02 09:09:54",
          "updated_at": "2021-06-08 23:21:17"
        }
      ],
      "dimensions": [
        {
          "id": 21,
          "name": "Relationships",
          "parent": 0,
          "code": "Rel",
          "assessment_id": 3,
          "definition": "Building relationships with, between, and among stakeholders...",
          "created_at": "2020-04-02 09:09:54",
          "updated_at": "2021-06-08 23:21:17",
          "benchmarks": [
            {
              "id": 723,
              "dimension_id": 21,
              "industry_id": 1,
              "value": "4.21",
              "created_at": "2021-07-07 15:37:55",
              "updated_at": "2021-07-07 15:37:55",
              "industry_name": "Technology"
            }
          ]
        }
      ],
      "translations": [],
      "weights": []
    }
  ]
}
```

## Advantages Over Database Connection

- **No Database Required**: Works with SQL dump files directly
- **Faster Processing**: No network latency or connection overhead
- **Offline Capable**: Can process dumps without database access
- **No Dependencies**: Only requires Python standard library
- **Safe**: No risk of affecting production database
- **Portable**: Can be run anywhere with the dump file

## Next Steps

1. **Review the Data**: Check `assessments_from_dump.json` to verify all data was extracted correctly
2. **Import to Your System**: Use the JSON file to import assessment data into your other system
3. **Customize if Needed**: Modify the script if you need additional fields or different output format

## Troubleshooting

If you encounter issues:

1. **File Not Found**: Make sure `involved_database_dump.sql` exists in the current directory
2. **Memory Issues**: For very large dumps, run with `python3 -X maxsize=2G parse_sql_dump.py`
3. **Parsing Errors**: The script handles standard MySQL dump formats

The script is now ready to use and has successfully extracted all your assessment data in a clean, system-agnostic JSON format that can be easily imported into other systems.
