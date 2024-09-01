<?php

namespace App\Providers;

use App\Repository\Project\Interface\ProjectInterface;
use App\Repository\Project\ProjectRepository;
use App\Repository\Role\Interface\RoleInterface;
use App\Repository\Role\RoleRepository;
use App\Repository\Task\Interface\TaskInterface;
use App\Repository\Task\TaskRepository;
use App\Repository\User\Interface\UserInterface;
use App\Repository\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectInterface::class, ProjectRepository::class);
        $this->app->bind(TaskInterface::class, TaskRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
