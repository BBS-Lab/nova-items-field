<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Models\Post;

uses(RefreshDatabase::class);

it('runs the workbench migrations and persists JSON items', function () {
    $post = Post::create([
        'title' => 'Demo',
        'tags' => ['php', 'laravel'],
    ]);

    expect($post->fresh()->tags)->toBe(['php', 'laravel']);
});
