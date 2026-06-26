<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->text('question_text');
            $table->text('help_text')->nullable();
            $table->unsignedSmallInteger('weight')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_complementary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
