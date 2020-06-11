<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SendEmailByAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('sended_emails_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('state')->nullable();
            $table->string('roc')->nullable();
            $table->string('doi');
            $table->string('activity_description');
            $table->string('category');
            $table->string('email');
            $table->string('template')->nullable();
            $table->string('status');
            $table->string('sended_by')->nullable();
            $table->string('sended_at')->nullable();
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
        Schema::dropIfExists('sended_emails_log');
    }
}
