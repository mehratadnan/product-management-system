<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportRequestTable extends Migration
{
    public function up()
    {
        Schema::create('import_requests', function (Blueprint $table) {
            $table->id();
            $table->string('merchantID');
            $table->string('type');
            $table->string('ref');
            $table->string('pathType');
            $table->string('path');
            $table->string('status');
            $table->string('message')->nullable();
            $table->index(['id'], 'id_index');
            $table->index(['ref'], 'ref_index');
            $table->index(['status'], 'status_index');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_requests');
    }
}
