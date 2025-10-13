<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sf5_records', function (Blueprint $table) {
            $table->id();

            // 🏫 Basic Info
            $table->string('region');
            $table->string('division');
            $table->string('school_id', 50);
            $table->string('school_name');
            $table->string('school_year', 50);
            $table->string('school_head_chair');

            // 🧑‍🏫 Relations
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');

            // 👦👧 Student Count Summary
            $table->integer('male_count')->default(0);
            $table->integer('female_count')->default(0);
            $table->integer('overall_count')->default(0);
            $table->decimal('class_average', 5, 2)->default(0);

            // 🏅 Action Taken Summary
            $table->integer('promoted_male')->default(0);
            $table->integer('promoted_female')->default(0);
            $table->integer('conditional_male')->default(0);
            $table->integer('conditional_female')->default(0);
            $table->integer('retained_male')->default(0);
            $table->integer('retained_female')->default(0);

            // 📊 Level of Progress and Achievement
            $table->integer('below_75_male')->default(0);
            $table->integer('below_75_female')->default(0);
            $table->integer('fair_75_79_male')->default(0);
            $table->integer('fair_75_79_female')->default(0);
            $table->integer('satisfactory_80_84_male')->default(0);
            $table->integer('satisfactory_80_84_female')->default(0);
            $table->integer('very_satisfactory_85_89_male')->default(0);
            $table->integer('very_satisfactory_85_89_female')->default(0);
            $table->integer('outstanding_90_100_male')->default(0);
            $table->integer('outstanding_90_100_female')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sf5_records');
    }
};
