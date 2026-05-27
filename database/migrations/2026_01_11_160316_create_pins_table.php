<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title')->nullable();
            $table->text('content')->nullable();

            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            $table->string('location_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};
