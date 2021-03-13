<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_data', function (Blueprint $table) {
            //$table->id();
            $table->string('identification',10)->unique();
            $table->primary('identification');
            $table->string('names',100);
            $table->string('lastnames',100);
            $table->string('email',100);
            $table->string('institutional_email',100)->nullable();
            $table->string('phone',9)->nullable();
            $table->string('cellphone',10)->nullable();
            $table->text('address');
            $table->morphs('personal');
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('personal_data');
    }
}
