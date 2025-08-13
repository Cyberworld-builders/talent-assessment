<?php

use App\Benchmark;
use App\Dimension;
use App\Industry;
use App\Assessment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BenchmarkTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that a benchmark can be created with proper relationships
     */
    public function testBenchmarkCanBeCreated()
    {
        // Create a user first
        $user = \App\User::create([
            'username' => 'testuser' . time(),
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => bcrypt('password')
        ]);

        // Create an assessment
        $assessment = Assessment::create([
            'name' => 'Test Assessment',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);

        // Create a dimension
        $dimension = Dimension::create([
            'name' => 'Test Dimension',
            'assessment_id' => $assessment->id
        ]);

        // Create an industry
        $industry = Industry::create([
            'name' => 'Test Industry'
        ]);

        // Create a benchmark
        $benchmark = Benchmark::create([
            'dimension_id' => $dimension->id,
            'industry_id' => $industry->id,
            'value' => '75'
        ]);

        // Assert the benchmark was created
        $this->assertInstanceOf('App\Benchmark', $benchmark);
        $this->assertEquals($dimension->id, $benchmark->dimension_id);
        $this->assertEquals($industry->id, $benchmark->industry_id);
        $this->assertEquals('75', $benchmark->value);
    }

    /**
     * Test benchmark relationships
     */
    public function testBenchmarkRelationships()
    {
        // Create a user first
        $user = \App\User::create([
            'username' => 'testuser' . time(),
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => bcrypt('password')
        ]);

        // Create test data
        $assessment = Assessment::create([
            'name' => 'Test Assessment',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);

        $dimension = Dimension::create([
            'name' => 'Test Dimension',
            'assessment_id' => $assessment->id
        ]);

        $industry = Industry::create([
            'name' => 'Test Industry'
        ]);

        $benchmark = Benchmark::create([
            'dimension_id' => $dimension->id,
            'industry_id' => $industry->id,
            'value' => '80'
        ]);

        // Test dimension relationship
        $this->assertInstanceOf('App\Dimension', $benchmark->dimension);
        $this->assertEquals('Test Dimension', $benchmark->dimension->name);

        // Test industry relationship
        $this->assertInstanceOf('App\Industry', $benchmark->industry);
        $this->assertEquals('Test Industry', $benchmark->industry->name);
    }

    /**
     * Test benchmark scopes
     */
    public function testBenchmarkScopes()
    {
        // Create a user first
        $user = \App\User::create([
            'username' => 'testuser' . time(),
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => bcrypt('password')
        ]);

        // Create test data
        $assessment = Assessment::create([
            'name' => 'Test Assessment',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);

        $dimension1 = Dimension::create([
            'name' => 'Dimension 1',
            'assessment_id' => $assessment->id
        ]);

        $dimension2 = Dimension::create([
            'name' => 'Dimension 2',
            'assessment_id' => $assessment->id
        ]);

        $industry1 = Industry::create(['name' => 'Industry 1']);
        $industry2 = Industry::create(['name' => 'Industry 2']);

        // Create benchmarks
        Benchmark::create([
            'dimension_id' => $dimension1->id,
            'industry_id' => $industry1->id,
            'value' => '75'
        ]);

        Benchmark::create([
            'dimension_id' => $dimension2->id,
            'industry_id' => $industry1->id,
            'value' => '80'
        ]);

        Benchmark::create([
            'dimension_id' => $dimension1->id,
            'industry_id' => $industry2->id,
            'value' => '85'
        ]);

        // Test forIndustry scope
        $industry1Benchmarks = Benchmark::forIndustry($industry1->id)->get();
        $this->assertEquals(2, $industry1Benchmarks->count());

        // Test forDimension scope
        $dimension1Benchmarks = Benchmark::forDimension($dimension1->id)->get();
        $this->assertEquals(2, $dimension1Benchmarks->count());

        // Test forAssessment scope
        $assessmentBenchmarks = Benchmark::forAssessment($assessment->id)->get();
        $this->assertEquals(3, $assessmentBenchmarks->count(), 'Expected 3 benchmarks for assessment, got ' . $assessmentBenchmarks->count());
    }

    /**
     * Test unique constraint on dimension_id and industry_id
     */
    public function testBenchmarkUniqueConstraint()
    {
        // Create a user first
        $user = \App\User::create([
            'username' => 'testuser' . time(),
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => bcrypt('password')
        ]);

        // Create test data
        $assessment = Assessment::create([
            'name' => 'Test Assessment',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);

        $dimension = Dimension::create([
            'name' => 'Test Dimension',
            'assessment_id' => $assessment->id
        ]);

        $industry = Industry::create([
            'name' => 'Test Industry'
        ]);

        // Create first benchmark
        Benchmark::create([
            'dimension_id' => $dimension->id,
            'industry_id' => $industry->id,
            'value' => '75'
        ]);

        // Try to create duplicate benchmark - should fail due to unique constraint
        try {
            Benchmark::create([
                'dimension_id' => $dimension->id,
                'industry_id' => $industry->id,
                'value' => '80'
            ]);
            $this->fail('Expected exception was not thrown');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertContains('Duplicate entry', $e->getMessage());
        }
    }
}
