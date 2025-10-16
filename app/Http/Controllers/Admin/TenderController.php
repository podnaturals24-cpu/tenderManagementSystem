<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $stage  = $request->query('stage');

        $tenders = Tender::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($stage,  fn($q) => $q->where('approve_stage', $stage))
            ->latest('created_at')
            ->paginate(20)->withQueryString();

        return view('admin.tenders.index', compact('tenders', 'status', 'stage'));
    }

    public function show(Tender $tender)
    {
        return view('admin.tenders.show', compact('tender'));
    }


    public function approve(Request $request, Tender $tender)
    {
        $data = $request->validate([
            'stage' => ['required', Rule::in(['first','second','third'])],
        ]);
        $intent = $data['stage'];
    
        // Current DB state
        $isFirst   = empty($tender->approve_stage);
        $isS2Pend  = $tender->approve_stage === Tender::S2_PENDING;
        $isS2Appr  = $tender->approve_stage === Tender::S2_APPROVED;
        $isS3Pend  = $tender->approve_stage === Tender::S3_PENDING;
        $isS3Appr  = $tender->approve_stage === Tender::S3_APPROVED;
    
        // Approve rules:
        // - first: allowed only when approve_stage is null/empty
        // - second: allowed only from second_stage_pending (NOT from approved)
        // - third: allowed only from third_stage_pending (NOT from approved)
        if ($intent === 'first') {
            if (! $isFirst) return back()->with('error', 'Tender is not in first stage.');
            $tender->update(['status' => Tender::ST_APPROVED]); // approve_stage stays null
            return back()->with('success', 'First stage approved.');
        }
    
        if ($intent === 'second') {
            if (! $isS2Pend) return back()->with('error', 'Tender is not in second-stage pending.');
            $tender->update(['approve_stage' => Tender::S2_APPROVED]);
            return back()->with('success', 'Second stage approved.');
        }
    
        // $intent === 'third'
        if (! $isS3Pend) return back()->with('error', 'Tender is not in third-stage pending.');
        $tender->update(['approve_stage' => Tender::S3_APPROVED]);
        return back()->with('success', 'Third stage approved.');
    }
    
    public function disapprove(Request $request, Tender $tender)
    {
        $data = $request->validate([
            'stage' => ['required', Rule::in(['first','second','third'])],
        ]);
        $intent = $data['stage'];
    
        $isFirst   = empty($tender->approve_stage);
        $isS2Pend  = $tender->approve_stage === Tender::S2_PENDING;
        $isS2Appr  = $tender->approve_stage === Tender::S2_APPROVED;
        $isS3Pend  = $tender->approve_stage === Tender::S3_PENDING;
        $isS3Appr  = $tender->approve_stage === Tender::S3_APPROVED;
    
        // Disapprove rules:
        // - first: only when in first stage
        // - second: allowed from second_stage_pending OR second_stage_approved -> send back to All (null stage, pending)
        // - third:  allowed from third_stage_pending OR third_stage_approved  -> send back to second_stage_approved
        if ($intent === 'first') {
            if (! $isFirst) return back()->with('error', 'Tender is not in first stage.');
            $tender->update(['status' => Tender::ST_DISAPPROVED, 'approve_stage' => null]);
            return back()->with('success', 'Tender disapproved.');
        }
    
        if ($intent === 'second') {
            if (! ($isS2Pend || $isS2Appr)) return back()->with('error', 'Tender is not in second-stage review.');
            $tender->update(['approve_stage' => null, 'status' => Tender::ST_PENDING]); // back to All; user must resubmit stage 2
            return back()->with('success', 'Second stage disapproved. Sent back to All.');
        }
    
        // $intent === 'third'
        if (! ($isS3Pend || $isS3Appr)) return back()->with('error', 'Tender is not in third-stage review.');
        $tender->update(['approve_stage' => Tender::S2_APPROVED]); // back to 2nd approved; user resubmits stage 3
        return back()->with('success', 'Third stage disapproved. Sent back to Second-stage approved.');
    }


    // (Optional) download method if you linked to it in Blade
    public function download(Tender $tender)
    {
        abort_unless($tender->document_path && \Storage::disk('public')->exists($tender->document_path), 404);
        return \Storage::disk('public')->download($tender->document_path, basename($tender->document_path));
    }
}