<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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

}
