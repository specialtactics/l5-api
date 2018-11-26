<?php

namespace Specialtactics\L5Api\Console\Commands;

use Illuminate\Console\Command;

class MakeApiResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-resource {name : The name of the API resource (eg. User)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Easily create a controller and model for an API resource';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        //
        // Model - Controller - (Policy)
        //

        $this->call('make:model', ['name' => $name]);

        $this->call('make:controller', ['name' => $name.'Controller', '--model' => $name]);

        // Conditionally create policy
        if ($this->anticipate('Would you like to create a policy for this resource?', ['yes', 'no']) == 'yes') {
            $this->call('make:policy', ['name' => $name.'Policy', '-m' => $name]);
        }

        //
        // Migration - (Seed)
        //

        $migrationName = 'create_' . str_plural(snake_case($name)) . '_table';
        $this->call('make:migration', ['name' => $migrationName]);

        // Conditionally create seeder
        if ($this->anticipate('Would you like to create a Seeder for this resource?', ['yes', 'no']) == 'yes') {
            $this->call('make:seeder', ['name' => str_plural($name) . 'Seeder']);
        }

        // @todo: Add seeder class to DatabaseSeeder.php
    }
}
