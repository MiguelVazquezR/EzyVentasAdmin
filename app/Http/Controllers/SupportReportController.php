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
        $support_report = $support_report->load(['media', 'store' => ['seller'], 'comments.admin:id,name,profile_photo_path']);
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
            'status' => 'required|string|max:255',
        ]);

        $support_report->update($validated);
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

    public function changeStatus(SupportReport $supportReport, $status)
    {
        $supportReport->update(['status' => $status]);
    }
}
