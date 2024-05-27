<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'user_name',
        'store_id',
    ];
}
