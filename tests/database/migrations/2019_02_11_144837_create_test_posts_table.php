<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestPostsTable extends Migration
{

    const TABLE_NAME = 'posts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('post_id');

            $table->uuid('topic_id');
            $table->uuid('author_id');

            $table->text('content');

            $table->primary('post_id');
            $table->foreign('topic_id')->references('topic_id')->on('topics')->onDelete('cascade');
            $table->foreign('author_id')->references('user_id')->on('users')->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();
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
