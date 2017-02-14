<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');

            $table->enum('gender', ['male', 'female']); // пол

            $table->date('birthday'); // дата рождения

            $table->float('height'); //рос
            $table->float('weight'); //вес

            $table->enum('eyes', ['---', 'amber', 'blue', 'brown', 'gray', 'green', 'hazel']); //цвет глаз

            $table->enum('hair', [
                '---',
                'black',
                'blonde',
                'brown',
                'dark_brown',
                'chestnut_brown',
                'light_brown',
                'ginger',
                'red',
                'dark_blonde',
                'fair',
                'grey',
                'albinos',]); // цвет волос

            $table->enum('education', [
                '---',
                'none',
                'elementary',
                'school',
                'high_school',
                'college',
                'university',
                'unfinished_higher',
                'phd_degree',
                'student',]); //образование

            $table->enum('kids', ['---', 'no', '1', '2', '3', '4', 'tell_you_later']); // дети
            $table->enum('want_kids', ['---', 'yes', 'no', 'tell_you_later']); //завести детей

            $table->enum('family', ['---', 'divorced', 'married', 'not_married', 'widowed']); // семейное положение
            $table->enum('religion', ['---','atheism', 'christianity', 'gnosticism', 'islam', 'judaism', 'catholicism', 'buddhism', 'hinduism', 'shinto', 'taoism']); //религия

            $table->enum('smoke', ['---', 'yes', 'no', 'times']); //курение
            $table->enum('drink', ['---', 'yes', 'no', 'times']); //выпивка

            $table->string('occupation');

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
        Schema::drop('profile');
    }
}
