<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\SettingHistory;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    private $departments_can_see_all_stores = ['Dirección', 'Gerente de ventas'];

    public function index()
    {
        // Obtener todos los vendedores y seleccionar solo las columnas id y name
        $all_sellers = Admin::all('name', 'id');
        // Transformar los resultados al formato deseado
        $sellers = $all_sellers->map(function ($seller) {
            return ['label' => $seller->name, 'value' => $seller->id];
        })->values()->all();

        // obtener las tiendas dependiendo del departamento del admin
        $stores = Store::latest('id')
            ->with(['user', 'seller', 'lastPayment.media'])
            ->when(!in_array(auth()->user()->employee_properties['department'], $this->departments_can_see_all_stores), function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->get()
            ->take(30);

        $total_stores = Store::all()->count();

        return inertia('Store/Index', compact('sellers', 'total_stores', 'stores'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($store_id)
    {
        $store = Store::with(['seller', 'payments.media', 'media'])->find($store_id);
        $stores = Store::latest('id')->get(['id', 'name']);

        return inertia('Store/Show', compact('store', 'stores'));
    }

    public function edit(Store $store)
    {
        return inertia('Store/Edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        //
    }

    public function destroy(Store $store)
    {
        $store->delete();
    }

    public function getMatches($query)
    {
        $stores = Store::latest()
            ->with(['user', 'seller'])
            ->when(!in_array(auth()->user()->employee_properties['department'], $this->departments_can_see_all_stores), function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('contact_name', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $stores]);
    }

    public function getFilters($prop, $value)
    {
        $stores = Store::latest()
            ->with(['user', 'seller'])
            ->when(!in_array(auth()->user()->employee_properties['department'], $this->departments_can_see_all_stores), function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->where($prop, $value)
            ->get();

        return response()->json(['items' => $stores]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        $stores = Store::latest('id')
            ->with(['user', 'seller'])
            ->when(!in_array(auth()->user()->employee_properties['department'], $this->departments_can_see_all_stores), function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->get()
            ->skip($offset)
            ->take(30);

        return response()->json(['items' => $stores]);
    }

    public function support(Store $store)
    {
        $store = $store->load(['settings']);

        return inertia('Store/Support', compact('store'));
    }

    // API
    public function getSettingsByModule(Store $store, $module)
    {
        $items = $store->settings()->where('module', $module)->get();

        return response()->json(compact('items'));
    }

    public function toggleSettingValue(Request $request, Store $store, $setting_id)
    {
        $new_value = $request->value ? 1 : null;
        $store->settings()->updateExistingPivot($setting_id, ['value' => $new_value]);
        $setting_name = Setting::find($setting_id)->key;
        $action = $new_value ? 'activó' : 'desactivó';

        // Guardar el movimiento en historial
        SettingHistory::create([
            'description' => $action . " la configuración \"$setting_name\"",
            'user_name' => auth()->user()->name,
            'store_id' => $store->id,
        ]);


        return response()->json([]);
    }

    public function asignSeller(Request $request, Store $store)
    {
        // Actualizar el modelo Store con el nuevo seller_id
        $store->update([
            'seller_id' => $request->sellerId
        ]);

        // Cargar la relación 'seller' en el modelo actualizado
        $store->load('seller', 'lastPayment.media');

        // Devolver la respuesta con el modelo actualizado y la relación cargada
        return response()->json(['item' => $store]);
    }

    public function updateModules(Request $request, Store $store)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'string|in:Punto de venta,Reportes,Ventas registradas,Caja,Productos,Configuraciones,Clientes,Gastos,Cotizaciones,Renta de productos,Tienda en línea,Servicios',
        ]);

        if (!$store) {
            return response()->json(['message' => 'Tienda no encontrada'], 404);
        }

        $store->activated_modules = $request->modules;
        $store->save();

        return response()->json(['message' => 'Módulos actualizados correctamente']);
    }

}
