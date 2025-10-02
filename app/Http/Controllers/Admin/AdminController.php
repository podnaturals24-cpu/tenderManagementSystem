<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\Application;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard.
     */
    public function index()
    {
        // load all tenders and all applications for admin overview
        $tenders = Tender::orderBy('created_at', 'desc')->get();
        $applications = Application::with(['tender','user'])->orderBy('created_at', 'desc')->get();

        return view('dashboard', compact('tenders', 'applications'));
    }
}
