<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Suscription;
use Illuminate\Http\Request;

class SuscriptionController extends Controller
{

    public function index()
    {
        // Obtener todos los vendedores y seleccionar solo las columnas id y name
        $all_sellers = Admin::all('name', 'id');
        // Transformar los resultados al formato deseado
        $sellers = $all_sellers->map(function ($seller) {
            return ['label' => $seller->name, 'value' => $seller->id];
        })->values()->all();

        $suscriptions = Suscription::latest('id')
            ->with(['user', 'seller'])
            ->take(10)
            ->get();

        $total_suscriptions = Suscription::all()->count();

        // return $suscriptions;
        return inertia('Suscription/Index', compact('sellers', 'total_suscriptions', 'suscriptions'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($suscription_id)
    {   
        $suscription = Suscription::with(['seller', 'payments'])->find($suscription_id);
        $suscriptions = Suscription::latest()->get(['id', 'name']);

        // return $suscription;
        return inertia('Suscription/Show', compact('suscription', 'suscriptions'));
    }

    public function edit(Suscription $suscription)
    {
        return inertia('Suscription/Edit', compact('suscription'));
    }

    public function update(Request $request, Suscription $suscription)
    {
        //
    }

    public function destroy(Suscription $suscription)
    {
        $suscription->delete();
    }

    public function getMatches($query)
    {
        $suscriptions = Suscription::query()
            ->with(['user', 'seller'])
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('contact_name', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $suscriptions]);
    }

    public function getFilters($prop, $value)
    {
        $suscriptions = Suscription::query()
            ->with(['user', 'seller'])
            ->where($prop, $value)
            ->get();

        return response()->json(['items' => $suscriptions]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 10;

        $suscriptions = Suscription::latest('id')
            ->with(['user', 'seller'])
            ->skip($offset)
            ->take(10)
            ->get();

        return response()->json(['items' => $suscriptions]);
    }
}
