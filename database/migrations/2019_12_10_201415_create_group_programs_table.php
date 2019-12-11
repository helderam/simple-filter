<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigIncrements('group_id');
            $table->bigIncrements('program_id');
            $table->strings('active',1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_programs');
    }
}
