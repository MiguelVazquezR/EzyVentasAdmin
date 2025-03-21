<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', //código del cupón (case sensitive por default)
        'is_percentage_discount', //bandera que indica si el descuento es de porcentaje
        'discount_amount', //cantidad de descuento (porcentaje o efectivo)
        'times_used', // Cantidad de veces que el cuón ha sido usado
        'is_active', // Bandera que indica si el cupón está activo y se puede usar
        'expired_date', //fecha de expiración programada para que se deshabilite automáticamente
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];
}
