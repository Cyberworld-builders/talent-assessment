#!/usr/bin/env python3
"""
SQL Dump Assessment Data Parser
===============================

This script parses a MySQL dump file and extracts all assessment data
including related questions, dimensions, translations, weights, and user data
to a JSON file. It's designed to be flexible and handle missing relations gracefully.

Usage:
    python3 parse_sql_dump.py [--input dump.sql] [--output output.json]

Requirements:
    No external dependencies - uses only Python standard library
"""

import json
import argparse
import sys
import os
import re
from datetime import datetime
from typing import Dict, List, Any, Optional


class SQLDumpParser:
    def __init__(self, dump_file: str):
        """Initialize the parser with the SQL dump file."""
        self.dump_file = dump_file
        self.data = {}
        
    def parse_dump(self) -> Dict[str, Any]:
        """Parse the entire SQL dump file."""
        print(f"Parsing SQL dump file: {self.dump_file}")
        
        with open(self.dump_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Parse each table
        self.data['assessments'] = self.parse_table_data(content, 'assessments')
        self.data['questions'] = self.parse_table_data(content, 'questions')
        self.data['dimensions'] = self.parse_table_data(content, 'dimensions')
        self.data['translations'] = self.parse_table_data(content, 'translations')
        self.data['translated_questions'] = self.parse_table_data(content, 'translated_questions')
        self.data['weights'] = self.parse_table_data(content, 'weights')
        self.data['users'] = self.parse_table_data(content, 'users')
        self.data['languages'] = self.parse_table_data(content, 'languages')
        self.data['industries'] = self.parse_table_data(content, 'industries')
        self.data['benchmarks'] = self.parse_table_data(content, 'benchmarks')
        
        return self.data
    
    def parse_table_data(self, content: str, table_name: str) -> List[Dict]:
        """Parse data for a specific table from the SQL dump."""
        print(f"Parsing table: {table_name}")
        
        # Find the INSERT statements for this table
        # Handle multi-line INSERT statements with multiple rows
        pattern = rf"INSERT INTO `{table_name}` VALUES\s*\((.*?)\);"
        matches = re.findall(pattern, content, re.DOTALL | re.IGNORECASE)
        
        if not matches:
            print(f"No data found for table: {table_name}")
            return []
        
        # Parse each INSERT statement
        table_data = []
        for match in matches:
            # Clean up the match - remove newlines and extra whitespace
            cleaned_match = re.sub(r'\s+', ' ', match.strip())
            
            # Split by "),(" to handle multiple rows in one INSERT statement
            if '),(' in cleaned_match:
                # Multiple rows in one INSERT
                row_parts = cleaned_match.split('),(')
                for i, part in enumerate(row_parts):
                    if i == 0:
                        # First row - remove leading (
                        part = part[1:]
                    elif i == len(row_parts) - 1:
                        # Last row - remove trailing )
                        part = part[:-1]
                    
                    values = self.parse_insert_values(part)
                    if values:
                        table_data.append(values)
            else:
                # Single row
                values = self.parse_insert_values(cleaned_match)
                if values:
                    table_data.append(values)
        
        print(f"Found {len(table_data)} records for {table_name}")
        return table_data
    
    def parse_insert_values(self, values_str: str) -> Optional[Dict]:
        """Parse the values from an INSERT statement into a dictionary."""
        try:
            # Split by comma, but be careful with quoted strings
            values = self.split_insert_values(values_str)
            
            # Convert values to appropriate types
            processed_values = []
            for value in values:
                processed_values.append(self.process_value(value))
            
            # For now, return as list - we'll need to map to column names
            return processed_values
            
        except Exception as e:
            print(f"Error parsing values: {e}")
            return None
    
    def split_insert_values(self, values_str: str) -> List[str]:
        """Split INSERT values by comma, handling quoted strings."""
        values = []
        current_value = ""
        in_quotes = False
        quote_char = None
        i = 0
        
        while i < len(values_str):
            char = values_str[i]
            
            if not in_quotes:
                if char in ["'", '"']:
                    in_quotes = True
                    quote_char = char
                    current_value += char
                elif char == ',':
                    values.append(current_value.strip())
                    current_value = ""
                else:
                    current_value += char
            else:
                current_value += char
                if char == quote_char:
                    # Check if it's escaped
                    if i + 1 < len(values_str) and values_str[i + 1] == quote_char:
                        i += 1  # Skip the escaped quote
                        current_value += values_str[i]
                    else:
                        in_quotes = False
                        quote_char = None
            
            i += 1
        
        # Add the last value
        if current_value.strip():
            values.append(current_value.strip())
        
        return values
    
    def process_value(self, value: str) -> Any:
        """Process a single value from the INSERT statement."""
        value = value.strip()
        
        # Handle NULL
        if value.upper() == 'NULL':
            return None
        
        # Handle quoted strings
        if (value.startswith("'") and value.endswith("'")) or \
           (value.startswith('"') and value.endswith('"')):
            # Remove quotes and handle escaped quotes
            unquoted = value[1:-1]
            unquoted = unquoted.replace("''", "'").replace('""', '"')
            return unquoted
        
        # Handle integers
        try:
            return int(value)
        except ValueError:
            pass
        
        # Handle floats
        try:
            return float(value)
        except ValueError:
            pass
        
        # Handle booleans (MySQL tinyint(1))
        if value in ['0', '1']:
            return bool(int(value))
        
        # Return as string
        return value
    
    def safe_unserialize(self, data: str) -> Any:
        """Safely unserialize PHP serialized data."""
        if not data:
            return None
        
        try:
            # Simple PHP unserialize for common cases
            if data.startswith('a:'):
                # Array format: a:count:{key;value;key;value;}
                return self._parse_php_array(data)
            elif data.startswith('s:'):
                # String format: s:length:"value";
                return self._parse_php_string(data)
            elif data.startswith('i:'):
                # Integer format: i:value;
                return self._parse_php_int(data)
            elif data.startswith('b:'):
                # Boolean format: b:value;
                return self._parse_php_bool(data)
            else:
                # Try to parse as JSON first
                try:
                    return json.loads(data)
                except:
                    # Return as-is if not parseable
                    return data
        except Exception as e:
            print(f"Failed to unserialize data: {e}")
            return data
    
    def _parse_php_array(self, data: str) -> Dict:
        """Parse PHP array format."""
        try:
            # Very basic PHP array parser
            if data.count('{') > 1:
                return data  # Too complex, return as string
            
            # Extract content between { and }
            start = data.find('{') + 1
            end = data.rfind('}')
            content = data[start:end]
            
            # Simple key-value parsing
            result = {}
            parts = content.split(';')
            for i in range(0, len(parts) - 1, 2):
                if i + 1 < len(parts):
                    key = parts[i].strip()
                    value = parts[i + 1].strip()
                    if key and value:
                        result[key] = value
            return result
        except:
            return data
    
    def _parse_php_string(self, data: str) -> str:
        """Parse PHP string format."""
        try:
            # Format: s:length:"value";
            start = data.find('"') + 1
            end = data.rfind('"')
            return data[start:end]
        except:
            return data
    
    def _parse_php_int(self, data: str) -> int:
        """Parse PHP integer format."""
        try:
            # Format: i:value;
            return int(data.split(':')[1].split(';')[0])
        except:
            return 0
    
    def _parse_php_bool(self, data: str) -> bool:
        """Parse PHP boolean format."""
        try:
            # Format: b:value;
            return bool(int(data.split(':')[1].split(';')[0]))
        except:
            return False


class AssessmentDataProcessor:
    def __init__(self, parsed_data: Dict[str, List]):
        """Initialize the processor with parsed SQL data."""
        self.data = parsed_data
        
        # Define column mappings for each table
        self.column_mappings = {
            'assessments': [
                'id', 'user_id', 'name', 'description', 'instructions', 'logo', 'background',
                'paginate', 'items_per_page', 'translation', 'language', 'whitelabel',
                'company_labeled_for', 'timed', 'time_limit', 'created_at', 'updated_at',
                'last_modified', 'use_custom_fields', 'custom_fields', 'target'
            ],
            'questions': [
                'id', 'content', 'assessment_id', 'number', 'type', 'dimension_id',
                'anchors', 'created_at', 'updated_at', 'practice'
            ],
            'dimensions': [
                'id', 'name', 'parent', 'code', 'created_at', 'updated_at',
                'assessment_id', 'definition'
            ],
            'translations': [
                'id', 'user_id', 'assessment_id', 'language_id', 'name', 'description',
                'created_at', 'updated_at', 'instructions'
            ],
            'translated_questions': [
                'id', 'translation_id', 'question_id', 'content', 'anchors',
                'created_at', 'updated_at'
            ],
            'weights': [
                'id', 'survey_id', 'assessment_id', 'weights', 'divisions',
                'created_at', 'updated_at'
            ],
            'users': [
                'id', 'name', 'username', 'password', 'remember_token', 'created_at',
                'updated_at', 'client_id', 'email', 'last_login_at', 'completed_profile',
                'language_id', 'accepted_terms', 'accepted_at', 'accepted_signature',
                'job_title', 'job_family', 'completed_research', 'industry_id',
                'timezone', 'registered', 'verified_email', 'picture'
            ],
            'languages': [
                'id', 'name', 'native_name', 'code', 'terms'
            ],
            'industries': [
                'id', 'name', 'created_at', 'updated_at'
            ],
            'benchmarks': [
                'id', 'dimension_id', 'industry_id', 'value', 'created_at', 'updated_at'
            ]
        }
    
    def convert_to_dicts(self) -> Dict[str, List[Dict]]:
        """Convert raw parsed data to dictionaries with proper column names."""
        result = {}
        
        for table_name, raw_data in self.data.items():
            if table_name not in self.column_mappings:
                continue
                
            columns = self.column_mappings[table_name]
            table_dicts = []
            
            for row in raw_data:
                if len(row) == len(columns):
                    row_dict = dict(zip(columns, row))
                    
                    # Process special fields
                    if table_name == 'questions' and row_dict.get('anchors'):
                        row_dict['anchors'] = self.safe_unserialize(row_dict['anchors'])
                    
                    if table_name == 'assessments' and row_dict.get('custom_fields'):
                        row_dict['custom_fields'] = self.safe_unserialize(row_dict['custom_fields'])
                    
                    if table_name == 'weights':
                        if row_dict.get('weights'):
                            row_dict['weights'] = self.safe_unserialize(row_dict['weights'])
                        if row_dict.get('divisions'):
                            row_dict['divisions'] = self.safe_unserialize(row_dict['divisions'])
                    
                    if table_name == 'translated_questions' and row_dict.get('anchors'):
                        row_dict['anchors'] = self.safe_unserialize(row_dict['anchors'])
                    
                    table_dicts.append(row_dict)
            
            result[table_name] = table_dicts
        
        return result
    
    def safe_unserialize(self, data: str) -> Any:
        """Safely unserialize PHP serialized data."""
        if not data:
            return None
        
        try:
            # Simple PHP unserialize for common cases
            if data.startswith('a:'):
                # Array format: a:count:{key;value;key;value;}
                return self._parse_php_array(data)
            elif data.startswith('s:'):
                # String format: s:length:"value";
                return self._parse_php_string(data)
            elif data.startswith('i:'):
                # Integer format: i:value;
                return self._parse_php_int(data)
            elif data.startswith('b:'):
                # Boolean format: b:value;
                return self._parse_php_bool(data)
            else:
                # Try to parse as JSON first
                try:
                    return json.loads(data)
                except:
                    # Return as-is if not parseable
                    return data
        except Exception as e:
            print(f"Failed to unserialize data: {e}")
            return data
    
    def _parse_php_array(self, data: str) -> Dict:
        """Parse PHP array format."""
        try:
            # Very basic PHP array parser
            if data.count('{') > 1:
                return data  # Too complex, return as string
            
            # Extract content between { and }
            start = data.find('{') + 1
            end = data.rfind('}')
            content = data[start:end]
            
            # Simple key-value parsing
            result = {}
            parts = content.split(';')
            for i in range(0, len(parts) - 1, 2):
                if i + 1 < len(parts):
                    key = parts[i].strip()
                    value = parts[i + 1].strip()
                    if key and value:
                        result[key] = value
            return result
        except:
            return data
    
    def _parse_php_string(self, data: str) -> str:
        """Parse PHP string format."""
        try:
            start = data.find('"') + 1
            end = data.rfind('"')
            return data[start:end]
        except:
            return data
    
    def _parse_php_int(self, data: str) -> int:
        """Parse PHP integer format."""
        try:
            return int(data.split(':')[1].split(';')[0])
        except:
            return 0
    
    def _parse_php_bool(self, data: str) -> bool:
        """Parse PHP boolean format."""
        try:
            return bool(int(data.split(':')[1].split(';')[0]))
        except:
            return False
    
    def build_assessment_hierarchy(self, processed_data: Dict[str, List[Dict]]) -> Dict[str, Any]:
        """Build the assessment hierarchy with all related data."""
        print("Building assessment hierarchy...")
        
        # Create lookup dictionaries
        users_lookup = {user['id']: user for user in processed_data.get('users', [])}
        languages_lookup = {lang['id']: lang for lang in processed_data.get('languages', [])}
        industries_lookup = {ind['id']: ind for ind in processed_data.get('industries', [])}
        
        result = {
            "export_info": {
                "timestamp": datetime.now().isoformat(),
                "source": "SQL Dump",
                "version": "1.0"
            },
            "assessments": []
        }
        
        # Process each assessment
        for assessment in processed_data.get('assessments', []):
            assessment_id = assessment['id']
            
            assessment_data = {
                "id": assessment['id'],
                "name": assessment.get('name'),
                "description": assessment.get('description'),
                "instructions": assessment.get('instructions'),
                "logo": assessment.get('logo'),
                "background": assessment.get('background'),
                "paginate": assessment.get('paginate'),
                "items_per_page": assessment.get('items_per_page'),
                "translation": assessment.get('translation'),
                "language": assessment.get('language'),
                "whitelabel": assessment.get('whitelabel'),
                "company_labeled_for": assessment.get('company_labeled_for'),
                "timed": assessment.get('timed'),
                "time_limit": assessment.get('time_limit'),
                "use_custom_fields": assessment.get('use_custom_fields'),
                "custom_fields": assessment.get('custom_fields'),
                "target": assessment.get('target'),
                "created_at": assessment.get('created_at'),
                "updated_at": assessment.get('updated_at'),
                "last_modified": assessment.get('last_modified'),
                
                # Related data
                "user": None,
                "questions": [],
                "dimensions": [],
                "translations": [],
                "weights": []
            }
            
            # Get user data
            if assessment.get('user_id') and assessment['user_id'] in users_lookup:
                assessment_data['user'] = users_lookup[assessment['user_id']]
            
            # Get questions
            assessment_data['questions'] = [
                q for q in processed_data.get('questions', [])
                if q.get('assessment_id') == assessment_id
            ]
            
            # Get dimensions
            dimensions = [
                d for d in processed_data.get('dimensions', [])
                if d.get('assessment_id') == assessment_id
            ]
            
            # Add benchmarks to dimensions
            for dimension in dimensions:
                dimension_id = dimension['id']
                dimension['benchmarks'] = []
                
                for benchmark in processed_data.get('benchmarks', []):
                    if benchmark.get('dimension_id') == dimension_id:
                        benchmark_data = benchmark.copy()
                        # Add industry name if available
                        if benchmark.get('industry_id') and benchmark['industry_id'] in industries_lookup:
                            benchmark_data['industry_name'] = industries_lookup[benchmark['industry_id']]['name']
                        dimension['benchmarks'].append(benchmark_data)
            
            assessment_data['dimensions'] = dimensions
            
            # Get translations
            translations = [
                t for t in processed_data.get('translations', [])
                if t.get('assessment_id') == assessment_id
            ]
            
            # Add translated questions to translations
            for translation in translations:
                translation_id = translation['id']
                translation['translated_questions'] = [
                    tq for tq in processed_data.get('translated_questions', [])
                    if tq.get('translation_id') == translation_id
                ]
                
                # Add language info
                if translation.get('language_id') and translation['language_id'] in languages_lookup:
                    lang = languages_lookup[translation['language_id']]
                    translation['language_name'] = lang['name']
                    translation['language_code'] = lang['code']
            
            assessment_data['translations'] = translations
            
            # Get weights
            assessment_data['weights'] = [
                w for w in processed_data.get('weights', [])
                if w.get('assessment_id') == assessment_id
            ]
            
            result['assessments'].append(assessment_data)
        
        result['export_info']['total_assessments'] = len(result['assessments'])
        print(f"Built hierarchy for {len(result['assessments'])} assessments")
        
        return result


def main():
    parser = argparse.ArgumentParser(description='Parse SQL dump and extract assessment data')
    parser.add_argument('--input', default='involved_database_dump.sql',
                       help='Input SQL dump file (default: involved_database_dump.sql)')
    parser.add_argument('--output', default='assessments_from_dump.json',
                       help='Output JSON file (default: assessments_from_dump.json)')
    
    args = parser.parse_args()
    
    # Check if input file exists
    if not os.path.exists(args.input):
        print(f"Error: Input file '{args.input}' not found")
        sys.exit(1)
    
    # Parse the SQL dump
    parser = SQLDumpParser(args.input)
    parsed_data = parser.parse_dump()
    
    # Process the data
    processor = AssessmentDataProcessor(parsed_data)
    processed_data = processor.convert_to_dicts()
    
    # Build the assessment hierarchy
    result = processor.build_assessment_hierarchy(processed_data)
    
    # Write to file
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(result, f, indent=2, ensure_ascii=False, default=str)
    
    print(f"Data exported to {args.output}")
    print(f"Total assessments: {result['export_info']['total_assessments']}")


if __name__ == '__main__':
    main()
