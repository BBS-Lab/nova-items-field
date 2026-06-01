<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\App\Models\User;
use Workbench\Database\Factories\PostFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The login user may already be provisioned by the Workbench `user:`
     * directive, so create it idempotently to avoid a unique constraint clash.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'nova@laravel.com'],
            ['name' => 'Laravel Nova', 'password' => 'password'],
        );

        PostFactory::new()->times(15)->create();
    }
}
