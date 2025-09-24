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
        Schema::create('exam_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('auto_score')->nullable()->comment('Score automatique des QCM');
            $table->enum('status', ['assigned', 'started', 'submitted', 'pending_review', 'graded'])->default('assigned');
            $table->text('teacher_notes')->nullable();
            $table->json('security_violations')->nullable()->comment('Violations de sécurité détectées pendant l\'examen');
            $table->boolean('forced_submission')->default(false)->comment('Soumission forcée par timeout ou détection de triche');
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);

            $table->index(['exam_id', 'student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_assignments');
    }
};
