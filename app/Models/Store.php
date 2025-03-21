<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Store extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_phone',
        'address',
        'plan',
        'is_active',
        'next_payment',
        'status',
        'seller_id',
        'suscription_period',
        'default_card_id',
        'activated_modules',
    ];

    protected $casts = [
        'next_payment' => 'date',
        'activated_modules' => 'array',
    ];

    //relationships
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'store_id', 'id');
    }

    public function payments() :HasMany
    {
        return $this->hasMany(Payment::class);
    }
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class)
            ->withPivot([
                'value',
            ]);
    }

    public function globalProducts() :BelongsToMany
    {
        return $this->belongsToMany(GlobalProduct::class, 'global_product_store')
            ->withPivot([
                'public_price',
                'cost',
                'min_stock',
                'max_stock',
                'current_stock',
            ])->withTimestamps();
    }

    public function lastPayment()
    {
        return $this->hasOne(Payment::class)->latest();
    }
}
