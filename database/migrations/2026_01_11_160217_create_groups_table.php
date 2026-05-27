<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name', 40);
            $table->text('description')->nullable();

            $table->string('privacy', 10)->default('private');
            $table->string('map_style', 20)->default('classic');
            $table->string('invite_code', 32)->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
