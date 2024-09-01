<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_project()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'This is a test project',
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

    /** @test */
    public function it_can_update_a_project()
    {
        $project = Project::create([
            'name' => 'Old Name',
            'description' => 'Old description',
        ]);

        $project->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('projects', ['name' => 'Updated Name']);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $project = Project::create([
            'name' => 'To be deleted',
            'description' => 'This project will be deleted',
        ]);

        $project->delete();

        $this->assertDatabaseMissing('projects', ['name' => 'To be deleted']);
    }
}
