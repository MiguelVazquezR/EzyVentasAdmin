<?php

namespace App\Http\Controllers;

use App\Models\SupportReport;
use Illuminate\Http\Request;

class SupportReportController extends Controller
{
    
    public function index()
    {   
        $support_reports = SupportReport::with(['store:id,name,contact_name,contact_phone,seller_id,status' => ['seller:id,name']])->latest()->get()->take(30);
        $total_reports = SupportReport::all()->count();

        // return $support_reports;
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

    
    public function show($support_report_id)
    {
        $support_report = SupportReport::with(['media', 'store' => ['seller']])->find($support_report_id);

        // return $support_report;
        return inertia('SupportReport/Show', compact('support_report'));
    }

    
    public function edit(SupportReport $support_report)
    {
        //
    }

    
    public function update(Request $request, SupportReport $support_report)
    {
        //
    }

    
    public function destroy(SupportReport $support_report)
    {
        //
    }


    public function changeStatus(Request $request, SupportReport $support_report, $status)
    {
        if ( $status == 'process' ) {
            $support_report->update([
                'status' => 'En proceso',
                'notes' => $request->notes
            ]);
        } else {
            $support_report->update([
                'status' => 'Resuelto',
                'notes' => $request->notes
            ]);
        }
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
}
