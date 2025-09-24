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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->enum('type', ['text', 'multiple', 'one_choice', 'boolean'])->default('text');
            $table->integer('points')->default(1);
            $table->integer('order_index')->default(1);


            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->index(['exam_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
