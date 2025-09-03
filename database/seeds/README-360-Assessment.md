# Involved-360 Assessment Seeders

This directory contains seeders for creating the complete Involved-360 assessment based on the JSON export data.

## Files

### 1. `Involved360AssessmentSeeder.php`
**Purpose**: Creates the basic assessment structure including:
- Assessment record with configuration
- 9 dimensions (Creative Problem Solving, Leadership Adaptability, Collaboration, etc.)
- Basic question structure (simplified)
- Industry benchmarks
- Basic industries

**Usage**: Run this first to establish the foundation.

### 2. `Involved360CompleteQuestionsSeeder.php`
**Purpose**: Creates the complete, detailed question set including:
- All 18 questions with full content from the JSON export
- Proper question types (Description, Multiple Choice, Text Input)
- Complete performance descriptors and rating scales
- Developmental comment fields for each dimension

**Usage**: Run this after the main seeder to populate the complete question set.

## Running the Seeders

### Option 1: Run All Seeders (Recommended)
```bash
php artisan db:seed
```
This will run all seeders including the 360 assessment.

### Option 2: Run Individual Seeders
```bash
# Run just the 360 assessment seeder
php artisan db:seed --class=Involved360AssessmentSeeder

# Run the complete questions seeder (after main seeder)
php artisan db:seed --class=Involved360CompleteQuestionsSeeder
```

### Option 3: Run from Docker
```bash
# If running in Docker environment
docker-compose exec app php artisan db:seed --class=Involved360AssessmentSeeder
docker-compose exec app php artisan db:seed --class=Involved360CompleteQuestionsSeeder
```

## Assessment Structure

The Involved-360 assessment consists of:

### Dimensions (9 total)
1. **Creative Problem Solving (CPS)** - Innovation and change management
2. **Leadership Adaptability (LA)** - Change leadership and flexibility
3. **Collaboration (CO)** - Stakeholder relationships and teamwork
4. **Self-Development (SD)** - Personal growth and learning
5. **Performance Management (PM)** - Team development and accountability
6. **Business Mindset (BM)** - Strategic thinking and business acumen
7. **Customer Focus (CF)** - Customer service and advocacy
8. **Communication (COM)** - Clear expression and active listening
9. **Ethics & Integrity (E&I)** - Honesty and moral leadership

### Question Types
- **Type 1**: Multiple Choice (5-point rating scale)
- **Type 2**: Description (instructions and performance descriptors)
- **Type 3**: Text Input (developmental comments)

### Question Pattern (per dimension)
Each dimension follows this 4-question pattern:
1. **Instructions**: Dimension definition and rating instructions
2. **Rating**: 5-point multiple choice rating scale
3. **Descriptors**: Performance level descriptions in table format
4. **Comments**: Text input field for developmental feedback

## Data Structure

### Assessment Configuration
- **Name**: "Involved-360"
- **Description**: Competency-based 360-evaluation
- **Pagination**: 4 questions per page
- **Custom Fields**: Name and email placeholders
- **Timing**: Not timed (0)

### Rating Scale
All dimensions use the same 5-point scale:
1. Below Expectations
2. Slightly Below Expectations
3. Meets Expectations
4. Slightly Exceeds Expectations
5. Exceeds Expectations

### Industry Benchmarks
The seeder creates sample benchmarks for 5 industries:
- Technology, Healthcare, Finance, Manufacturing, Education

## Customization

### Adding New Dimensions
1. Add the dimension to the `createDimensions()` method
2. Add corresponding questions to the `createCompleteQuestions()` method
3. Add industry benchmarks for the new dimension

### Modifying Question Content
1. Edit the HTML content in the `createCompleteQuestions()` method
2. Update the performance descriptors as needed
3. Adjust the rating scale if required

### Adding New Industries
1. Add industry names to the `createBasicIndustries()` method
2. Add corresponding benchmarks in `createIndustryBenchmarks()`

## Troubleshooting

### Common Issues

**"Assessment not found" error**
- Ensure `Involved360AssessmentSeeder` runs first
- Check that the assessment name matches exactly: "Involved-360"

**"Dimension not found" error**
- Verify all dimensions are created before running the questions seeder
- Check dimension codes match between seeders

**Database constraint errors**
- Ensure required tables exist (assessments, dimensions, questions, industries, benchmarks)
- Check foreign key relationships are properly set up

### Verification

After running the seeders, verify:
```sql
-- Check assessment creation
SELECT * FROM assessments WHERE name = 'Involved-360';

-- Check dimensions
SELECT * FROM dimensions WHERE assessment_id = [assessment_id];

-- Check questions
SELECT * FROM questions WHERE assessment_id = [assessment_id] ORDER BY number;

-- Check benchmarks
SELECT * FROM benchmarks WHERE dimension_id IN (SELECT id FROM dimensions WHERE assessment_id = [assessment_id]);
```

## Notes

- The seeders use `Carbon::now()` for timestamps to ensure consistency
- Question numbering follows the pattern from the JSON export
- Performance descriptors are simplified versions - customize as needed
- Industry benchmarks are sample data - adjust values based on your needs
- The seeder assumes user ID 1 exists - modify if needed

## Next Steps

After seeding:
1. Test the assessment in the application
2. Customize question content as needed
3. Adjust industry benchmarks based on your data
4. Add translations if multi-language support is required
5. Configure any additional assessment settings
