# 360 Assessment Campaign Seeder

## Quick Start

### 1. Prerequisites

Ensure the Involved-360 assessment exists:

```bash
docker compose exec app php artisan db:seed --class=Involved360AssessmentSeeder
```

### 2. Run the Campaign Seeder

```bash
docker compose exec app php artisan db:seed --class=StarkIndustries360CampaignSeeder
```

### 3. Login and Test

- **Username**: `tony.stark`
- **Password**: `password`

## What Gets Created

- **1 Client**: Stark Industries
- **30 Users**: Marvel and Disney characters
- **3 Groups**: Avengers, X-Men, Radiator Springs Racers
- **30 Assignments**: Each user rates their group's target
- **~900 Answers**: Realistic responses with trends

## Features

### Realistic Performance Profiles

Each target has unique strengths and development areas:

- **Tony Stark**: High achiever, needs collaboration work
- **Cyclops**: Strong collaborator, well-rounded
- **Lightning McQueen**: Developing leader with growth potential

### Relationship Bias

Simulates real-world rating patterns:
- Self-ratings: Slightly inflated (+0.3)
- Direct reports: Positive bias (+0.2)
- Supervisors: More critical (-0.1)
- Peers: Most accurate (0.0)

### Contextual Feedback

70% of responses include written comments:
- Tailored to target's name
- Specific to dimension
- Reflects relationship and score
- Mix of positive and constructive

## Testing Report Generation

After seeding, test these scenarios:

1. **View Multi-Source Reports**
   - Login as admin
   - Navigate to Development Reports
   - View reports for any of the 3 targets

2. **Compare Relationship Perspectives**
   - Check Self vs Direct Report vs Supervisor scores
   - Review feedback differences by relationship

3. **Identify Performance Trends**
   - Tony Stark: Strong on innovation, weaker on collaboration
   - Cyclops: Consistent across dimensions
   - Lightning McQueen: High self-development, lower adaptability

4. **Export to PDF**
   - Test PDF generation for each target
   - Verify all charts and feedback render correctly

## Troubleshooting

### "Involved-360 assessment not found"

Run the assessment seeder first:
```bash
docker compose exec app php artisan db:seed --class=Involved360AssessmentSeeder
```

### "Stark Industries already exists"

The seeder automatically cleans up existing data. Just re-run it.

### No assignments showing in reports

Check that:
1. Assignments were created 7 days ago (by design)
2. You're looking at the Development Reports section
3. You're filtering by the correct date range

## Data Cleanup

To remove all test data:

```bash
docker compose exec app php artisan tinker
```

Then in tinker:
```php
$client = App\Client::where('name', 'Stark Industries')->first();
if ($client) {
    App\User::where('client_id', $client->id)->delete();
    App\Group::where('client_id', $client->id)->delete();
    $client->delete();
}
```

Or simply re-run the seeder (it deletes and recreates automatically).

## Technical Details

### File Location
`database/seeds/StarkIndustries360CampaignSeeder.php`

### Full Documentation
See: `docs/assessment-seeder.md`

### Technical Report Documentation
See: `docs/360-report-technical-documentation.md`

## Support

For issues or questions, refer to:
- [Assessment Seeder Documentation](../../docs/assessment-seeder.md)
- [360 Report Technical Documentation](../../docs/360-report-technical-documentation.md)


