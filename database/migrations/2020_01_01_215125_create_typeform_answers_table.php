<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeformAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('typeform_answers', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('field_id');
            $table->string('response_id');
            $table->string('type');
            $table->text('answer')->nullable();
            $table->boolean('encoded')->default(false);
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
        Schema::dropIfExists('typeform_answers');
    }
}
