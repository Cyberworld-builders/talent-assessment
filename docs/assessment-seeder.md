# 360 Assessment Campaign Seeder

We need a way to simulate a massive number of completed assessments in order to test the report generation functionality.

## Overview

The seeder creates a complete 360 assessment campaign with realistic data for testing report generation.

## What it Creates

1. **Client**: Stark Industries
2. **Users**: 30 users (Marvel and Disney characters)
3. **Groups**: 3 groups of 10 users each with designated targets
4. **Assignments**: 360 assessments for all users rating their targets
5. **Responses**: Completed assessments with organic responses that demonstrate trends

## Implementation

The seeder has been created at: `database/seeds/StarkIndustries360CampaignSeeder.php`

### Usage

#### Prerequisites

First, ensure the Involved-360 assessment exists:

```bash
# Docker environment
docker compose exec app php artisan db:seed --class=Involved360AssessmentSeeder
```

#### Run the Seeder

```bash
# Docker environment
docker compose exec app php artisan db:seed --class=StarkIndustries360CampaignSeeder
```

### What Gets Created

#### Client: Stark Industries
- **Address**: 200 Park Avenue, New York, NY 10166
- **Colors**: Red (#C8102E) and Gold (#FFD700)
- **Assessments**: Involved-360

#### Group 1: Avengers (Target: Tony Stark)
1. **Tony Stark** - CEO (Target)
2. Steve Rogers - Director of Operations
3. Bruce Banner - Chief Scientist
4. Natasha Romanoff - Director of Security
5. Thor - VP of Energy
6. Clint Barton - VP of Precision Engineering
7. Nick Fury - Chief Strategy Officer
8. Agent Coulson - Senior Manager
9. Colby Wilson - Project Manager
10. Jarvis - AI Systems Administrator

#### Group 2: X-Men (Target: Cyclops)
1. Wolverine - VP of Manufacturing
2. **Cyclops** - Director of Vision Tech (Target)
3. Storm - VP of Environmental Systems
4. Jean Grey - Chief Innovation Officer
5. Iceman - VP of Cryogenics
6. Beast - Chief Technology Officer
7. Angel - VP of Aviation
8. Shadowcat - Senior Developer
9. Jubilee - Marketing Manager
10. Gambit - Director of Risk Management

#### Group 3: Radiator Springs Racers (Target: Lightning McQueen)
1. **Lightning McQueen** - VP of Automotive Division (Target)
2. Mater - Director of Maintenance
3. Doc Hudson - Senior Advisor
4. Sally Carrera - VP of Community Relations
5. Ramone - Director of Design
6. Strip Weathers - Executive Consultant
7. Fillmore - VP of Alternative Energy
8. Sheriff - Director of Compliance
9. Flo - VP of Hospitality
10. Luigi - Director of Supply Chain

### Test Data Characteristics

#### Performance Profiles

Each target has a unique performance profile to create realistic trends:

**Tony Stark** (High performer with some collaboration gaps):
- Creative Problem Solving: 4.5/5
- Leadership Adaptability: 4.0/5
- Collaboration: 3.5/5 (opportunity area)
- Self-Development: 4.8/5
- Performance Management: 3.8/5
- Business Mindset: 4.9/5

**Cyclops** (Strong collaborator, solid all-around):
- Creative Problem Solving: 3.8/5
- Leadership Adaptability: 4.2/5
- Collaboration: 4.5/5
- Self-Development: 3.9/5
- Performance Management: 4.3/5
- Business Mindset: 3.7/5

**Lightning McQueen** (Developing leader):
- Creative Problem Solving: 3.5/5
- Leadership Adaptability: 3.2/5 (opportunity area)
- Collaboration: 3.0/5 (opportunity area)
- Self-Development: 4.5/5
- Performance Management: 3.5/5
- Business Mindset: 3.8/5

#### Relationship Bias Simulation

The seeder simulates realistic rating biases based on relationships:
- **Self**: +0.3 (people rate themselves higher)
- **Direct Report**: +0.2 (direct reports tend to rate higher)
- **Supervisor**: -0.1 (supervisors are more critical)
- **Peer**: 0.0 (peers are most accurate)
- **Other**: +0.1

#### Feedback Generation

- 70% of responses include written feedback
- Feedback is contextual based on:
  - Target's name
  - Dimension being rated
  - Relationship to target
  - Performance score for that dimension
- Positive feedback for scores ≥ 4.0
- Constructive feedback for scores < 4.0

### Testing Reports

After running the seeder, you can test report generation:

1. **Login** as any user:
   - Username: `tony.stark` (or any other character)
   - Password: `password`

2. **View 360 Reports**:
   - Navigate to the Development Reports section
   - Select assignments from the survey date (7 days ago)
   - View reports for Tony Stark, Cyclops, or Lightning McQueen

3. **Test Report Features**:
   - Multi-source feedback (Self, Direct Report, Supervisor, Peer, Others)
   - Score visualization across 6 dimensions
   - Written feedback organized by relationship
   - Performance trends and patterns
   - PDF export functionality

### Cleanup

To remove the test data:

```bash
# Access MySQL
docker compose exec mysql mysql -u [user] -p[password] [database]

# Delete client and related data
DELETE FROM clients WHERE name = 'Stark Industries';
DELETE FROM users WHERE email LIKE '%@starkindustries.com';
```

Or simply re-run the seeder (it will delete and recreate automatically).
