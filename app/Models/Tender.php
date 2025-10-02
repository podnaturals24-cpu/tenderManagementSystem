<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'last_date',
        'document_path',
        'contact_person_name',
        'contact_person_number',
        'contact_email',
        'user_id',
        'created_by',
        'status',
    ];

    public function applications()
{
    return $this->hasMany(\App\Models\Application::class, 'tender_id');
}


    // Optional: who created the tender
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
