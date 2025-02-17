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
        // $totalRevenue = Sale::sum(DB::raw('current_price * quantity')); //dinero total acumulado de tiendas suscritas
        $totalReports = SupportReport::count();
        $year = request('year', now()->year);

        // Ventas por mes
        $salesData = Sale::selectRaw('MONTH(created_at) as month, SUM(current_price * quantity) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total');

        $salesMonths = Sale::selectRaw('MONTHNAME(created_at) as month, MONTH(created_at) as month_number')
            ->whereYear('created_at', $year)
            ->groupBy('month', 'month_number')
            ->orderBy('month_number')
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
            ->whereYear('sales.created_at', $year)
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
        
        // Obtener ingresos mensuales del año en curso
        $monthlyRevenue = collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
            return [$month => 0]; // Inicializa todos los meses en 0
        })->merge(
            DB::table('payments')
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->whereYear('created_at', $year)
                ->where('status', 'Aprobado')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray()
        )->toArray();        

        // Rellenar los meses sin ingresos con 0
        $months = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];
        $formattedRevenue = [];
        foreach (range(1, 12) as $i) {
            $formattedRevenue[] = $monthlyRevenue[$i] ?? 0;
        }

        // Total de ingresos de todos los años
        $totalCompanyRevenue = DB::table('payments')
            ->where('status', 'Aprobado')
            ->sum('amount');

        $subscriptionPlans = DB::table('stores')
            ->selectRaw("CASE 
                            WHEN suscription_period = 'Mensual' THEN 'Mensual' 
                            WHEN suscription_period = 'Anual' THEN 'Anual' 
                            WHEN suscription_period = 'Periodo de prueba' THEN 'Periodo de prueba' 
                            ELSE 'Otro' 
                         END as plan, COUNT(*) as total")
            ->where('is_active', true)  // Filtra solo las tiendas activas
            ->groupBy(DB::raw("CASE 
                                 WHEN suscription_period = 'Mensual' THEN 'Mensual' 
                                 WHEN suscription_period = 'Anual' THEN 'Anual' 
                                 WHEN suscription_period = 'Periodo de prueba' THEN 'Periodo de prueba' 
                                 ELSE 'Otro' 
                               END"))
            ->pluck('total', 'plan')
            ->toArray();
        
        
        
        $responseData['subscriptionPlansData'] = array_values($subscriptionPlans);
        $responseData['subscriptionPlansLabels'] = array_keys($subscriptionPlans);
        
        return response()->json([
            'storeCount' => $storeCount,
            // 'totalRevenue' => $totalRevenue,
            'totalReports' => $totalReports,
            'salesData' => $salesData,
            'salesMonths' => $salesMonths,
            'reportsByStatus' => array_values($reportsByStatus->toArray()),
            'storeTypeCounts' => $storeTypeCounts,
            'storeTypeLabels' => $storeTypeLabels,
            'topStoresSales' => $topStores->pluck('total_sales'),
            'topStoresNames' => $topStores->pluck('name'),
            'monthlyRevenue' => $formattedRevenue,
            'months' => $months,
            'totalCompanyRevenue' => $totalCompanyRevenue,
            'subscriptionPlansData' => $responseData['subscriptionPlansData'],
            'subscriptionPlansLabels' => $responseData['subscriptionPlansLabels'],
        ]);
    }
}
