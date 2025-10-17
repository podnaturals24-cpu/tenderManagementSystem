<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\Application;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
{
    $active = $request->query('filter', 'all'); // 'all' | 'second' | 'third'

    $q = \App\Models\Tender::query()->latest('created_at');

    switch ($active) {
        case 'second':
            // Show both: second_stage_pending + second_stage_approved
            $q->whereIn('approve_stage', [
                \App\Models\Tender::S2_PENDING,
            ]);
            break;

        case 'third':
            // Show both: third_stage_pending + third_stage_approved
            $q->whereIn('approve_stage', [
                \App\Models\Tender::S3_PENDING,
            ]);
            break;

        case 'all':
        default:
            // No stage filter (show everything applied by users)
            break;
    }

    $tenders = $q->paginate(20)->withQueryString();

    // Keep your counts (pending-only) as-is
    $counts = [
        'total'          => \App\Models\Tender::count(),
        'approved_first' => \App\Models\Tender::where('status', \App\Models\Tender::ST_APPROVED)->count(),
        'second_pending' => \App\Models\Tender::where('approve_stage', \App\Models\Tender::S2_PENDING)->count(),
        'third_pending'  => \App\Models\Tender::where('approve_stage', \App\Models\Tender::S3_PENDING)->count(),
    ];

    return view('admin.dashboard', compact('tenders', 'active', 'counts'));
}



}