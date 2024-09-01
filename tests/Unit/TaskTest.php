<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_task()
    {
        $project = Project::factory()->create();

        $task = Task::create([
            'project_id' => $project->id,
            'name' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('tasks', ['name' => 'Test Task']);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $project = Project::factory()->create();

        $task = Task::create([
            'project_id' => $project->id,
            'name' => 'Old Task',
            'description' => 'Old description',
            'status' => 1,
        ]);

        $task->update(['name' => 'Updated Task']);

        $this->assertDatabaseHas('tasks', ['name' => 'Updated Task']);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $project = Project::factory()->create();

        $task = Task::create([
            'project_id' => $project->id,
            'name' => 'Task to be deleted',
            'description' => 'This task will be deleted',
            'status' => 1,
        ]);

        $task->delete();

        $this->assertDatabaseMissing('tasks', ['name' => 'Task to be deleted']);
    }
}
