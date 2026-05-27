<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('pins', function (Blueprint $table) {

        if (!Schema::hasColumn('pins', 'location_text')) {
            $table->string('location_text', 180)->nullable()->after('content');
        }

        if (!Schema::hasColumn('pins', 'lat')) {
            $table->decimal('lat', 10, 7)->nullable()->after('location_text');
        }

        if (!Schema::hasColumn('pins', 'lng')) {
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
        }
    });
}


public function down(): void
{
    Schema::table('pins', function (Blueprint $table) {
        $table->dropColumn(['location_text', 'lat', 'lng']);
    });
}

};
