<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Tender;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, Tender $tender)
    {
        if ($tender->status !== 'approved') {
            return back()->with('error','Cannot apply to this tender');
        }

        $request->validate(['notes' => 'nullable|string']);

        Application::create([
            'tender_id' => $tender->id,
            'user_id' => 1, // dummy
            'notes' => $request->notes,
        ]);

        return back()->with('success','Applied successfully!');
    }

    public function updateStatus(Request $request, Application $application)
    {
        $application->update(['status'=>$request->status]);
        return back()->with('success','Application status updated!');
    }
}
