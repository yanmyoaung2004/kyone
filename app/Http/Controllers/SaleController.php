<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

}
