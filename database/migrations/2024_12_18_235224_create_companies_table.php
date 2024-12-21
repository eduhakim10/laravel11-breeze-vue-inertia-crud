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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('name');  // Name (required)
            $table->string('email')->nullable();  // Email (nullable)
            $table->string('logo')->nullable();  // Logo (nullable)
            $table->string('website')->nullable();  // Website (nullable)
            $table->timestamps();  // Created at and updated at timestamps
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
