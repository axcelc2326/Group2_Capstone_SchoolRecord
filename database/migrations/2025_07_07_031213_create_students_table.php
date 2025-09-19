<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('lrn')->unique(); // ✅ Add LRN field
            $table->string('first_name');
            $table->string('middle_name')->nullable(); // ✅ Added middle name (optional)
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable(); // ✅ Optional gender field
            $table->boolean('approved_by_teacher')->default(false);
            $table->unsignedBigInteger('class_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
