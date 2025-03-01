<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_sales_volume');  // Total sales in the period
            $table->decimal('total_revenue', 15, 2); // Total revenue
            $table->decimal('average_monthly_revenue', 15, 2); // Avg revenue
            $table->enum('type',['monthly','weekly','annual','emergency']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales_reports');
    }
};
