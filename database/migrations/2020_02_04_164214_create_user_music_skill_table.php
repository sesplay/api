<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMusicSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_music_skill', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('music_skill_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_music_skill', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('music_skill_id')->references('id')->on('music_skills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_music_skill');
    }
}
