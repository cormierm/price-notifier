<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\Http\Controllers\Watcher\Create;
use Tests\TestCase;

class CreateTest extends TestCase
{

    /** @test */
    public function itCanShowView(): void
    {
        $this->get(route('watcher.create'))
            ->assertSuccessful()
            ->assertViewIs('watcher.create');
    }

}
