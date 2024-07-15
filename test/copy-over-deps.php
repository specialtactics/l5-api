#!/usr/bin/env php
<?php

$sourceDir = '/www/laravel/bp-demo';
$migrationsDir = './database/migrations';
$configsToCopy = ['api.php', 'auth.php', 'jwt.php'];

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
        rename($migrationsDir.'/'.$filename, $migrationsDir.'/'.$newFileName);

        // Rename class
        $contents = file_get_contents($migrationsDir.'/'.$newFileName);

        // Get classname
        $newContents = str_replace('class Create', 'class CreateTest', $contents);
        file_put_contents($migrationsDir.'/'.$newFileName, $newContents);
    }
}

// Copy over App
echo `rm -rf ./app`;
echo `cp -pr {$sourceDir}/app .`;

// Copy routes
echo `rm -f ./routes/*`;
echo `cp -pr {$sourceDir}/routes/api.php ./routes/api-routes.php`;

// Copy configs
echo `rm -f ./config/*`;
foreach ($configsToCopy as $configFile) {
    echo `cp -pr {$sourceDir}/config/$configFile ./config/`;
}
