<?php

namespace App\Http\Controllers;
use App\Http\Controllers;
use App\Models\Application;   // ✅ correct import
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenderController extends Controller
{
    /**
     * Show the create tender form (separate view or used via dashboard).
     */
    public function create()
    {
        return view('tenders.create');
    }

    public function show(Tender $tender)
    {
        return view('admin.tenders.show', compact('tender'));
    }
    
    /**
     * Store a newly created tender.
     */
    public function store(Request $request)
    {
        // Validate input - make fields required (document optional)
        $data = $request->validate([
            // 'tender_id' removed because form doesn't provide it
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'last_date' => 'required|date',
            'pre_bid_date' => 'required|date',  // ✅ new field
            'value_of_tender' => 'required|string', // ✅ new field (longtext)
            'document' => 'nullable|file|max:10240',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_number' => 'required|string|max:50',
            'contact_email' => 'required|email|max:255',
        ]);

        // Handle file upload if present
        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('tenders', 'public');
        } else {
            $data['document_path'] = null;
        }

        // Save logged-in user and initial status
        $data['created_by'] = auth()->id();
        $data['user_id'] = auth()->id();   // <--- new line
        $data['status'] = 'pending';

        // Create tender (ensure Tender::$fillable includes these keys)
        $tender = Tender::create($data);

        // Redirect back to dashboard (or show page) with success message
        return redirect()->route('user.dashboard')->with('success', 'Tender created successfully!');
    }


public function updateTenderData(Request $request, $id)
{
    // 1) Find
    $tender = \App\Models\Tender::findOrFail($id);

    // 2) Validate
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'department' => ['required', 'string', 'max:255'],
        'last_date' => ['required', 'date'],                  // ✅ include expiry_date
        // The browser sends datetime-local as "YYYY-MM-DDTHH:MM"
        // You can keep 'date' or be strict with date_format:Y-m-d\TH:i
        'pre_bid_date' => ['required', 'date'],
        'value_of_tender' => ['required', 'string'],
        'contact_person_name' => ['required', 'string', 'max:255'],
        'contact_person_number' => ['required', 'string', 'max:30'],
        'contact_email' => ['required', 'email'],
        'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],

        // New fields
        'tech_criteria' => ['nullable', 'string'],
        'tech_eligibilty' => ['nullable', 'string'],
        'fin_criteria' => ['nullable', 'string'],
        'fin_elegibility' => ['nullable', 'integer'],
        // Also datetime-local in the form
        'tender_doucemnt_uploaded_date' => ['nullable', 'date'],
    ]);

    // 3) Pull only what we need (✅ include expiry_date)
    $data = $request->only([
        'name',
        'department',
        'last_date',
        'pre_bid_date',
        'value_of_tender',
        'contact_person_name',
        'contact_person_number',
        'contact_email',
        'tech_criteria',
        'tech_eligibilty',
        'fin_criteria',
        'fin_elegibility',
        'tender_doucemnt_uploaded_date',
    ]);

    // 3b) Normalize datetime-local -> MySQL DATETIME ("YYYY-MM-DD HH:MM:SS")
    $normalizeDtLocal = function (?string $v): ?string {
        if (!$v) return $v;
        // from "YYYY-MM-DDTHH:MM" (length 16) to "YYYY-MM-DD HH:MM:SS"
        $v = str_replace('T', ' ', $v);
        if (strlen($v) === 16) {
            $v .= ':00';
        }
        return $v;
    };

    if (!empty($data['pre_bid_date'])) {
        $data['pre_bid_date'] = $normalizeDtLocal($data['pre_bid_date']);
    }
    if (!empty($data['tender_doucemnt_uploaded_date'])) {
        $data['tender_doucemnt_uploaded_date'] = $normalizeDtLocal($data['tender_doucemnt_uploaded_date']);
    }

    // 4) File upload (optional)
    if ($request->hasFile('document')) {
        $path = $request->file('document')->store('tenders', 'public');
        $data['document_path'] = $path;
    }
    // 5) Update
    $tender->update($data);
    $tender->approve_stage = 'second_stage_pending';
    $tender->save();

    

    // 6) Redirect
    return redirect()->back()->with('success', 'Tender details updated successfully.');
}

public function updateTenderEmdData(Request $request, $id)
{
    // 1) Find the tender
    $tender = \App\Models\Tender::findOrFail($id);

    // 2) Validate only the requested fields
    $request->validate([
        'details_of_emd' => ['nullable', 'string'],  // longtext
        'emd_number'     => ['nullable', 'digits_between:1,20'], // int
        'emd_date'       => ['nullable', 'date'],    // date (HTML <input type="date">)
        'expiry_date'    => ['nullable', 'date'],    // date (HTML <input type="date">)
    ]);

    // 3) Extract the allowed fields
    $data = $request->only([
        'details_of_emd',
        'emd_number',
        'emd_date',
        'expiry_date',
    ]);

    // 4) Persist
    $tender->update($data);

    $tender->approve_stage = 'third_stage_pending';
    $tender->save();


    // 5) Redirect/flash
    return redirect()->back()->with('success', 'EMD & expiry details updated successfully.');
}


public function dashboard(Request $request)
{
    $user = Auth::user();

    // ✅ Restrict access to admins only
    if (! $user || $user->role !== 'admin') {
        abort(403, 'Unauthorized'); // or redirect()->route('user.dashboard');
    }

      // ✅ Read filter from URL: ?filter=all|second|third
    $active = $request->query('filter', 'all');

    // Base query
    $q = Tender::query()->latest('created_at');

    // ✅ Pending-only filters per tab
    switch ($active) {
        case 'second':
            $q->where('approve_stage', Tender::S2_PENDING);
            break;

        case 'third':
            $q->where('approve_stage', Tender::S3_PENDING);
            break;

        case 'all':
        default:
            // No stage filter (show everything)
            break;
    }

    // Paginate + preserve query string
    $tenders = $q->paginate(20)->withQueryString();

    // Counts (pending-only for stage counts)
    $counts = [
        'total'          => Tender::count(),
        'approved_first' => Tender::where('status', Tender::ST_APPROVED)->count(),
        'second_pending' => Tender::where('approve_stage', Tender::S2_PENDING)->count(),
        'third_pending'  => Tender::where('approve_stage', Tender::S3_PENDING)->count(),
    ];


    return view('dashboard', compact('tenders', 'active', 'counts'));
}

public function userDashboard()
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'user') {
            abort(403, 'Unauthorized'); // or redirect()->route('user.dashboard');
        }

        $userId = $user->id;



        // Applications made by this user (eager load tender)
        $applications = Application::with('tender')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Tenders created by this user (optional, for "My Tenders")
        $myTenders = Tender::where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Build list of tender IDs the user applied to
        $tenderIds = $applications->pluck('tender_id')->unique()->filter()->values();

        if ($tenderIds->isEmpty()) {
            // If user hasn't applied to any tenders, show tenders where user is the owner (or other fallback)
            $tenders = Tender::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Show only tenders the user applied for and not disapproved
            $tenders = Tender::whereIn('id', $tenderIds)
                ->where('status', '!=', 'Disapproved')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        // dd($tenders);

        // return a user-specific blade (create resources/views/user/dashboard.blade.php)
        return view('userDashboard', compact('applications', 'myTenders', 'tenders'));
    }

    /**
     * Download the tender document file (public storage).
     */
    public function downloadDocument(Tender $tender)
    {
        if (! $tender->document_path || ! Storage::disk('public')->exists($tender->document_path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($tender->document_path);
    }
}