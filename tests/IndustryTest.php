<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Industry;

class IndustryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test basic industry creation.
     *
     * @return void
     */
    public function testBasicIndustryCreation()
    {
        $industry = Industry::create(['name' => 'Test Industry']);
        
        $this->assertInstanceOf(Industry::class, $industry);
        $this->assertEquals('Test Industry', $industry->name);
    }

    /**
     * Test industry fillable fields.
     *
     * @return void
     */
    public function testIndustryFillableFields()
    {
        $industry = new Industry();
        $fillable = $industry->getFillable();
        
        $this->assertContains('name', $fillable);
    }

    /**
     * Test industry table name.
     *
     * @return void
     */
    public function testIndustryTableName()
    {
        $industry = new Industry();
        $this->assertEquals('industries', $industry->getTable());
    }

    /**
     * Test industry relationships.
     *
     * @return void
     */
    public function testIndustryRelationships()
    {
        $industry = Industry::create(['name' => 'Test Industry']);
        
        // Test users relationship
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $industry->users);
    }
}
