#!/usr/bin/env python3
"""
Assessment Data Export Script
=============================

This script connects to a MySQL database and exports all assessment data
including related questions, dimensions, translations, weights, and user data
to a JSON file. It's designed to be flexible and handle missing relations gracefully.

Usage:
    python3 export_assessments.py [--config config.json] [--output output.json]

Requirements:
    pip3 install pymysql
"""

import json
import argparse
import sys
import os
from datetime import datetime
from typing import Dict, List, Any, Optional
import pymysql
from pymysql.cursors import DictCursor


class AssessmentExporter:
    def __init__(self, db_config: Dict[str, Any]):
        """Initialize the exporter with database configuration."""
        self.db_config = db_config
        self.connection = None
        
    def connect(self) -> bool:
        """Establish database connection."""
        try:
            self.connection = pymysql.connect(
                host=self.db_config['host'],
                port=self.db_config.get('port', 3306),
                user=self.db_config['username'],
                password=self.db_config['password'],
                database=self.db_config['database'],
                charset='utf8mb4',
                cursorclass=DictCursor,
                autocommit=True
            )
            return True
        except Exception as e:
            print(f"Database connection failed: {e}")
            return False
    
    def disconnect(self):
        """Close database connection."""
        if self.connection:
            self.connection.close()
    
    def execute_query(self, query: str, params: tuple = None) -> List[Dict]:
        """Execute a query and return results as list of dictionaries."""
        try:
            with self.connection.cursor() as cursor:
                cursor.execute(query, params)
                return cursor.fetchall()
        except Exception as e:
            print(f"Query execution failed: {e}")
            print(f"Query: {query}")
            print(f"Params: {params}")
            return []
    
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
            # This is a simplified version - for complex nested arrays, return as string
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
    
    def get_assessments(self) -> List[Dict]:
        """Get all assessments with basic data."""
        query = """
        SELECT 
            id, user_id, name, description, logo, background, 
            paginate, items_per_page, translation, language,
            whitelabel, company_labeled_for, timed, time_limit,
            use_custom_fields, custom_fields, target,
            created_at, updated_at, last_modified
        FROM assessments
        ORDER BY id
        """
        return self.execute_query(query)
    
    def get_questions(self, assessment_id: int) -> List[Dict]:
        """Get all questions for an assessment."""
        query = """
        SELECT 
            id, content, number, type, dimension_id, anchors, practice,
            assessment_id, created_at, updated_at
        FROM questions 
        WHERE assessment_id = %s
        ORDER BY number
        """
        questions = self.execute_query(query, (assessment_id,))
        
        # Process anchors (unserialize)
        for question in questions:
            if question.get('anchors'):
                question['anchors'] = self.safe_unserialize(question['anchors'])
        
        return questions
    
    def get_dimensions(self, assessment_id: int) -> List[Dict]:
        """Get all dimensions for an assessment."""
        query = """
        SELECT 
            id, name, parent, code, assessment_id,
            created_at, updated_at
        FROM dimensions 
        WHERE assessment_id = %s
        ORDER BY id
        """
        return self.execute_query(query, (assessment_id,))
    
    def get_translations(self, assessment_id: int) -> List[Dict]:
        """Get all translations for an assessment."""
        query = """
        SELECT 
            t.id, t.name, t.description, t.assessment_id, t.language_id,
            t.created_at, t.updated_at,
            l.name as language_name, l.code as language_code
        FROM translations t
        LEFT JOIN languages l ON t.language_id = l.id
        WHERE t.assessment_id = %s
        ORDER BY t.language_id
        """
        return self.execute_query(query, (assessment_id,))
    
    def get_translated_questions(self, translation_id: int) -> List[Dict]:
        """Get translated questions for a translation."""
        query = """
        SELECT 
            id, question_id, content, translation_id,
            created_at, updated_at
        FROM translated_questions 
        WHERE translation_id = %s
        ORDER BY id
        """
        return self.execute_query(query, (translation_id,))
    
    def get_weights(self, assessment_id: int) -> List[Dict]:
        """Get all weights for an assessment."""
        query = """
        SELECT 
            id, assessment_id, job_id, weights, divisions,
            created_at, updated_at
        FROM weights 
        WHERE assessment_id = %s
        ORDER BY id
        """
        weights = self.execute_query(query, (assessment_id,))
        
        # Process serialized data
        for weight in weights:
            if weight.get('weights'):
                weight['weights'] = self.safe_unserialize(weight['weights'])
            if weight.get('divisions'):
                weight['divisions'] = self.safe_unserialize(weight['divisions'])
        
        return weights
    
    def get_user(self, user_id: int) -> Optional[Dict]:
        """Get user data for an assessment."""
        query = """
        SELECT 
            id, username, name, email, client_id, language_id,
            completed_profile, completed_research, job_title,
            created_at, updated_at
        FROM users 
        WHERE id = %s
        """
        users = self.execute_query(query, (user_id,))
        return users[0] if users else None
    
    def get_benchmarks(self, dimension_id: int) -> List[Dict]:
        """Get benchmarks for a dimension."""
        query = """
        SELECT 
            b.id, b.value, b.industry_id, b.dimension_id,
            b.created_at, b.updated_at,
            i.name as industry_name
        FROM benchmarks b
        LEFT JOIN industries i ON b.industry_id = i.id
        WHERE b.dimension_id = %s
        ORDER BY b.id
        """
        return self.execute_query(query, (dimension_id,))
    
    def export_assessment_data(self) -> Dict[str, Any]:
        """Export all assessment data to a structured format."""
        print("Starting assessment data export...")
        
        # Get all assessments
        assessments = self.get_assessments()
        print(f"Found {len(assessments)} assessments")
        
        result = {
            "export_info": {
                "timestamp": datetime.now().isoformat(),
                "total_assessments": len(assessments),
                "version": "1.0"
            },
            "assessments": []
        }
        
        for i, assessment in enumerate(assessments, 1):
            print(f"Processing assessment {i}/{len(assessments)}: {assessment.get('name', 'Unknown')}")
            
            assessment_data = {
                "id": assessment['id'],
                "name": assessment.get('name'),
                "description": assessment.get('description'),
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
                "custom_fields": self.safe_unserialize(assessment.get('custom_fields')),
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
            if assessment.get('user_id'):
                assessment_data['user'] = self.get_user(assessment['user_id'])
            
            # Get questions
            assessment_data['questions'] = self.get_questions(assessment['id'])
            
            # Get dimensions
            dimensions = self.get_dimensions(assessment['id'])
            for dimension in dimensions:
                # Get benchmarks for each dimension
                dimension['benchmarks'] = self.get_benchmarks(dimension['id'])
            assessment_data['dimensions'] = dimensions
            
            # Get translations
            translations = self.get_translations(assessment['id'])
            for translation in translations:
                # Get translated questions for each translation
                translation['translated_questions'] = self.get_translated_questions(translation['id'])
            assessment_data['translations'] = translations
            
            # Get weights
            assessment_data['weights'] = self.get_weights(assessment['id'])
            
            result['assessments'].append(assessment_data)
        
        print("Export completed successfully!")
        return result


def load_config(config_file: str) -> Dict[str, Any]:
    """Load database configuration from JSON file."""
    try:
        with open(config_file, 'r') as f:
            return json.load(f)
    except Exception as e:
        print(f"Failed to load config file {config_file}: {e}")
        sys.exit(1)


def main():
    parser = argparse.ArgumentParser(description='Export assessment data to JSON')
    parser.add_argument('--config', default='db_config.json', 
                       help='Database configuration file (default: db_config.json)')
    parser.add_argument('--output', default='assessments_export.json',
                       help='Output JSON file (default: assessments_export.json)')
    parser.add_argument('--host', help='Database host')
    parser.add_argument('--port', type=int, default=3306, help='Database port')
    parser.add_argument('--user', help='Database username')
    parser.add_argument('--password', help='Database password')
    parser.add_argument('--database', help='Database name')
    
    args = parser.parse_args()
    
    # Load configuration
    if os.path.exists(args.config):
        db_config = load_config(args.config)
    else:
        # Use command line arguments
        if not all([args.host, args.user, args.password, args.database]):
            print("Error: Either provide a config file or all database connection parameters")
            sys.exit(1)
        
        db_config = {
            'host': args.host,
            'port': args.port,
            'username': args.user,
            'password': args.password,
            'database': args.database
        }
    
    # Create exporter and run export
    exporter = AssessmentExporter(db_config)
    
    if not exporter.connect():
        sys.exit(1)
    
    try:
        data = exporter.export_assessment_data()
        
        # Write to file
        with open(args.output, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False, default=str)
        
        print(f"Data exported to {args.output}")
        print(f"Total assessments: {data['export_info']['total_assessments']}")
        
    finally:
        exporter.disconnect()


if __name__ == '__main__':
    main()
