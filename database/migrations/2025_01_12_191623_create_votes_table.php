<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_member_id')->constrained()->onDelete('cascade'); // Kto głosował
            $table->foreignId('name_id')->constrained()->onDelete('cascade'); // Na co głosował
            $table->boolean('vote'); // 1 = Tak, 0 = Nie
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
