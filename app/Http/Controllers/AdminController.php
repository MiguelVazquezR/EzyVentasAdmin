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
       $request->validate([
        'name' => 'required|string|max:100',
        'phone' => 'required|string|min:10|max:10',
        'email' => 'required|unique:admins,email',
        'disabled_date' => $request->is_active ? 'nullable' : 'required|date',
        'disabled_reason' => $request->is_active ? 'nullable' : 'required|string|max:255',
        'created_at' => 'required|date',
       ]);

       $user = Admin::create($request->except('userImage') + ['password' => bcrypt('ezyventas')]);

       // Guardar el archivo en la colección 'userImage'
       if ($request->hasFile('userImage')) {
            $user->addMediaFromRequest('userImage')->toMediaCollection('userImage');
        }

        return to_route('admins.index');
    }

    
    public function show($admin_id)
    {
        $user = Admin::with(['media', 'stores'])->find($admin_id);

        // return $user;
        return inertia('User/Show', compact('user'));
    }

    
    public function edit($admin_id)
    {
        $user = Admin::with(['media'])->find($admin_id);

        // return $user;
        return inertia('User/Edit', compact('user'));
    }

    
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'required|unique:admins,email,' .$admin->id,
            'disabled_date' => $request->is_active ? 'nullable' : 'required|date',
            'disabled_reason' => $request->is_active ? 'nullable' : 'required|string|max:255',
            'created_at' => 'required|date',
        ]);

        $admin->update($request->except('userImage'));

         // media
        // Eliminar imagen sólo si se borró desde el input y no se agregó una nueva
        if ($request->userImageCleared) {
            $admin->clearMediaCollection('userImage');
        }

        return to_route('admins.index');
    }


    public function updateWithMedia(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'required|unique:admins,email,' .$admin->id,
            'disabled_date' => $request->is_active ? 'nullable' : 'required|date',
            'disabled_reason' => $request->is_active ? 'nullable' : 'required|string|max:255',
            'created_at' => 'required|date',
        ]);

        $admin->update($request->except('userImage'));

        // media ------------
        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('userImage')) {
            $admin->clearMediaCollection('userImage');
        }

        // Guardar el archivo en la colección 'userImage'
        if ($request->hasFile('userImage')) {
            $admin->addMediaFromRequest('userImage')->toMediaCollection('userImage');
        }

        return to_route('admins.index');
    }

    
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return to_route('admins.index');
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
