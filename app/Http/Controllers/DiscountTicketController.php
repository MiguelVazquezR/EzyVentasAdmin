<?php

namespace App\Http\Controllers;

use App\Models\DiscountTicket;
use Illuminate\Http\Request;

class DiscountTicketController extends Controller
{
    public function index()
    {
        $discount_tickets = DiscountTicket::latest('id')->get()->take(30);
        $total_discount_tickets = DiscountTicket::all()->count();

        return inertia('DiscountTicket/Index', compact('discount_tickets', 'total_discount_tickets'));
    }

    public function create()
    {
        return inertia('DiscountTicket/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:100',
            'is_percentage_discount' => 'nullable|boolean',
            'discount_amount' => 'required|number|' . $request->is_percentage_discount ? 'min:1|max:100' : 'min:10|max:9999',
            'is_active' => 'nullable|boolean',
            'expired_date' => 'nullable|date|after:tomorrow',
        ]);

        DiscountTicket::create($request->all());

        return to_route('discount-tickets.index');
    }

    public function show(DiscountTicket $discount_ticket)
    {
        //
    }

    public function edit(DiscountTicket $discount_ticket)
    {
        return inertia('DiscountTicket/Edit', compact('discount_ticket'));
    }

    public function update(Request $request, DiscountTicket $discount_ticket)
    {
        $request->validate([
            'code' => 'required|string|max:100',
            'is_percentage_discount' => 'nullable|boolean',
            'discount_amount' => 'required|number|' . $request->is_percentage_discount ? 'min:1|max:100' : 'min:10|max:9999',
            'is_active' => 'nullable|boolean',
            'expired_date' => 'nullable|date|after:tomorrow',
        ]);

        $discount_ticket->update($request->all());

        return to_route('discount-tickets.index');
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
        $discount_tickets = DiscountTicket::latest('id')
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('code', 'LIKE', "%$query%")
            ->orWhere('discount_amount', 'LIKE', "%$query%")
            ->get();

        return response()->json(['items' => $discount_tickets]);
    }

    public function getItemsByPage($currentPage)
        {
            $offset = $currentPage * 30;

            $discount_tickets = DiscountTicket::latest('id')
                ->get()
                ->skip($offset)
                ->take(30);

            return response()->json(['items' => $discount_tickets]);
        }
}
