<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestPostTagsTable extends Migration
{
    const TABLE_NAME = 'post_tags';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('tag_id');
            $table->uuid('post_id');

            $table->primary(['tag_id', 'post_id']);

            $table->foreign('tag_id')->references('tag_id')->on('tags')->onDelete('cascade');
            $table->foreign('post_id')->references('post_id')->on('posts')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(static::TABLE_NAME);
    }
}
