<?php

namespace Specialtactics\L5Api\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

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
    protected $description = 'Easily create the infrastructure for an API resource';

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
        $this->setupStyles();

        $name = ucfirst($this->argument('name'));

        //
        // The basics - Model - Controller - (Policy)
        //

        $this->call('make:model', ['name' => $name]);

        $this->call('make:controller', ['name' => $name.'Controller', '--model' => $name]);

        // Conditionally create policy
        if ($this->anticipate('Would you like to create a policy for this resource?', ['yes', 'no']) == 'yes') {
            $policyName = '../Models/Policies/' . $name . 'Policy';
            $this->call('make:policy', ['name' => $policyName, '-m' => $name]);
        }

        //
        // Database related generation
        //

        // Create a migration
        $migrationName = Str::snake(Str::pluralStudly($name));
        $this->call('make:migration', ['name' => "create_{$migrationName}_table"]);

        // Conditionally create seeder
        if ($this->anticipate('Would you like to create a Seeder for this resource?', ['yes', 'no']) == 'yes') {
            $seederName = Str::plural($name) . 'Seeder';

            $this->call('make:seeder', ['name' => $seederName]);

            $this->line('Please add the following to your DatabaseSeeder.php file', 'important');
            $this->line('$this->call('. $seederName .'::class);', 'code');
            $this->line(PHP_EOL);
        }

        //
        // Spit out example routes
        //

        $this->line('Example routes to put in your routes/api.php', 'important');

        $sectionName = Str::pluralStudly($name);
        $routePrefix = Str::plural(Str::kebab($name));
        $controllerName = $name . 'Controller';

        $exampleRoutes =
            '/*' . PHP_EOL .
            ' * ' . $sectionName . PHP_EOL .
            ' */' . PHP_EOL .
            '$api->group([\'prefix\' => \''. $routePrefix .'\'], function (Router $api) {' . PHP_EOL .
            '    $api->get(\'/\', \'App\Http\Controllers\\'. $controllerName .'@getAll\');' . PHP_EOL .
            '    $api->get(\'/{uuid}\', \'App\Http\Controllers\\'. $controllerName .'@get\');' . PHP_EOL .
            '    $api->post(\'/\', \'App\Http\Controllers\\'. $controllerName .'@post\');' . PHP_EOL .
            '    $api->patch(\'/{uuid}\', \'App\Http\Controllers\\'. $controllerName .'@patch\');' . PHP_EOL .
            '    $api->delete(\'/{uuid}\', \'App\Http\Controllers\\'. $controllerName .'@delete\');' . PHP_EOL .
            '});';

        $this->line($exampleRoutes, 'code');
    }

    /**
     * Setup styles for command
     */
    protected function setupStyles()
    {
        $style = new OutputFormatterStyle('yellow', 'black', ['bold']);
        $this->output->getFormatter()->setStyle('important', $style);

        $style = new OutputFormatterStyle('cyan', 'black', ['bold']);
        $this->output->getFormatter()->setStyle('code', $style);
    }
}
