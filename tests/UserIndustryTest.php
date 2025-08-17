<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;

class UserIndustryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that a user can be assigned to an industry
     */
    public function testUserCanBeAssignedToIndustry()
    {
        // Create an industry
        $industry = Industry::create([
            'name' => 'Test Industry ' . uniqid()
        ]);

        // Create a user with industry
        $user = User::create([
            'username' => 'testuser_' . uniqid(),
            'name' => 'Test User',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'industry_id' => $industry->id
        ]);

        // Assert the relationship works
        $this->assertEquals($industry->id, $user->industry_id);
        $this->assertEquals($industry->name, $user->industry->name);
    }

    /**
     * Test that a user can have no industry (nullable)
     */
    public function testUserCanHaveNoIndustry()
    {
        // Create a user without industry
        $user = User::create([
            'username' => 'testuser2_' . uniqid(),
            'name' => 'Test User 2',
            'email' => 'test2_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'industry_id' => null
        ]);

        // Assert the user has no industry
        $this->assertNull($user->industry_id);
        $this->assertNull($user->industry);
    }

    /**
     * Test that industry relationship returns correct data
     */
    public function testIndustryRelationshipReturnsCorrectData()
    {
        // Create an industry
        $industry = Industry::create([
            'name' => 'Technology Industry ' . uniqid()
        ]);

        // Create a user with industry
        $user = User::create([
            'username' => 'techuser_' . uniqid(),
            'name' => 'Tech User',
            'email' => 'tech_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'industry_id' => $industry->id
        ]);

        // Refresh the user to load the relationship
        $user->load('industry');

        // Assert the relationship data
        $this->assertInstanceOf('App\Industry', $user->industry);
        $this->assertContains('Technology Industry', $user->industry->name);
        $this->assertEquals($industry->id, $user->industry->id);
    }

    /**
     * Test that user can be updated with different industry
     */
    public function testUserCanBeUpdatedWithDifferentIndustry()
    {
        // Create industries
        $industry1 = Industry::create(['name' => 'Industry 1 ' . uniqid()]);
        $industry2 = Industry::create(['name' => 'Industry 2 ' . uniqid()]);

        // Create a user with first industry
        $user = User::create([
            'username' => 'updateuser_' . uniqid(),
            'name' => 'Update User',
            'email' => 'update_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'industry_id' => $industry1->id
        ]);

        // Update user with second industry
        $user->update(['industry_id' => $industry2->id]);

        // Reload the user from database
        $user = User::find($user->id);

        // Assert the industry was updated
        $this->assertEquals($industry2->id, $user->industry_id);
        $this->assertContains('Industry 2', $user->industry->name);
    }
}
