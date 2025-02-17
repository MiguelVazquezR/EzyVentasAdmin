<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SupportReport;

class DashboardController extends Controller
{
    public function fetchDashboardData() 
    {
        $storeCount = Store::where('is_active', true)->count();
        $totalRevenue = Sale::sum(DB::raw('current_price * quantity'));
        $totalReports = SupportReport::count();

        // Ventas por mes
        $salesData = Sale::selectRaw('MONTH(created_at) as month, SUM(current_price * quantity) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total');
        $salesMonths = Sale::selectRaw('MONTHNAME(created_at) as month')
            ->groupBy('month')
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->pluck('month');

        // Reportes por estado
        $reportsByStatus = SupportReport::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Tipos de tienda
        $storeTypes = Store::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();
        $storeTypeCounts = $storeTypes->pluck('count');
        $storeTypeLabels = $storeTypes->pluck('type');

        //venta por tienda
        $topStores = DB::table('sales')
            ->select('stores.name', DB::raw('SUM(sales.current_price * sales.quantity) as total_sales'))
            ->join('stores', 'sales.store_id', '=', 'stores.id')
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        return response()->json([
            'storeCount' => $storeCount,
            'totalRevenue' => $totalRevenue,
            'totalReports' => $totalReports,
            'salesData' => $salesData,
            'salesMonths' => $salesMonths,
            'reportsByStatus' => array_values($reportsByStatus->toArray()),
            'storeTypeCounts' => $storeTypeCounts,
            'storeTypeLabels' => $storeTypeLabels,
            'topStoresSales' => $topStores->pluck('total_sales'),
            'topStoresNames' => $topStores->pluck('name'),
        ]);
    }
}
