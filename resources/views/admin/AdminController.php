<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Application;

class AdminController extends Controller
{
    public function index()
    {
        // For admin dashboard show all tenders and all applications
        $tenders = Tender::orderBy('created_at', 'desc')->get();
        $applications = Application::with('tender', 'user')->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('tenders', 'applications'));
    }
}
