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
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('ddi');
            $table->string('ddd');
            $table->string('number');
            // mysql
            $table->string('full_number')->storedAs("CONCAT('+','',ddi,' ',ddd,' ',number)");
            // sqlite
            // $table->string('full_number')->virtualAs("'+' || ddi || '' || ddd || '' || number")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_numbers');
    }
};
