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
        $title = "Respuesta a pago registrado";
        $description = "Tu pago creado el {$payment->created_at->isoFormat('ddd DD MMMM, Y')} tiene un estatus de <b class='text-primary'>$payment->status</b>";
        if (app()->environment() === 'local'){
            $url = 'http://localhost:8000/user/profile';
        } else {
            $url = 'https://ezyventas.com/user/profile';
        }
        $payment->store->user->notify(new StoreBasicNotification($title, $description, $url));
    }
}
