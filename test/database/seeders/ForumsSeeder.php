<?php

namespace Database\Seeders;

use App\Models\Forum;

class ForumsSeeder extends BaseSeeder
{
    /**
     * Run fake Seeders - for non production environments
     *
     * @return mixed
     */
    public function runFake() {
        Forum::create([
            'name' => 'Test Forum',
        ]);
    }

    /**
     * Run Seeders to be ran only on production environments
     *
     * @return mixed
     */
    public function runProduction() {

    }

    /**
     * Run Seeders to be ran on every environment (including production)
     *
     * @return mixed
     */
    public function runAlways() {

    }
}
