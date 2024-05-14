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
        Schema::create('guest_houses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable()->unique();
            $table->longText('description');
            $table->integer('price');
            $table->string('address');
            $table->string('map_link')->nullable();
            $table->integer('bedrooms_nbr');
            $table->integer('beds_nbr');
            $table->integer('toilets_nbr');
            $table->integer('bathrooms_nbr');
            $table->boolean('has_kitchen')->default(false);
            $table->boolean('has_pool')->default(false);
            $table->boolean('has_air_conditionner')->default(false);
            $table->boolean('has_jacuzzi')->default(false);
            $table->boolean('has_washing_machine')->default(false);
            $table->boolean('has_car')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_houses');
    }
};
