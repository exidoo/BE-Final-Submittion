<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowsTable extends Migration
{
    public function up()
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('load_date');
            $table->dateTime('barrow_date');
            $table->uuid('book_id');
            $table->uuid('user_id');
            $table->foreign('book_id')->references('id')->on('books')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('borrows');
    }
}
