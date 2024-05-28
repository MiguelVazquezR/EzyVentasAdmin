<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function index()
    {
        // Obtener todos los vendedores y seleccionar solo las columnas id y name
        $all_sellers = Admin::all('name', 'id');
        // Transformar los resultados al formato deseado
        $sellers = $all_sellers->map(function ($seller) {
            return ['label' => $seller->name, 'value' => $seller->id];
        })->values()->all();

        $stores = Store::latest('id')
            ->with(['user', 'seller', 'lastPayment.media'])
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
        $store = Store::with(['seller', 'payments'])->find($store_id);
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
        $stores = Store::query()
            ->with(['user', 'seller'])
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('contact_name', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $stores]);
    }

    public function getFilters($prop, $value)
    {
        $stores = Store::query()
            ->with(['user', 'seller'])
            ->where($prop, $value)
            ->get();

        return response()->json(['items' => $stores]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        $stores = Store::latest('id')
            ->with(['user', 'seller'])
            ->get()
            ->skip($offset)
            ->take(30);

        return response()->json(['items' => $stores]);
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
}
