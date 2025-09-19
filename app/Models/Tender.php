<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'created_by',
        'status',
    ];
    

    protected $dates = ['last_date'];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
