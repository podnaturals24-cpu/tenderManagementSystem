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

    /**
     * Index - show tenders.
     * Admins see all tenders, regular users see only approved tenders.
     */
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
    //         // Admin sees all tenders
    //         $tenders = Tender::orderBy('last_date', 'asc')->get();
    //     } else {
    //         // Normal users: only approved tenders
    //        // $tenders = Tender::where('status', 'approved')->orderBy('last_date', 'asc')->get();
    //        $tenders = Tender::where('user_id', auth()->id())
    //                      ->orderBy('last_date', 'asc')
    //                      ->get();
    //     }

    //     return view('tenders.index', compact('tenders'));
    // }

// public function index()
// {
//     $user = auth()->user();

//     // Debug: check the logged in user
//     // dd($user); // uncomment this first to see full user object

//     if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
//         // Admin sees all tenders
//         $tenders = Tender::orderBy('last_date', 'asc')->get();

//         // Debug: check tenders list for admin
//         // dd($tenders); 
//     } else {
//         // Regular user: show only tenders they created
//         $tenders = Tender::where('user_id', auth()->id())
//                          ->orderBy('last_date', 'asc')
//                          ->get();

//         // Debug: check tenders list for user
//         // dd($tenders);
//     }

//     // Debug: see both user id and tenders together
//     // dd([
//     //     'auth_id' => auth()->id(),
//     //     'user' => $user,
//     //     'tenders' => $tenders
//     // ]);

//     return view('tenders.index', compact('tenders'));
// }



public function dashboard()
{
    $user = Auth::user();

    // ✅ Restrict access to admins only
    if (! $user || $user->role !== 'admin') {
        abort(403, 'Unauthorized'); // or redirect()->route('user.dashboard');
    }

    // Admin view: load all applications (with tender + applicant) and all tenders
    $applications = Application::with(['tender', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

    // All tenders (for admin)
    $tenders = Tender::orderBy('created_at', 'desc')->get();

    // Optionally keep "myTenders" if you want to show tenders created by this admin user as well
    $myTenders = Tender::where('created_by', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('dashboard', compact('applications', 'myTenders', 'tenders'));
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
