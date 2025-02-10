<?php

namespace App\Http\Controllers;

use App\Models\DiscountTicket;
use Illuminate\Http\Request;

class DiscountTicketController extends Controller
{
    public function index()
    {
        $discount_tickets = DiscountTicket::latest('id')->get();
        $total_discount_tickets = DiscountTicket::all()->count();

        return inertia('DiscountTicket/Index', compact('discount_tickets', 'total_discount_tickets'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(DiscountTicket $discount_ticket)
    {
        //
    }

    public function edit(DiscountTicket $discount_ticket)
    {
        //
    }

    public function update(Request $request, DiscountTicket $discount_ticket)
    {
        //
    }

    public function destroy(DiscountTicket $discount_ticket)
    {
        //
    }

    public function togglStatus(DiscountTicket $discount_ticket)
    {
        //hace un toggle al estatus del cupón
        $discount_ticket->is_active = !$discount_ticket->is_active;
        $discount_ticket->save();
    }

    public function getMatches($query)
    {
        $discount_tickets = DiscountTicket::latest()
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('code', 'LIKE', "%$query%")
            ->orWhere('discount_amount', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $discount_tickets]);
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
}
