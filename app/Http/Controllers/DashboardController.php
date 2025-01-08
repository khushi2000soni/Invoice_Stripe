<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $timeFrame = $request->input('time_frame', 'daily');
        $data = null;
        // Fetch the default data based on the default time frame
        $data = $this->getDataForTimeFrame($timeFrame);
        $todaySaleAmount = Order::whereDate('invoice_date', Carbon::today())->sum('grand_total');
        $last7DaysSaleAmount = Order::whereDate('invoice_date', '>=', Carbon::today()->subDays(7))->sum('grand_total');
        // $last30DaysSaleAmount = Order::whereDate('invoice_date', '>=', Carbon::today()->subDays(30))->sum('grand_total');
        $firstDayOfMonth = Carbon::now()->startOfMonth();
        $currentDate = Carbon::now();
        $currentMonthSaleAmount = Order::whereBetween('invoice_date', [$firstDayOfMonth, $currentDate])->sum('grand_total');
        $allSaleAmount =  Order::sum('grand_total');
        $todayTotalOrder =  Order::whereDate('invoice_date', Carbon::today())->count();
        $totalProductInStock =  Product::count();
        $totalCategory = Category::count();
        $totalCustomer = Customer::count();

        return view('admin.dashboard', compact(
            'data',
            'todaySaleAmount',
            'last7DaysSaleAmount',
            'currentMonthSaleAmount',
            'allSaleAmount',
            'todayTotalOrder',
            'totalProductInStock',
            'totalCategory',
            'totalCustomer',
            'timeFrame',
        ));
    }

    public function fetchReportData(Request $request)
    {
        $timeFrame = $request->input('timeFrame');
        // Fetch the data for the selected time frame
        $data = $this->getDataForTimeFrame($timeFrame);
        return response()->json(['data' => $data,'timeFrame'=>$timeFrame]);
    }

    private function getDataForTimeFrame($timeFrame)
    {
        $data = null;
        switch ($timeFrame) {
            case 'today':
                $data = $this->getTodayData();
                break;
            case '7days':
                $data = $this->getLast7DaysData();
                break;
            case '30days':
                $data = $this->getLast30DaysData();
                break;
            case 'yearly':
                $data = $this->getYearlyData();
                break;
            case 'monthly':
                $data = $this->getMonthlyData();
                break;
            case 'weekly':
                $data = $this->getWeeklyData();
                break;
            case 'daily':
                $data = $this->getDailyData();
                break;
            default:
                $data = $this->getMonthlyData();
                break;
        }

        return $data; // Handle other cases or validation
    }

    private function getYearlyData() {
        $currentYear = now()->year;
        $labels = [];
        $values = [];

        // Fetch data for each year from the current year to 2022
        for ($year = $currentYear; $year >= 2022; $year--) {
            $startDate = now()->startOfYear()->setYear($year);
            $endDate = now()->endOfYear()->setYear($year);

            // $orders = Order::with('orderProduct.product')
            //     ->whereBetween('created_at', [$startDate, $endDate])
            //     ->get();
            $totalGrandTotal = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

            // Format data for the chart
            $labels[] = $year;
            $values[] = $totalGrandTotal;
        }

        return ['labels' => array_reverse($labels), 'values' => array_reverse($values)];
    }

    private function getMonthlyData() {
        $currentDate = now();
        $labels = [];
        $values = [];

        // Fetch data for the last 12 months
        for ($i = 0; $i < 12; $i++) {
            $startDate = $currentDate->copy()->startOfMonth();
            $endDate = $currentDate->copy()->endOfMonth();

            // $orders = Order::with('orderProduct.product')
            //     ->whereBetween('created_at', [$startDate, $endDate])
            //     ->get();
            $totalGrandTotal = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

            // Format data for the chart
            $labels[] = $startDate->format('M Y'); // Display month and year
            $values[] = $totalGrandTotal;
            $currentDate->subMonth(); // Move back to the previous month
        }

        return ['labels' => array_reverse($labels), 'values' => array_reverse($values)];
    }

    // Method to fetch weekly data
    private function getWeeklyData() {

        $currentDate = now();
        $labels = [];
        $values = [];

        // Fetch data for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $startDate = $currentDate->copy()->startOfDay();
            $endDate = $currentDate->copy()->endOfDay();

            $orders = Order::with('orderProduct.product')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Format data for the chart
            $labels[] = $startDate->format('d M Y');
            $values[] = $orders->count();
            $currentDate->subDay(); // Move back to the previous day
        }

        return ['labels' => array_reverse($labels), 'values' => array_reverse($values)];
    }

    // Method to fetch Daily data  of last 30 days
    private function getDailyData() {
        $currentDate = now();
        $labels = [];
        $values = [];

        // Fetch data for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $startDate = $currentDate->copy()->startOfDay();
            $endDate = $currentDate->copy()->endOfDay();

            // $orders = Order::with('orderProduct.product')
            //     ->whereBetween('created_at', [$startDate, $endDate])
            //     ->get();
            $totalGrandTotal = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

            // Format data for the chart
            $labels[] = $startDate->format('d M Y');
            $values[] = $totalGrandTotal;
            $currentDate->subDay(); // Move back to the previous day
        }

        return ['labels' => array_reverse($labels), 'values' => array_reverse($values)];
    }




    private function getTodayData()
    {
        // Fetch the last 7 orders of today
        $today = now()->startOfDay(); // Get the start of the current day

        $data = Order::with('customer.address')
            ->where('created_at', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->get(['invoice_number', 'grand_total', 'customer_id']);

        return $data;
    }

    private function getLast7DaysData()
    {
        // Fetch the last 7 records of the last 7 days
        $last7Days = now()->subDays(7)->startOfDay(); // Calculate the start of the day 7 days ago
        $data = Order::with('customer.address')
            ->where('created_at', '>=', $last7Days)
            ->orderBy('created_at', 'desc')
            ->get(['invoice_number', 'grand_total', 'customer_id']);

        return $data;
    }

    private function getLast30DaysData()
    {
        // Fetch the last 7 records of the last 30 days
        $last30Days = now()->subDays(30)->startOfDay(); // Calculate the start of the day 30 days ago
        $data = Order::with('customer.address')
            ->where('created_at', '>=', $last30Days)
            ->orderBy('created_at', 'desc')
            ->get(['invoice_number', 'grand_total', 'customer_id']);
        return $data;
    }

}
