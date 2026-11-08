<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->string('type')->default('fruit')->index();
            $table->unsignedSmallInteger('energy')->default(0);
            $table->decimal('protein', 7, 2)->default(0);
            $table->decimal('carbohydrates', 7, 2)->default(0);
            $table->decimal('fat', 7, 2)->default(0);
            $table->decimal('sugars', 7, 2)->default(0);
            $table->decimal('salt', 7, 3)->default(0);
            $table->string('image_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropUnique(['name']);
            $table->dropColumn([
                'name',
                'type',
                'energy',
                'protein',
                'carbohydrates',
                'fat',
                'sugars',
                'salt',
                'image_path',
            ]);
        });
    }
};