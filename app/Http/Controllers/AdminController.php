<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
     
    public function index()
    {
        $users = Admin::latest()->get();
        $total_users = Admin::all()->count();

        // return $users;
        return inertia('User/Index', compact('users', 'total_users'));
    }

    
    public function create()
    {
        return inertia('User/Create');
    }

    
    public function store(Request $request)
    {
       //
    }

    
    public function show(Admin $admin)
    {
        //
    }

    
    public function edit(Admin $admin)
    {
        //
    }

    
    public function update(Request $request, Admin $admin)
    {
        //
    }

    
    public function destroy(Admin $admin)
    {
        //
    }


    public function getNotifications()
    {
        $items = NotificationResource::collection(auth()->user()->notifications);

        return response()->json(compact('items'));
    }

    public function deleteNotifications(Request $request)
    {
        auth()->user()->notifications()->whereIn('id', $request->notifications_ids)->delete();

        return response()->json(['message' => "Se han eliminado las notificaciones seleccionadas"]);
    }

    public function readNotifications(Request $request)
    {
        $unread = auth()->user()->unreadNotifications->count();
        if ($request->notifications_ids) {
            auth()->user()->notifications->whereIn('id', $request->notifications_ids)->markAsRead();
        } else {
            auth()->user()->notifications->markAsRead();
        }

        return response()->json(compact('unread'));
    }

    public function getMatches($query)
    {
        $users = Admin::query()
            // ->with(['stores']) //informacion de las tiendas que administra el usuario
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $users]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 10;

        $users = Admin::latest('id')
            // ->with(['stores']) //informacion de las tiendas que administra el usuario
            ->skip($offset)
            ->take(10)
            ->get();

        return response()->json(['items' => $users]);
    }
}
