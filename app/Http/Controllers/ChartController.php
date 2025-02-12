<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Make sure to import DB

class ChartController extends Controller
{
    //
    public function topSellingProducts($i)
    {
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'categories.name as category', // Get category name
            DB::raw('SUM(order_product.quantity) as orders') // Calculate total sold
        )
        ->join('order_product', 'products.id', '=', 'order_product.product_id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->join('categories', 'products.category_id', '=', 'categories.id') // Join with categories table
        ->whereNotIn('orders.status', ['pending', 'cancelled']) // Exclude pending & cancelled orders
        ->groupBy('products.id', 'products.name', 'products.category_id', 'categories.name') // Include category name in groupBy
        ->orderByDesc('orders') // Order by highest sales
        ->take($i) // Get top 5 products
        ->get();
    
       
        return response()->json($topProducts);
    }
    


    public function getAnnualMonthlyOrders() {
        // Define an array of month names
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
    
        // Query to get monthly order counts grouped by year and month
        $ordersData = DB::table('orders')
            ->select(DB::raw('strftime("%Y", created_at) as year'), DB::raw('strftime("%m", created_at) as month'), DB::raw('COUNT(*) as orders'))
            ->groupBy(DB::raw('strftime("%Y", created_at)'), DB::raw('strftime("%m", created_at)'))
            ->get();
    
        // Query to get yearly total orders
        $yearlyOrders = DB::table('orders')
            ->select(DB::raw('strftime("%Y", created_at) as year'), DB::raw('COUNT(*) as yearly_total'))
            ->groupBy(DB::raw('strftime("%Y", created_at)'))
            ->pluck('yearly_total', 'year');
    
        // Fetch all distinct years from the orders table
        $years = $ordersData->pluck('year')->unique();
    
        // Prepare the final result
        $result = [];
    
        foreach ($years as $year) {
            $yearData = [];
    
            foreach ($months as $index => $month) {
                // Find the matching month data
                $order = $ordersData->filter(function($item) use ($year, $index) {
                    return $item->year == $year && $item->month == str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                })->first();
                
                // If found, use the order count, otherwise set to 0
                $yearData[] = [
                    'month' => $month,
                    'orders' => $order ? $order->orders : 0
                ];
            }
    
            // Add yearly total orders for this year
            $result[$year] = [
                'Yearly Total Orders' => $yearlyOrders[$year] ?? 0,
                'Monthly Orders' => $yearData
            ];
        }
    
        return response()->json($result);
    }
    
public function topSellingLocations($i){

    $topCities = DB::table('orders')
    ->join('locations', 'orders.location_id', '=', 'locations.id')
    ->join('cities', 'locations.city_id', '=', 'cities.id')
    ->select('cities.name as location', DB::raw('COUNT(orders.id) as orders'))
    ->groupBy('cities.id', 'cities.name')
    ->orderByDesc('orders')
    ->limit($i)
    ->get();

    return response()->json($topCities);

}

function getAnnualtMonthlyTotalSales()
{
    $years = range(2020, 2025); // Define the years you want to retrieve data for
    $months = range(1, 12); // Define months (1 to 12)

    // Fetch monthly total sales grouped by year and month
    $salesData = DB::table('orders')
        ->selectRaw('strftime("%Y", created_at) as year, strftime("%m", created_at) as month, SUM(total_price) as total_sale')
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->groupBy('year');

    // Fetch total sales per year
    $yearlySales = DB::table('orders')
        ->selectRaw('strftime("%Y", created_at) as year, SUM(total_price) as yearly_total')
        ->groupBy('year')
        ->orderBy('year')
        ->pluck('yearly_total', 'year');

    $formattedData = [];

    foreach ($years as $year) {
        $monthlySales = [];

        foreach ($months as $month) {
            $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
            $sale = $salesData[$year]->firstWhere('month', $monthFormatted)->total_sale ?? 0;

            $monthlySales[] = [
                'month' => Carbon::createFromFormat('m', $month)->format('F'),
                'Total Sale' => $sale,
            ];
        }

        $formattedData[$year] = [
            'Yearly Total' => $yearlySales[$year] ?? 0,
            'Monthly Sales' => $monthlySales
        ];
    }

    return response()->json($formattedData);
}


public function getTruckStatus(){
    $truckStatusCounts = DB::table('trucks')
    ->select('status', DB::raw('COUNT(*) as trucks'))
    ->groupBy('status')
    ->get();
    
return response()->json($truckStatusCounts);
}

 }


