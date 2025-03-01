<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model {
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'total_sales_volume',
        'total_revenue',
        'average_monthly_revenue',
        'type'
    ];
}