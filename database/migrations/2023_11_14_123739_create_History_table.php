<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->string('method');
            $table->string('ref')->nullable();
            $table->string('message');
            $table->string('extra')->nullable();
            $table->index(['ref'], 'ref_index');
            $table->index(['class','method'], 'class_method_index');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('history');
    }
}
