<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    public function index()
    {
        $tenders = Tender::orderBy('created_at', 'desc')->get();
        return view('admin.tenders.index', compact('tenders'));
    }

    public function show(Tender $tender)
    {
        return view('admin.tenders.show', compact('tender'));
    }

     public function approve(Request $request, Tender $tender)
    {
        $stage = $request->input('stage', 'first'); // first|second|third

        if ($stage === 'first') {
            // First review of base tender details
            $tender->status = 'approved';
            $tender->approve_stage = 'second_stage_pending';
        } elseif ($stage === 'second') {
            // After user submitted EMD (second-stage) data
            $tender->approve_stage = 'third_stage_pending';
            // (Optional) persist a marker:
            // $tender->second_stage_status = 'approved';
        } elseif ($stage === 'third') {
            // Final approval after third-stage form
            $tender->approve_stage = 'third_stage_approved';
            // (Optional) final marker:
            // $tender->tender_apply_status = 'completed';
        }

        $tender->save();

        return back()->with('success', 'Tender approved for stage: '.$stage.'.');
    }

    public function disapprove(Request $request, Tender $tender)
    {
        $stage = $request->input('stage', 'first'); // first|second|third

        if ($stage === 'first') {
            $tender->status = 'disapproved';
            // Optionally reset pipeline:
            $tender->approve_stage = null;
        } elseif ($stage === 'second') {
            // Second-stage rejected; stay in second-stage lane or move back
            // Choose one:
            $tender->approve_stage = 'second_stage_rejected';
        } elseif ($stage === 'third') {
            $tender->approve_stage = 'third_stage_rejected';
        }

        $tender->save();

        return back()->with('success', 'Tender disapproved for stage: '.$stage.'.');
    }
}
