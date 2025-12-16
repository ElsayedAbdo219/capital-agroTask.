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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('tax', 5, 2)->default(0);
            $table->json('additional_data')->nullable();
            $table->string('feed_type'); // starter, grower, finisher
            $table->string('animal_type'); // poultry, cattle, fish
            $table->decimal('weight_per_unit', 6, 2); // kg
            $table->boolean('is_returnable')->default(true); // هل هناك مرتجع ام لا 
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
