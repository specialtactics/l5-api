#!/usr/bin/env php
<?php

$sourceDir = '/www/laravel/bp-demo';
$migrationsDir = './database/migrations';

// Copy over database dir
echo `rm -rf ./database/*`;
echo `cp -pr {$sourceDir}/database .`;

// Rename the migrations
$migrationsIterator = new DirectoryIterator($migrationsDir);
foreach ($migrationsIterator as $migration) {
    if (!$migration->isDot()) {
        $filename = $migration->getFilename();

        // Rename file
        $newFileName = str_replace('_create_', '_create_test_', $filename);
        rename($migrationsDir . '/' . $filename, $migrationsDir . '/' . $newFileName);

        // Rename class
        $contents = file_get_contents($migrationsDir . '/' . $newFileName);

        // Get classname
        $newContents = str_replace('class Create', 'class CreateTest', $contents);
        file_put_contents($migrationsDir . '/' . $newFileName, $newContents);
    }
}

// Copy over App
echo `rm -rf ./App`;
echo `cp -pr {$sourceDir}/app .`;
echo `mv ./app ./App`;

// Copy routes
echo `rm -rf ./routes.php`;
echo `cp -pr {$sourceDir}/routes/api.php ./routes.php`;

// Replace all namespaces
echo shell_exec('find ./App \( -type d -name .git -prune \) -o -type f -print0 | xargs -0 sed -i \'s/App\\\\/Specialtactics\\\\L5Api\\\\Tests\\\\App\\\\/g\'');
echo shell_exec('find ./database \( -type d -name .git -prune \) -o -type f -print0 | xargs -0 sed -i \'s/App\\\\/Specialtactics\\\\L5Api\\\\Tests\\\\App\\\\/g\'');
echo shell_exec('find ./routes.php \( -type d -name .git -prune \) -o -type f -print0 | xargs -0 sed -i \'s/App\\\\/Specialtactics\\\\L5Api\\\\Tests\\\\App\\\\/g\'');

// Next steps - try without namespace overriding, if it's only autoloading on testing of this package, maybe not a problem