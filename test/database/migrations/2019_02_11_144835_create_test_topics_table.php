<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTopicsTable extends Migration
{

    const TABLE_NAME = 'topics';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('topic_id');
            $table->uuid('forum_id');
            $table->uuid('author_id');

            $table->string('title');

            $table->primary('topic_id');

            $table->foreign('forum_id')->references('forum_id')->on('forums')->onDelete('cascade');
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
