# Assessment Data Export Script

This standalone Python script exports all assessment data from a MySQL database to JSON format, including all related data such as questions, dimensions, translations, weights, and user information.

## Features

- **Complete Data Export**: Exports all assessment data with full relationship hierarchy
- **PHP Serialization Support**: Handles PHP serialized data (anchors, custom_fields, weights, divisions)
- **Flexible Configuration**: Supports both config file and command-line parameters
- **Error Handling**: Gracefully handles missing relations, null values, and empty lists
- **System Agnostic**: Output JSON is designed to be compatible with other systems
- **Standalone**: No Laravel or PHP dependencies required

## Data Structure Exported

The script exports the following structure:

```json
{
  "export_info": {
    "timestamp": "2025-08-22T14:30:00",
    "total_assessments": 10,
    "version": "1.0"
  },
  "assessments": [
    {
      "id": 1,
      "name": "Assessment Name",
      "description": "Assessment Description",
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
        "client_id": 1,
        "language_id": 1,
        "completed_profile": true,
        "completed_research": false,
        "job_title": "Developer",
        "created_at": "2025-01-01 00:00:00",
        "updated_at": "2025-01-01 00:00:00"
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
          "language_name": "Spanish",
          "language_code": "es",
          "translated_questions": [
            {
              "id": 1,
              "question_id": 1,
              "content": "Pregunta en español",
              "translation_id": 1,
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
          "job_id": 1,
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

## Installation Instructions

### 1. Upload Files to Server

Upload these files to your Digital Ocean server:

```bash
# Create a directory for the export script
mkdir assessment_export
cd assessment_export

# Upload the files (you can copy-paste the content or use scp)
# - export_assessments.py
# - db_config.json (optional, you can use command line args)
# - README_export.md (this file)
```

### 2. Install Python Dependencies

```bash
# Update package list
sudo apt update

# Install Python 3 and pip if not already installed
sudo apt install python3 python3-pip -y

# Install required Python package
pip3 install pymysql
```

### 3. Configure Database Connection

**Option A: Using Configuration File**

Edit `db_config.json` with your database credentials:

```json
{
  "host": "your_database_host",
  "port": 3306,
  "username": "your_database_username",
  "password": "your_database_password",
  "database": "your_database_name"
}
```

**Option B: Using Command Line Arguments**

You can provide database credentials directly via command line (see usage below).

### 4. Make Script Executable

```bash
chmod +x export_assessments.py
```

## Usage

### Basic Usage (with config file)

```bash
python3 export_assessments.py
```

This will use `db_config.json` and output to `assessments_export.json`.

### Advanced Usage

```bash
# Use custom config file
python3 export_assessments.py --config my_config.json

# Specify output file
python3 export_assessments.py --output my_export.json

# Use command line arguments instead of config file
python3 export_assessments.py \
  --host localhost \
  --port 3306 \
  --user myuser \
  --password mypassword \
  --database talent_assessment \
  --output export.json

# Combine options
python3 export_assessments.py \
  --config production_db.json \
  --output production_export.json
```

### Command Line Options

- `--config`: Database configuration file (default: db_config.json)
- `--output`: Output JSON file (default: assessments_export.json)
- `--host`: Database host
- `--port`: Database port (default: 3306)
- `--user`: Database username
- `--password`: Database password
- `--database`: Database name

## Error Handling

The script is designed to be robust and handle various edge cases:

- **Missing Relations**: If a user, dimension, or translation doesn't exist, it will be set to `null` or empty array
- **PHP Serialized Data**: Handles PHP serialized strings and converts them to JSON-compatible format
- **Null Values**: All fields can be null/undefined and will be preserved in output
- **Empty Lists**: Relations that have no data will be exported as empty arrays
- **Database Errors**: Connection and query errors are logged but don't stop the export

## Output Format

The exported JSON is designed to be:

- **System Agnostic**: No Laravel-specific dependencies
- **Flexible**: Handles missing data gracefully
- **Complete**: Includes all assessment data and relationships
- **Structured**: Well-organized hierarchy for easy processing
- **UTF-8 Encoded**: Properly handles international characters

## Troubleshooting

### Connection Issues

```bash
# Test database connection
mysql -h your_host -u your_user -p your_database

# Check if port is accessible
telnet your_host 3306
```

### Permission Issues

```bash
# Make sure script is executable
chmod +x export_assessments.py

# Check file permissions
ls -la export_assessments.py
```

### Python Issues

```bash
# Check Python version
python3 --version

# Check if pymysql is installed
python3 -c "import pymysql; print('PyMySQL installed')"
```

### Memory Issues

For large datasets, you might need to increase memory limits:

```bash
# Run with more memory
python3 -X maxsize=2G export_assessments.py
```

## Security Notes

- Store database credentials securely
- Use environment variables for production deployments
- Consider using read-only database user for exports
- Delete or secure the exported JSON file after use

## Example Output

After running the script, you'll see output like:

```
Starting assessment data export...
Found 15 assessments
Processing assessment 1/15: Personality Assessment
Processing assessment 2/15: Technical Skills Test
...
Processing assessment 15/15: Leadership Assessment
Export completed successfully!
Data exported to assessments_export.json
Total assessments: 15
```

The resulting JSON file will contain all assessment data in a structured, system-agnostic format that can be imported into other systems.
