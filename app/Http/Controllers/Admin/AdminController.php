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
        $active = $request->query('filter', 'all'); // all | second | third

        $q = Tender::query()->latest('created_at');

        switch ($active) {
            case 'second':
                // Show tenders that have user-submitted second-stage data and are pending review
                $q->where('approve_stage', 'second_stage_pending');
                break;

            case 'third':
                // Show tenders that have user-submitted third-stage data and are pending review
                $q->where('approve_stage', 'third_stage_pending');
                break;

            case 'all':
            default:
                // all tenders
                break;
        }

        // If you expect large data, use paginate(20) and render links()
        $tenders = $q->get();

        // Metrics for header cards
        $counts = [
            'total' => Tender::count(),
            'approved_first' => Tender::where('status', 'approved')->count(),
            'second_pending' => Tender::where('approve_stage', 'second_stage_pending')->count(),
            'third_pending'  => Tender::where('approve_stage', 'third_stage_pending')->count(),
        ];

        // Applications panel (as in your current admin dashboard)
        $applications = Application::with(['tender','user'])->latest()->get();

        return view('admin.dashboard', compact('tenders', 'applications', 'active', 'counts'));
    }
}