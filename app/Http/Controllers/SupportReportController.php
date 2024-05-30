<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Comment;
use App\Models\SupportReport;
use App\Notifications\AdminBasicNotification;
use Illuminate\Http\Request;

class SupportReportController extends Controller
{

    public function index()
    {
        $support_reports = SupportReport::with(['store:id,name,contact_name,contact_phone,seller_id,status' => ['seller:id,name']])->latest()->get()->take(30);
        $total_reports = SupportReport::all()->count();

        return inertia('SupportReport/Index', compact('support_reports', 'total_reports'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(SupportReport $support_report)
    {
        $support_report = $support_report->load(['media', 'store' => ['seller'], 'comments']);
        $admins = Admin::where('is_active', true)->get();

        return inertia('SupportReport/Show', compact('support_report', 'admins'));
    }


    public function edit(SupportReport $support_report)
    {
        //
    }


    public function update(Request $request, SupportReport $support_report)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $support_report->update($validated);

        return to_route('support-reports.index');
    }


    public function destroy(SupportReport $support_report)
    {
        //
    }

    public function getMatches($query)
    {
        $support_reports = SupportReport::query()
            ->join('stores', 'support_reports.store_id', '=', 'stores.id')
            ->select('support_reports.*')
            ->with(['store:id,name,contact_name,contact_phone,seller_id,status' => ['seller:id,name']])
            ->where('support_reports.id', 'LIKE', "%$query%")
            ->orWhere('stores.name', 'LIKE', "%$query%")
            ->orWhere('support_reports.status', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $support_reports]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;
        $support_reports = SupportReport::with(['store:id,name,contact_name,contact_phone,seller_id,status' => ['seller:id,name']])
            ->latest()
            ->skip($offset)
            ->take(30)
            ->get();

        return response()->json(['items' => $support_reports]);
    }

    // public function comment(Request $request, SupportReport $support_reports)
    // {
    //     $comment = new Comment([
    //         'content' => $request->comment,
    //         'admin_id' => auth()->id(),
    //     ]);

    //     $support_reports->comments()->save($comment);

    //     // notificar a responsable de la tienda si no comentó el
    //     $url = route('support-reports.show', $support_reports->id);
    //     if ($support_reports->store->seller_id != auth()->id()) {
    //         $auth = auth()->user();
    //         $title = "Nuevo comentario en reporte de la tienda {$support_reports->store->name}";
    //         $description = "\"{$auth->name}\" ha hecho un comentario en el reporte #{$support_reports->id} perteneciente a la tienda llamada \"{$support_reports->store->name}\" de la cual tu eres responsable.";
    //         $support_reports->store->seller->notify(new AdminBasicNotification($title, $description, $url));
    //     }

    //     // notificar a mencionados 
    //     $mentions = $request->mentions;
    //     foreach ($mentions as $mention) {
    //         $admin = Admin::find($mention['id']);
    //         $title = "Te mencionaron en un comentario";
    //         $description = "\"{$auth->name}\" te ha mencionado en un comentario del reporte #{$support_reports->id} perteneciente a la tienda llamada \"{$support_reports->store->name}\".";
    //         $admin->notify(new AdminBasicNotification($title, $description, $url));
    //     }

    //     // return response()->json(['item' => $comment->fresh('user')]);
    // }
}
