<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->string('cin')->nullable();
            // $table->string('llpin')->nullable();
            // $table->string('din_pan');
            $table->string('email');
            $table->string('template')->nullable();
            $table->string('status')->default('0');
            $table->string('sended_at')->nullable();
            $table->string('sended_by')->nullable();
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
         Schema::dropIfExists('email_log');
    }
}
