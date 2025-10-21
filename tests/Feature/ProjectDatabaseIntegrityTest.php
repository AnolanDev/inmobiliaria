<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Property;
use App\Models\Visit;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectDatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user with permissions
        $this->user = User::factory()->create([
            'is_active' => true,
            'email' => 'admin@test.com'
        ]);
        
        // Create super-admin role and assign to user
        $superAdminRole = \App\Models\Role::firstOrCreate([
            'slug' => 'super-admin'
        ], [
            'name' => 'Super Administrator',
            'description' => 'Full system access'
        ]);
        
        $this->user->roles()->attach($superAdminRole);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_has_correct_database_schema(): void
    {
        $columns = DB::select("DESCRIBE projects");
        $columnNames = collect($columns)->pluck('Field')->toArray();
        
        $expectedColumns = [
            'id', 'name', 'description', 'type', 'status', 'property_count',
            'is_public', 'sort_order', 'city', 'state', 'cover_image', 
            'gallery', 'videos', 'created_at', 'updated_at', 'deleted_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $columnNames, "Column '{$column}' should exist in projects table");
        }
    }

    /** @test */
    public function it_has_correct_enum_constraints_for_type(): void
    {
        $project = Project::factory()->make(['type' => 'Campestres']);
        $project->save();
        $this->assertTrue(true); // Should not throw exception
        
        $project = Project::factory()->make(['type' => 'Urbanos']);
        $project->save();
        $this->assertTrue(true);
        
        $project = Project::factory()->make(['type' => 'Turísticos']);
        $project->save();
        $this->assertTrue(true);
        
        // Test invalid type
        $this->expectException(\Illuminate\Database\QueryException::class);
        $project = Project::factory()->make(['type' => 'InvalidType']);
        $project->save();
    }

    /** @test */
    public function it_has_correct_enum_constraints_for_status(): void
    {
        $project = Project::factory()->make(['status' => 'Disponible']);
        $project->save();
        $this->assertTrue(true);
        
        $project = Project::factory()->make(['status' => 'Reservado']);
        $project->save();
        $this->assertTrue(true);
        
        $project = Project::factory()->make(['status' => 'Vendido']);
        $project->save();
        $this->assertTrue(true);
        
        // Test invalid status
        $this->expectException(\Illuminate\Database\QueryException::class);
        $project = Project::factory()->make(['status' => 'InvalidStatus']);
        $project->save();
    }

    /** @test */
    public function it_properly_handles_json_fields(): void
    {
        $project = Project::factory()->create([
            'gallery' => ['image1.jpg', 'image2.jpg'],
            'videos' => ['video1.mp4', 'video2.mp4']
        ]);
        
        $this->assertIsArray($project->fresh()->gallery);
        $this->assertIsArray($project->fresh()->videos);
        $this->assertEquals(['image1.jpg', 'image2.jpg'], $project->fresh()->gallery);
        $this->assertEquals(['video1.mp4', 'video2.mp4'], $project->fresh()->videos);
    }

    /** @test */
    public function it_properly_handles_optimized_image_json_structure(): void
    {
        $optimizedImageStructure = [
            'thumbnail' => 'projects/1/cover_123_abc_thumbnail.jpg',
            'medium' => 'projects/1/cover_123_abc_medium.jpg',
            'large' => 'projects/1/cover_123_abc_large.jpg',
            'original' => 'projects/1/cover_123_abc_original.jpg'
        ];
        
        $project = Project::factory()->create([
            'cover_image' => $optimizedImageStructure
        ]);
        
        $this->assertIsArray($project->fresh()->cover_image);
        $this->assertArrayHasKey('thumbnail', $project->fresh()->cover_image);
        $this->assertArrayHasKey('medium', $project->fresh()->cover_image);
        $this->assertArrayHasKey('large', $project->fresh()->cover_image);
        $this->assertArrayHasKey('original', $project->fresh()->cover_image);
    }

    /** @test */
    public function it_correctly_handles_soft_deletes(): void
    {
        $project = Project::factory()->create();
        $projectId = $project->id;
        
        // Verify project exists
        $this->assertDatabaseHas('projects', ['id' => $projectId, 'deleted_at' => null]);
        
        // Soft delete
        $project->delete();
        
        // Verify soft delete
        $this->assertSoftDeleted('projects', ['id' => $projectId]);
        
        // Verify project not in normal queries
        $this->assertNull(Project::find($projectId));
        
        // Verify project exists with trashed
        $this->assertNotNull(Project::withTrashed()->find($projectId));
    }

    /** @test */
    public function it_maintains_referential_integrity_with_properties(): void
    {
        $project = Project::factory()->create();
        $agent = Agent::factory()->create();
        
        // Create property linked to project
        $property = Property::factory()->create([
            'project_id' => $project->id,
            'agent_id' => $agent->id
        ]);
        
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'project_id' => $project->id
        ]);
        
        // Test relationship
        $this->assertEquals($project->id, $property->fresh()->project_id);
        $this->assertTrue($project->properties->contains($property));
    }

    /** @test */
    public function it_handles_project_deletion_with_properties_set_null(): void
    {
        $project = Project::factory()->create();
        $agent = Agent::factory()->create();
        
        $property = Property::factory()->create([
            'project_id' => $project->id,
            'agent_id' => $agent->id
        ]);
        
        // Delete project (soft delete)
        $project->delete();
        
        // Property should still exist but project_id should be set to null
        $property->refresh();
        $this->assertNull($property->project_id);
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'project_id' => null
        ]);
    }

    /** @test */
    public function it_supports_visits_to_projects(): void
    {
        $project = Project::factory()->create();
        $agent = Agent::factory()->create();
        
        // Create visit linked to project (not property)
        $visit = Visit::factory()->create([
            'project_id' => $project->id,
            'property_id' => null,
            'agent_id' => $agent->id
        ]);
        
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'project_id' => $project->id,
            'property_id' => null
        ]);
    }

    /** @test */
    public function it_enforces_visit_constraint_project_or_property_required(): void
    {
        $agent = Agent::factory()->create();
        
        // This should fail - neither project_id nor property_id set
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Visit::factory()->create([
            'project_id' => null,
            'property_id' => null,
            'agent_id' => $agent->id
        ]);
    }

    /** @test */
    public function it_properly_handles_boolean_fields(): void
    {
        $project = Project::factory()->create(['is_public' => true]);
        $this->assertTrue($project->fresh()->is_public);
        
        $project = Project::factory()->create(['is_public' => false]);
        $this->assertFalse($project->fresh()->is_public);
        
        $project = Project::factory()->create(['is_public' => null]);
        $this->assertFalse($project->fresh()->is_public); // Should default to false
    }

    /** @test */
    public function it_properly_handles_integer_fields(): void
    {
        $project = Project::factory()->create([
            'property_count' => 10,
            'sort_order' => 5
        ]);
        
        $this->assertIsInt($project->fresh()->property_count);
        $this->assertIsInt($project->fresh()->sort_order);
        $this->assertEquals(10, $project->fresh()->property_count);
        $this->assertEquals(5, $project->fresh()->sort_order);
    }

    /** @test */
    public function it_allows_null_values_for_nullable_fields(): void
    {
        $project = Project::factory()->create([
            'description' => null,
            'property_count' => null,
            'sort_order' => null,
            'city' => null,
            'state' => null,
            'cover_image' => null,
            'gallery' => null,
            'videos' => null
        ]);
        
        $fresh = $project->fresh();
        $this->assertNull($fresh->description);
        $this->assertNull($fresh->property_count);
        $this->assertNull($fresh->sort_order);
        $this->assertNull($fresh->city);
        $this->assertNull($fresh->state);
        $this->assertNull($fresh->cover_image);
        $this->assertNull($fresh->gallery);
        $this->assertNull($fresh->videos);
    }

    /** @test */
    public function it_has_correct_timestamps(): void
    {
        $project = Project::factory()->create();
        
        $this->assertNotNull($project->created_at);
        $this->assertNotNull($project->updated_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->updated_at);
    }

    /** @test */
    public function it_updates_timestamps_on_modification(): void
    {
        $project = Project::factory()->create();
        $originalUpdatedAt = $project->updated_at;
        
        // Wait a moment to ensure timestamp difference
        sleep(1);
        
        $project->update(['name' => 'Updated Name']);
        
        $this->assertNotEquals($originalUpdatedAt, $project->fresh()->updated_at);
        $this->assertTrue($project->fresh()->updated_at->greaterThan($originalUpdatedAt));
    }

    /** @test */
    public function it_handles_unique_constraint_on_name(): void
    {
        Project::factory()->create(['name' => 'Unique Project Name']);
        
        // Try to create another project with same name
        $this->expectException(\Illuminate\Database\QueryException::class);
        Project::factory()->create(['name' => 'Unique Project Name']);
    }

    /** @test */
    public function it_properly_handles_long_text_fields(): void
    {
        $longDescription = str_repeat('This is a very long description. ', 100);
        $project = Project::factory()->create(['description' => $longDescription]);
        
        $this->assertEquals($longDescription, $project->fresh()->description);
    }

    /** @test */
    public function it_properly_handles_cover_image_as_longtext(): void
    {
        // Test with very large JSON structure (simulating optimized images with metadata)
        $largeImageData = [
            'thumbnail' => str_repeat('a', 1000),
            'medium' => str_repeat('b', 1000), 
            'large' => str_repeat('c', 1000),
            'original' => str_repeat('d', 1000),
            'metadata' => [
                'sizes' => array_fill(0, 100, ['width' => 800, 'height' => 600]),
                'optimization_data' => str_repeat('metadata', 500)
            ]
        ];
        
        $project = Project::factory()->create(['cover_image' => $largeImageData]);
        
        $this->assertIsArray($project->fresh()->cover_image);
        $this->assertArrayHasKey('metadata', $project->fresh()->cover_image);
    }

    /** @test */
    public function it_maintains_data_consistency_across_transactions(): void
    {
        DB::beginTransaction();
        
        try {
            $project = Project::factory()->create(['name' => 'Transaction Test']);
            $agent = Agent::factory()->create();
            $property = Property::factory()->create([
                'project_id' => $project->id,
                'agent_id' => $agent->id
            ]);
            
            DB::commit();
            
            // Verify both records exist
            $this->assertDatabaseHas('projects', ['name' => 'Transaction Test']);
            $this->assertDatabaseHas('properties', ['project_id' => $project->id]);
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}