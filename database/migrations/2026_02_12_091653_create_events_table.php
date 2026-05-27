<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('title', 120);
            $table->text('description')->nullable();

            $table->date('event_date');
            $table->time('event_time');

            $table->string('location_text', 180);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            $table->unsignedInteger('max_participants')->default(10);

            $table->enum('status', ['active', 'cancelled'])
                  ->default('active');

            $table->timestamps();

            $table->index(['event_date', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
