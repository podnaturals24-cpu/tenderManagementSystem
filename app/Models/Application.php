<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'user_id',
        'status',
        'notes'
    ];

    public function tender()
    {
        return $this->belongsTo(\App\Models\Tender::class, 'tender_id');
    }
    

public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}

}
