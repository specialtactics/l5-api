<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(UserStorySeeder::class);

        $this->call(ForumsSeeder::class);
        $this->call(TopicsSeeder::class);
        $this->call(PostsSeeder::class);
    }
}
