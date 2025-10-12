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
        'pre_bid_date',       // ✅ NEW
        'value_of_tender',    // ✅ NEW
        'document_path',
        'contact_person_name',
        'contact_person_number',
        'contact_email',
        'user_id',
        'created_by',
        'status',

        // ✅ Added fields
        'tech_criteria',
        'tech_eligibilty',
        'fin_criteria',
        'fin_elegibility',
        'tender_doucemnt_uploaded_date',
        // ✅ Newly added fields
        'details_of_emd',              // longtext
        'emd_number',                  // int
        'emd_date',                    // date
        'expiry_date',                 // date
        'tender_apply_status',         // varchar

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
