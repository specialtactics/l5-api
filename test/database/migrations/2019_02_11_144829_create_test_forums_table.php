<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestForumsTable extends Migration
{
    const TABLE_NAME = 'forums';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('forum_id');
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->default(\App\Models\Forum::TYPE_FORUM);

            $table->primary('forum_id');
            $table->foreign('parent_id')->references('forum_id')->on(static::TABLE_NAME)->onDelete('restrict');

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
