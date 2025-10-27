<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honor_rolls', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');

            // 📅 Academic info
            $table->string('school_year', 20);
            $table->string('quarter', 20); // e.g. "1st Quarter", "2nd Quarter"

            // 🧑‍🏫 Inputted Principal Name
            $table->string('principal_name');

            // 📊 Summary counts
            $table->integer('with_honors_count')->default(0);
            $table->integer('with_high_honors_count')->default(0);
            $table->integer('with_highest_honors_count')->default(0);
            $table->integer('achievers_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honor_rolls');
    }
};
