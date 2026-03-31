<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('direction_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['internal', 'external']);
            $table->decimal('price', 6)->nullable();
            $table->integer('duration')->nullable();
            $table->string('external_link')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
