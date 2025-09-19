<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenderController extends Controller
{
    public function create()
    {
        return view('tenders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // tender_id removed because it is not in the form
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'last_date' => 'nullable|date',
            'document' => 'nullable|file|max:10240',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
        ]);

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('tenders', 'public');
        }

        // ✅ Save logged-in user instead of dummy
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        Tender::create($data);

        // ✅ Redirect back to dashboard after creation
        return redirect()->route('dashboard')->with('success', 'Tender created successfully!');
    }

    public function index()
    {
        $tenders = Tender::orderBy('last_date', 'asc')->get();
        //dd($tenders);
        return view('tenders.index', compact('tenders'));
    }
    


    public function show(Tender $tender)
    {
        return view('tenders.show', compact('tender'));
    }

    public function downloadDocument(Tender $tender)
    {
        if (!$tender->document_path || !Storage::disk('public')->exists($tender->document_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($tender->document_path);
    }
}
