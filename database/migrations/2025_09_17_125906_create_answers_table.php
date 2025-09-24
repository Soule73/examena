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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('exam_assignments')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('choice_id')->nullable()->constrained('choices')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->float('score')->nullable()->default(null)->comment('Score obtenu pour cette réponse, si applicable');

            $table->foreignId('choice_id')->nullable()->after('question_id')->constrained('choices')->onDelete('cascade');
            $table->foreignId('assignment_id')->after('id')->constrained('exam_assignments')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
