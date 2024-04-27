<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SupportReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'type',
        'description',
        'first_report',
        'status',
        'notes',
        'user_id',
        // 'seller_id', ya lo tiene la tienda.
        'store_id',
    ];

    // relaciones
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // public function seller() :BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
