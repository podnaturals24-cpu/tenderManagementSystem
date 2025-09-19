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
        return $this->belongsTo(Tender::class);
    }
}
