<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sf5_records', function (Blueprint $table) {
            $table->string('status')->default('pending'); // pending | reviewed
            $table->unsignedBigInteger('reviewed_by')->nullable(); // admin user id
            $table->timestamp('reviewed_at')->nullable();
        });

        Schema::table('honor_rolls', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sf5_records', function (Blueprint $table) {
            $table->dropColumn(['status','reviewed_by','reviewed_at']);
        });
        Schema::table('honor_rolls', function (Blueprint $table) {
            $table->dropColumn(['status','reviewed_by','reviewed_at']);
        });
    }
};
