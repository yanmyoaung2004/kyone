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
        Schema::disableForeignKeyConstraints();

        Schema::create('escalated_issues', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->foreignId('order_id');
            $table->foreignId('driver_id');
            $table->enum('status', ["pending","inprogress","resolved"]);
            $table->enum('priority', ["high","low","medium"]);
            $table->enum('status', ["pending","inprogress","resolved"]);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalated_issues');
    }
};
