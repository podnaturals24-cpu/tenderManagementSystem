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

    public function approve(Tender $tender)
    {
        $tender->update(['status' => 'approved']);
        return back()->with('success','Tender Approved!');
    }

    public function disapprove(Tender $tender)
    {
        $tender->update(['status' => 'disapproved']);
        return back()->with('success','Tender Disapproved!');
    }
}
