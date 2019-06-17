<?php

use Specialtactics\L5Api\Tests\App\Models\Forum;
use Specialtactics\L5Api\Tests\App\Models\Topic;
use Specialtactics\L5Api\Tests\App\Models\Post;

class PostsSeeder extends BaseSeeder
{
    /**
     * Run fake seeds - for non production environments
     *
     * @return mixed
     */
    public function runFake() {
        $topics = Topic::all();

        foreach ($topics as $topic) {
            for ($i = 0; $i < 10; ++$i) {
                Post::create([
                    'topic_id' => $topic->getKey(),
                    'topic' => implode(' ', $this->faker->words(3)),
                    'content' => $this->faker->paragraph(3),
                ]);
            }
        }
    }

    /**
     * Run seeds to be ran only on production environments
     *
     * @return mixed
     */
    public function runProduction() {

    }

    /**
     * Run seeds to be ran on every environment (including production)
     *
     * @return mixed
     */
    public function runAlways() {

    }
}
