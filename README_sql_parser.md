# SQL Dump Assessment Data Parser

This script parses a MySQL dump file and extracts all assessment data including related questions, dimensions, translations, weights, and user information to JSON format.

## Features

- **SQL Dump Parsing**: Parses MySQL dump files without requiring database connection
- **Complete Data Export**: Extracts all assessment data with full relationship hierarchy
- **PHP Serialization Support**: Handles PHP serialized data (anchors, custom_fields, weights, divisions)
- **Flexible**: Handles missing relations, null values, and empty lists gracefully
- **System Agnostic**: Output JSON is designed to be compatible with other systems
- **Standalone**: No external dependencies - uses only Python standard library

## Usage

### Basic Usage

```bash
python3 parse_sql_dump.py
```

This will parse `involved_database_dump.sql` and output to `assessments_from_dump.json`.

### Advanced Usage

```bash
# Use custom input file
python3 parse_sql_dump.py --input my_dump.sql

# Specify output file
python3 parse_sql_dump.py --output my_export.json

# Use both custom files
python3 parse_sql_dump.py --input my_dump.sql --output my_export.json
```

### Command Line Options

- `--input`: Input SQL dump file (default: involved_database_dump.sql)
- `--output`: Output JSON file (default: assessments_from_dump.json)

## Data Structure Exported

The script exports the same structure as the database version:

```json
{
  "export_info": {
    "timestamp": "2025-08-22T14:30:00",
    "source": "SQL Dump",
    "version": "1.0",
    "total_assessments": 6
  },
  "assessments": [
    {
      "id": 1,
      "name": "Assessment Name",
      "description": "Assessment Description",
      "instructions": "Instructions text",
      "logo": "logo_url",
      "background": "background_url",
      "paginate": true,
      "items_per_page": 10,
      "translation": true,
      "language": "en",
      "whitelabel": false,
      "company_labeled_for": null,
      "timed": false,
      "time_limit": null,
      "use_custom_fields": false,
      "custom_fields": null,
      "target": 1,
      "created_at": "2025-01-01 00:00:00",
      "updated_at": "2025-01-01 00:00:00",
      "last_modified": "2025-01-01 00:00:00",
      "user": {
        "id": 1,
        "username": "user123",
        "name": "User Name",
        "email": "user@example.com",
        // ... other user fields
      },
      "questions": [
        {
          "id": 1,
          "content": "Question text",
          "number": 1,
          "type": 1,
          "dimension_id": 1,
          "anchors": {"1": "Strongly Disagree", "5": "Strongly Agree"},
          "practice": false,
          "assessment_id": 1,
          "created_at": "2025-01-01 00:00:00",
          "updated_at": "2025-01-01 00:00:00"
        }
      ],
      "dimensions": [
        {
          "id": 1,
          "name": "Dimension Name",
          "parent": 0,
          "code": "DIM1",
          "assessment_id": 1,
          "created_at": "2025-01-01 00:00:00",
          "updated_at": "2025-01-01 00:00:00",
          "definition": "Dimension definition",
          "benchmarks": [
            {
              "id": 1,
              "value": 75,
              "industry_id": 1,
              "dimension_id": 1,
              "created_at": "2025-01-01 00:00:00",
              "updated_at": "2025-01-01 00:00:00",
              "industry_name": "Technology"
            }
          ]
        }
      ],
      "translations": [
        {
          "id": 1,
          "name": "Spanish Translation",
          "description": "Spanish version",
          "assessment_id": 1,
          "language_id": 2,
          "created_at": "2025-01-01 00:00:00",
          "updated_at": "2025-01-01 00:00:00",
          "instructions": "Instructions in Spanish",
          "language_name": "Spanish",
          "language_code": "es",
          "translated_questions": [
            {
              "id": 1,
              "question_id": 1,
              "content": "Pregunta en español",
              "translation_id": 1,
              "anchors": {"1": "Totalmente en desacuerdo", "5": "Totalmente de acuerdo"},
              "created_at": "2025-01-01 00:00:00",
              "updated_at": "2025-01-01 00:00:00"
            }
          ]
        }
      ],
      "weights": [
        {
          "id": 1,
          "assessment_id": 1,
          "survey_id": 1,
          "weights": {"1": 0.5, "2": 0.3, "3": 0.2},
          "divisions": {"1": "Technical", "2": "Soft Skills"},
          "created_at": "2025-01-01 00:00:00",
          "updated_at": "2025-01-01 00:00:00"
        }
      ]
    }
  ]
}
```

## Tables Parsed

The script parses the following tables from the SQL dump:

- **assessments** - Main assessment data
- **questions** - Assessment questions with anchors
- **dimensions** - Assessment dimensions with hierarchical structure
- **translations** - Assessment translations for different languages
- **translated_questions** - Translated question content
- **weights** - Custom weights and divisions
- **users** - User data (creators of assessments)
- **languages** - Language information
- **industries** - Industry data for benchmarks
- **benchmarks** - Dimension benchmarks by industry

## Error Handling

The script is designed to be robust and handle various edge cases:

- **Missing Relations**: If a user, dimension, or translation doesn't exist, it will be set to `null` or empty array
- **PHP Serialized Data**: Handles PHP serialized strings and converts them to JSON-compatible format
- **Null Values**: All fields can be null/undefined and will be preserved in output
- **Empty Lists**: Relations that have no data will be exported as empty arrays
- **SQL Parsing Errors**: Connection and parsing errors are logged but don't stop the export

## Example Output

After running the script, you'll see output like:

```
Parsing SQL dump file: involved_database_dump.sql
Parsing table: assessments
Found 6 records for assessments
Parsing table: questions
Found 549 records for questions
Parsing table: dimensions
Found 91 records for dimensions
Parsing table: translations
Found 0 records for translations
Parsing table: translated_questions
Found 0 records for translated_questions
Parsing table: weights
Found 0 records for weights
Parsing table: users
Found 1389 records for users
Parsing table: languages
Found 2 records for languages
Parsing table: industries
Found 16 records for industries
Parsing table: benchmarks
Found 1234 records for benchmarks
Building assessment hierarchy...
Built hierarchy for 6 assessments
Data exported to assessments_from_dump.json
Total assessments: 6
```

## Advantages Over Database Connection

- **No Database Required**: Works with SQL dump files directly
- **Faster Processing**: No network latency or connection overhead
- **Offline Capable**: Can process dumps without database access
- **No Dependencies**: Only requires Python standard library
- **Safe**: No risk of affecting production database

## Troubleshooting

### File Not Found
```bash
Error: Input file 'involved_database_dump.sql' not found
```
Make sure the SQL dump file exists in the current directory or specify the correct path.

### Parsing Errors
If you see parsing errors, the SQL dump format might be different than expected. The script handles standard MySQL dump formats.

### Memory Issues
For very large dump files, you might need more memory:
```bash
python3 -X maxsize=2G parse_sql_dump.py
```

The resulting JSON file will contain all assessment data in a structured, system-agnostic format that can be imported into other systems.
