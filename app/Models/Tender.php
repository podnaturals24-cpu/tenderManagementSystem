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
        'approve_stage',  

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

    protected $casts = [
        'last_date'   => 'datetime',
        'expiry_date' => 'datetime',
    ];

    // Status
    public const ST_PENDING     = 'pending';
    public const ST_APPROVED    = 'approved';
    public const ST_DISAPPROVED = 'disapproved';

    // Stages
    public const S2_PENDING   = 'second_stage_pending';
    public const S2_APPROVED  = 'second_stage_approved';
    public const S3_PENDING   = 'third_stage_pending';
    public const S3_APPROVED  = 'third_stage_approved';

    // Scopes
    public function scopeSecondPending($q) { return $q->where('approve_stage', self::S2_PENDING); }
    public function scopeThirdPending($q)  { return $q->where('approve_stage', self::S3_PENDING); }
    

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