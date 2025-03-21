<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Store;
use App\Notifications\StoreBasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function index()
    {
        $payments = Payment::with('store:id,name')->latest('id')->get()->take(50);
        $total_payments = Payment::all()->count();

        return inertia('Payment/Index', compact('payments', 'total_payments'));
    }


    public function create()
    {   
        $stores = Store::all(['id', 'name']);

        return inertia('Payment/Create', compact('stores'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0|max:99999',
            'suscription_period' => 'required|string',
            'store_id' => 'required',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Payment::create([
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'suscription_period' => $request->suscription_period,
            'store_id' => $request->store_id,
            'status' => $request->status,
            'notes' => $request->notes,
            'validated_at' => now(),
            'validated_by_id' => auth()->id(),
        ]);

        // Actualizar status y siguiente pago de tienda seleccionada ----------------------------
        $store = Store::find($request->store_id);
        $daysToAdd = ($request->suscription_period === "Mensual") ? 30 : 365;
        $now = now();
        $nextPayment = $store->next_payment ? Carbon::parse($store->next_payment) : $now;

        // Si el pago está vencido, partir de hoy, si no, sumar a la fecha actual de next_payment
        $store->next_payment = ($nextPayment->isPast() ? $now : $nextPayment)->addDays($daysToAdd)->toDateTimeString();

        //actualizar status de la tienda
        if ($nextPayment->isPast()) {
            $store->suscription_period = $daysToAdd === 30 ? 'Mensual' : 'Anual';
            $store->is_active = true;
            $store->status = 'Pagado';
        }
        $store->save();

        return to_route('payments.index');
    }

    public function storeInvoice(Request $request, Payment $payment)
    {
        // borrar factura anterior si es que la hay
        if ($payment->getFirstMedia('invoice')) {
            $payment->clearMediaCollection('invoice');
        }

        $payment->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('invoice'));

        // marcar como factura enviada
        $payment->invoice_status = 'Enviada';
        $payment->save();

        // notificar a suscriptor de factura emitida
        $title = "Factura disponible";
        $description = "La factura solicitada del pago con folio #$payment->id está lista para descargar.";
        if (app()->environment() === 'local') {
            $url = 'http://localhost:8000/payments';
        } else {
            $url = 'https://ezyventas.com/payments';
        }
        $payment->store->user->notify(new StoreBasicNotification($title, $description, $url));
    }

    public function show(Payment $payment)
    {
        //
    }


    public function edit(Payment $payment)
    {
        $stores = Store::all(['id', 'name']);

        return inertia('Payment/Edit', compact('payment', 'stores'));
    }


    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0|max:99999',
            'suscription_period' => 'required|string',
            'store_id' => 'required',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Guardar valores anteriores
        $previousPeriod = $payment->suscription_period;
        $previousDays = $previousPeriod === "Mensual" ? 30 : 365;

        $payment->update([
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'suscription_period' => $request->suscription_period,
            'store_id' => $request->store_id,
            'status' => $request->status,
            'notes' => $request->notes,
            'validated_at' => $request->validated_at,
        ]);

        // Actualizar status y siguiente pago de tienda seleccionada ----------------------------
        $store = Store::find($request->store_id);

        // Nuevo periodo de suscripción
        $updatedDays = $request->suscription_period === "Mensual" ? 30 : 365;

        $now = now();
        $nextPayment = $store->next_payment ? Carbon::parse($store->next_payment) : $now;

        // Ajustar `next_payment` según el cambio de periodo
        if ($previousPeriod !== $request->suscription_period) {
            $nextPayment->subDays($previousDays)->addDays($updatedDays);
        } else {
            $nextPayment = ($nextPayment->isPast() ? $now : $nextPayment)->addDays($updatedDays);
        }

        $store->next_payment = $nextPayment->toDateTimeString();
        $store->save();

        return to_route('payments.index');
    }

    public function notifyFiscalDataError(Request $request, Payment $payment)
    {
        // marcar como Error en datos fiscales
        $payment->invoice_status = 'Error en datos fiscales';
        $payment->save();

        // notificar a suscriptor de error
        $title = "Error al emitir factura";
        $description = "No pudimos emitir la factura del pago con folio #$payment->id debido a errores en los datos fiscales que registraste. Favor de subir una constancia de situación fiscal válida y actualizada";
        if (app()->environment() === 'local') {
            $url = 'http://localhost:8000/payments';
        } else {
            $url = 'https://ezyventas.com/payments';
        }
        $payment->store->user->notify(new StoreBasicNotification($title, $description, $url));
    }

    public function destroy(Payment $payment)
    {
        // Obtener la tienda asociada
        $store = Store::find($payment->store_id);

        if ($store) {
            // Determinar cuántos días restar según el periodo del pago eliminado
            $daysToSubtract = $payment->suscription_period === "Mensual" ? 30 : 365;

            // Convertir next_payment a una fecha válida
            $nextPayment = $store->next_payment ? Carbon::parse($store->next_payment) : now();

            // Restar los días
            $nextPayment->subDays($daysToSubtract);

            // Actualizar la fecha en la tienda
            $store->next_payment = $nextPayment->toDateTimeString();

            // Si la nueva fecha ya venció, actualizar estado de la tienda
            if ($nextPayment->isPast()) {
                $store->is_active = false;
                $store->status = 'Vencido';
            }
            // Guardar cambios en la tienda
            $store->save();
        }

        // Eliminar el pago
        $payment->delete();
    }

    // API
    public function validatePayment(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => $request->status,
            'validated_at' => now(),
            'validated_by_id' => auth()->id(),
            'rejected_reason' => $request->status == 'Aprobado' ? null : $request->rejected_reason,
            'notes' => $request->notes,
        ]);

        //obtiene la tienda a la cual se hizo el pago para guarda el periodo pagado
        $store = Store::find($payment->store_id);
        $store->suscription_period = $request->suscription;
        $store->is_active = 1;
        $store->status = 'Pagado';
        $store->save();

        // notificar a suscriptor de validacion de pago
        $title = "Respuesta a pago registrado";
        $description = "La suscripción de \"$store->name\" ha sido aprobada y esta activa. <br> Ahora puedes seguir utilizando el sistema con total normalidad.";
        if (app()->environment() === 'local') {
            $url = 'http://localhost:8000/user/profile';
        } else {
            $url = 'https://ezyventas.com/user/profile';
        }
        $payment->store->user->notify(new StoreBasicNotification($title, $description, $url));
    }

    public function getItemsByPage($currentPage)
    {
        $offset = ($currentPage - 1) * 50;

        $payments = Payment::with('store:id,name')
            ->latest('id')
            ->get()
            ->skip($offset)
            ->take(50);

        return response()->json(['items' => $payments]);
    }
}
