<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Create and authenticate a user with permissions
        $this->user = User::factory()->create([
            'is_active' => true,
            'email' => 'admin@test.com'
        ]);
        
        // Create super-admin role if it doesn't exist and assign to user
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
    public function it_can_list_projects(): void
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) use ($projects) {
            $page->component('Projects/Index')
                ->has('projects.data', 3)
                ->where('projects.data.0.name', $projects->first()->name);
        });
    }

    /** @test */
    public function it_can_filter_projects_by_search(): void
    {
        $project1 = Project::factory()->create(['name' => 'Villa del Mar']);
        $project2 = Project::factory()->create(['name' => 'Casa de Campo']);
        Project::factory()->create(['name' => 'Apartamento Urbano']);

        $response = $this->get(route('projects.index', ['search' => 'Villa']));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data', 1)
                ->where('projects.data.0.name', 'Villa del Mar');
        });
    }

    /** @test */
    public function it_can_filter_projects_by_type(): void
    {
        Project::factory()->create(['type' => 'Campestres']);
        Project::factory()->create(['type' => 'Urbanos']);
        Project::factory()->count(2)->create(['type' => 'Turísticos']);

        $response = $this->get(route('projects.index', ['type' => 'Turísticos']));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data', 2);
        });
    }

    /** @test */
    public function it_can_filter_projects_by_status(): void
    {
        Project::factory()->create(['status' => 'Disponible']);
        Project::factory()->count(2)->create(['status' => 'Reservado']);

        $response = $this->get(route('projects.index', ['status' => 'Reservado']));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data', 2);
        });
    }

    /** @test */
    public function it_can_show_create_form(): void
    {
        $response = $this->get(route('projects.create'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->component('Projects/Create')
                ->has('types')
                ->has('statuses');
        });
    }

    /** @test */
    public function it_can_create_a_project(): void
    {
        $coverImage = UploadedFile::fake()->image('cover.jpg');
        $galleryImage = UploadedFile::fake()->image('gallery.jpg');
        $video = UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4');

        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test project description',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'property_count' => 10,
            'is_public' => true,
            'sort_order' => 1,
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => $coverImage,
            'gallery' => [$galleryImage],
            'videos' => [$video],
        ];

        $response = $this->post(route('projects.store'), $projectData);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
        ]);

        $project = Project::where('name', 'Test Project')->first();
        $this->assertNotNull($project);
        $this->assertTrue($project->is_public);
        $this->assertEquals(1, $project->sort_order);
        $this->assertEquals(10, $project->property_count);
        $this->assertNotNull($project->cover_image);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_project(): void
    {
        $response = $this->post(route('projects.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'type',
            'status',
            'city',
            'state',
            'cover_image',
        ]);
    }

    /** @test */
    public function it_validates_unique_name_when_creating_project(): void
    {
        $existingProject = Project::factory()->create(['name' => 'Existing Project']);

        $response = $this->post(route('projects.store'), [
            'name' => 'Existing Project',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_validates_project_type(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'type' => 'Invalid Type',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    /** @test */
    public function it_validates_project_status(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'type' => 'Campestres',
            'status' => 'Invalid Status',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function it_validates_cover_image_file_type(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['cover_image']);
    }

    /** @test */
    public function it_can_show_a_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertInertia(function ($page) use ($project) {
            $page->component('Projects/Show')
                ->where('project.id', $project->id)
                ->where('project.name', $project->name)
                ->has('project.properties_count');
        });
    }

    /** @test */
    public function it_can_show_edit_form(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.edit', $project));

        $response->assertOk();
        $response->assertInertia(function ($page) use ($project) {
            $page->component('Projects/Edit')
                ->where('project.id', $project->id)
                ->has('types')
                ->has('statuses');
        });
    }

    /** @test */
    public function it_can_update_a_project(): void
    {
        $project = Project::factory()->create([
            'name' => 'Original Name',
            'type' => 'Campestres',
            'status' => 'Disponible',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'type' => 'Urbanos',
            'status' => 'Reservado',
            'property_count' => 15,
            'is_public' => false,
            'sort_order' => 2,
            'city' => 'Medellín',
            'state' => 'Antioquia',
        ];

        $response = $this->put(route('projects.update', $project), $updateData);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success');

        $project->refresh();
        $this->assertEquals('Updated Name', $project->name);
        $this->assertEquals('Updated description', $project->description);
        $this->assertEquals('Urbanos', $project->type);
        $this->assertEquals('Reservado', $project->status);
        $this->assertEquals(15, $project->property_count);
        $this->assertFalse($project->is_public);
        $this->assertEquals(2, $project->sort_order);
        $this->assertEquals('Medellín', $project->city);
        $this->assertEquals('Antioquia', $project->state);
    }

    /** @test */
    public function it_can_update_project_with_new_cover_image(): void
    {
        $project = Project::factory()->create();
        $newCoverImage = UploadedFile::fake()->image('new_cover.jpg');

        $updateData = [
            'name' => $project->name,
            'type' => $project->type,
            'status' => $project->status,
            'city' => $project->city,
            'state' => $project->state,
            'cover_image' => $newCoverImage,
        ];

        $response = $this->put(route('projects.update', $project), $updateData);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_validates_unique_name_when_updating_project_except_itself(): void
    {
        $project1 = Project::factory()->create(['name' => 'Project One']);
        $project2 = Project::factory()->create(['name' => 'Project Two']);

        // Try to update project2 with project1's name - should fail
        $response = $this->put(route('projects.update', $project2), [
            'name' => 'Project One',
            'type' => $project2->type,
            'status' => $project2->status,
            'city' => $project2->city,
            'state' => $project2->state,
        ]);

        $response->assertSessionHasErrors(['name']);

        // Update project1 with its own name - should succeed
        $response = $this->put(route('projects.update', $project1), [
            'name' => 'Project One',
            'description' => 'Updated description',
            'type' => $project1->type,
            'status' => $project1->status,
            'city' => $project1->city,
            'state' => $project1->state,
        ]);

        $response->assertRedirect(route('projects.show', $project1));
    }

    /** @test */
    public function it_can_delete_a_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    /** @test */
    public function it_returns_404_when_trying_to_access_non_existent_project(): void
    {
        $response = $this->get(route('projects.show', 999));
        $response->assertNotFound();

        $response = $this->get(route('projects.edit', 999));
        $response->assertNotFound();

        $response = $this->put(route('projects.update', 999), []);
        $response->assertNotFound();

        $response = $this->delete(route('projects.destroy', 999));
        $response->assertNotFound();
    }

    /** @test */
    public function it_orders_projects_by_sort_order_and_creation_date(): void
    {
        $project1 = Project::factory()->create(['sort_order' => 3, 'name' => 'Third']);
        $project2 = Project::factory()->create(['sort_order' => 1, 'name' => 'First']);
        $project3 = Project::factory()->create(['sort_order' => 2, 'name' => 'Second']);

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->where('projects.data.0.name', 'First')
                ->where('projects.data.1.name', 'Second')
                ->where('projects.data.2.name', 'Third');
        });
    }

    /** @test */
    public function it_includes_properties_count_in_project_listing(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data.0.properties_count');
        });
    }

    /** @test */
    public function it_can_filter_projects_by_location(): void
    {
        Project::factory()->create(['city' => 'Bogotá', 'state' => 'Cundinamarca']);
        Project::factory()->create(['city' => 'Medellín', 'state' => 'Antioquia']);
        Project::factory()->create(['city' => 'Cali', 'state' => 'Valle del Cauca']);

        $response = $this->get(route('projects.index', ['location' => 'Bogotá']));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data', 1)
                ->where('projects.data.0.city', 'Bogotá');
        });
    }

    /** @test */
    public function it_can_filter_projects_by_state(): void
    {
        Project::factory()->create(['state' => 'Cundinamarca']);
        Project::factory()->count(2)->create(['state' => 'Antioquia']);

        $response = $this->get(route('projects.index', ['state' => 'Antioquia']));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->has('projects.data', 2);
        });
    }

    /** @test */
    public function it_allows_null_property_count(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'Test description',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'property_count' => null,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'property_count' => null,
        ]);
    }

    /** @test */
    public function it_allows_null_sort_order(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'Test description',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'sort_order' => null,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'sort_order' => null,
        ]);
    }

    /** @test */
    public function it_allows_optional_gallery(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'Test description',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'gallery' => null,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
        ]);
    }

    /** @test */
    public function it_allows_optional_videos(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'Test description',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'videos' => null,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
        ]);
    }
}