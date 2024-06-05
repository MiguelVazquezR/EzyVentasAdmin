<?php

namespace App\Http\Controllers;

use App\Models\SettingHistory;
use App\Models\Store;
use Illuminate\Http\Request;

class SettingHistoryController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        //
    }

    public function show(SettingHistory $settingHistory)
    {
        //
    }

    public function edit(SettingHistory $settingHistory)
    {
        //
    }

    public function update(Request $request, SettingHistory $settingHistory)
    {
        //
    }

    public function destroy(SettingHistory $settingHistory)
    {
        //
    }

    // API
    public function getByStore(Store $store)
    {
        $setting_histories = SettingHistory::latest('id')->where('store_id', $store->id)->get()->map(function ($activity) {
            return [
                'content' => $activity->user_name . ' ' . $activity->description,
                'timestamp' => $activity->created_at->isoFormat('DD MMM, YYYY • hh:mm a'),
            ];
        });

        return response()->json(['items' => $setting_histories]);
    }
}
