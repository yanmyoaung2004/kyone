<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalesReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Order count by year
    public function orderCountByYear($year)
    {
        try {
            $orders = Order::whereYear('created_at', $year)->count();
            return response()->json(['count' => $orders]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Order count by week
    public function orderCountByWeek($week)
    {
        try {
            $orders = Order::whereWeek('created_at', $week)->count();
            return response()->json(['count' => $orders]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Order count by day
    public function orderCountByDay($day)
    {
        try {
            $orders = Order::whereDate('created_at', $day)->count();
            return response()->json(['count' => $orders]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function topSellProduct($id, $top)
    {
        try {
            $products = Order::where('product_id', $id)
                ->selectRaw('product_id, SUM(quantity) as total')
                ->groupBy('product_id')
                ->orderBy('total', 'desc')
                ->take($top)
                ->get();

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function topSellingProducts($startDate, $endDate)
    {
        // Validate the dates
        $validatedData = validator(
            ['start_date' => $startDate, 'end_date' => $endDate],
            [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]
        );

        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        // Parse the dates with Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Query for top-selling products
        $topProducts = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(order_product.quantity) as total_quantity_sold')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing'])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity_sold')
            ->get();

        return response()->json([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'top_selling_products' => $topProducts,
        ]);
    }



    public function topSellCategory($id, $top)
    {
        try {
            $categories = Order::with('products')
                ->where('category_id', $id)
                ->selectRaw('product_id, SUM(quantity) as total')
                ->groupBy('product_id')
                ->orderBy('total', 'desc')
                ->take($top)
                ->get();

            return response()->json(['products' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getMonthlyOrders() {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        $ordersData = DB::table('orders')
            ->select(DB::raw('strftime("%Y", created_at) as year'), DB::raw('strftime("%m", created_at) as month'), DB::raw('COUNT(*) as orders'))
            ->groupBy(DB::raw('strftime("%Y", created_at)'), DB::raw('strftime("%m", created_at)'))
            ->get();
        $years = $ordersData->pluck('year')->unique();
        $result = [];
        foreach ($years as $year) {
            $yearData = [];
            foreach ($months as $index => $month) {
                $order = $ordersData->filter(function($item) use ($year, $index) {
                    return $item->year == $year && $item->month == str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                })->first();
                $yearData[] = [
                    'month' => $month,
                    'orders' => $order ? $order->orders : 0
                ];
            }
            $result[$year] = $yearData;
        }

        return response()->json($result);
    }

    public function getWeeklyOrdersMysql(Request $request) {

        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);


        if ($startDate->diffInDays($endDate) != 7) {
            return response()->json(['error' => 'The date range must be exactly 7 days.'], 400);
        }


        $ordersData = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as volume')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get();


        $result = [];
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $order = $ordersData->firstWhere('date', $date->toDateString());
            $result[] = [
                'date' => $date->toDateString(),
                'volume' => $order ? $order->volume : 0, // If no orders, set to 0
            ];
        }

        return response()->json($result);
    }



    public function getDailyOrdersMysql(Request $request) {
        // Validate the request parameters for month and year
        $validatedData = $request->validate([
            'month' => 'required|integer|between:1,12', // month must be between 1 and 12
            'year' => 'required|integer', // year must be a valid integer
        ]);

        $month = $validatedData['month'];
        $year = $validatedData['year'];

        // Get the number of days in the selected month
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Query to get the number of orders for each day in the given month
        $ordersData = DB::table('orders')
            ->select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as orders'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        // Prepare the results for each day of the month (1, 2, 3, ..., last day)
        $result = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $order = $ordersData->first(function ($item) use ($day) {
                return $item->day == $day;
            });

            $result[] = [
                'month' => $day,
                'volume' => $order ? $order->orders : 0, // If no orders, set to 0
            ];
        }

        return response()->json($result);
    }


    public function getMonthlyOrdersMysql() {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $ordersData = DB::table('orders')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as orders'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get();

        $years = $ordersData->pluck('year')->unique();
        $result = [];

        foreach ($years as $year) {
            $yearData = [];
            foreach ($months as $index => $month) {
                $order = $ordersData->first(function ($item) use ($year, $index) {
                    return $item->year == $year && $item->month == ($index + 1);
                });

                $yearData[] = [
                    'month' => $month,
                    'orders' => $order ? $order->orders : 0
                ];
            }
            $result[$year] = $yearData;
        }

        return response()->json($result);
    }

    public function getMonthlyOrdersByYearMysql($year) {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $ordersData = DB::table('orders')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as orders'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get();

        $years = $ordersData->pluck('year')->unique();
        $result = [];

            $yearData = [];
            foreach ($months as $index => $month) {
                $order = $ordersData->first(function ($item) use ($year, $index) {
                    return $item->year == $year && $item->month == ($index + 1);
                });

                $yearData[] = [
                    'month' => $month,
                    'volume' => $order ? $order->orders : 0
                ];
            }

        return response()->json($yearData);
    }


    public function topSellingLocations($i){
        $topCities = DB::table('orders')
            ->join('locations', 'orders.location_id', '=', 'locations.id')
            ->join('cities', 'locations.city_id', '=', 'cities.id')
            ->select('cities.name as location', DB::raw('SUM(orders.total_price) as value'))
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('value')
            ->limit($i)
            ->get();
        return response()->json($topCities);
    }

    public function getReportById($id){
        $report = SalesReport::find($id);
        return response()->json($report);
    }


    public function generateReport(Request $request) {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Fetch sales data
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalSalesVolume = $sales->count();
        $totalRevenue = $sales->sum('total_price');
        $months = max(1, $startDate->diffInMonths($endDate)); // Avoid division by zero
        $averageMonthlyRevenue = $totalRevenue / $months;

        // Store report
        $report = SalesReport::create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_sales_volume' => $totalSalesVolume,
            'total_revenue' => $totalRevenue,
            'type' => $request->type,
            'average_monthly_revenue' => $averageMonthlyRevenue
        ]);

        return response()->json(['message' => 'Sales report generated', 'data' => $report]);
    }

    public function getSalesReports(){
        $reports = SalesReport::orderBy('created_at','desc')->get();
        return response()->json($reports);
    }
}
