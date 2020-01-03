<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeformResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('typeform_responses', function(Blueprint $table) {
            $table->string('id')->primary()->unique();
            $table->string('form_id');
            $table->unsignedBigInteger('submitted_by');
            $table->unsignedInteger('module_instance_id');
            $table->unsignedInteger('activity_instance_id');
            $table->dateTime('submitted_at');
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
        Schema::dropIfExists('typeform_responses');
    }
}
