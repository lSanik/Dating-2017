<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCountryRegionCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('cities');
        Schema::drop('states');
        Schema::drop('countries');

        Schema::create('countries', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',256);
            $table->string('name_en',256);
            $table->string('sortname');
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table){
            $table->increments('id');
            $table->integer('country_id');
            /*$table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');*/
            $table->string('name');
            $table->string('name_en');
            $table->string('short_name');
            $table->timestamps();

        });

        Schema::create('cities', function (Blueprint $table){
            $table->increments('id');
            $table->integer('state_id');
            /*$table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');*/
            $table->string('name');
            $table->string('name_en');
            $table->string('hasMetro');
            $table->timestamps();
        });

        $script = getcwd().'/database/all_regions.sql';
        //echo $script;
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');

        $command = "mysql -u $username -h $host -p$password $database < $script";

        exec($command);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::drop('cities');
        Schema::drop('states');
        Schema::drop('countries');
    }
}
