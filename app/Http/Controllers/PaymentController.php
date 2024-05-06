<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Notifications\StoreBasicNotification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }


    public function show(Payment $payment)
    {
        //
    }


    public function edit(Payment $payment)
    {
        //
    }


    public function update(Request $request, Payment $payment)
    {
        //
    }


    public function destroy(Payment $payment)
    {
        //
    }

    // API
    public function updateStatus(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => $request->status,
            'validated_at' => now(),
            'validated_by_id' => auth()->id(),
            'rejected_reason' => $request->rejected_reason,
        ]);

    }

    public function validatePayment(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => $request->status,
            'validated_at' => now(),
            'validated_by_id' => auth()->id(),
            'rejected_reason' => $request->rejected_reason,
            'notes' => $request->notes,
        ]);

        // notificar a cliente de validacion de pago
        // $admins = Admin::where('employee_properties->department', 'Dirección')->get();
        // $title = "Nuevo pago registrado";
        // $description = "La tienda '$store->name' ha pagado una suscripción {$validated['suscription_period']} ($ {$validated['amount']}).";
        // $url = 'https://admin.ezyventas.com/suscriptions';
        // $payment->store->user->notify(new StoreBasicNotification());

    }
}
