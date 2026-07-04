<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Institution;
use App\Models\Mou;
use App\Models\MouRenewal;
use App\Models\Notification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_aktif' => Mou::where('status', 'aktif')->count(),
            'total_expire' => Mou::where('status', 'expire')->count(),
            'total_akan_expire' => Mou::where('status', 'akan_expire')->count(),
            'total_institutions' => Institution::count(),
            'total_mou' => Mou::where('cooperation_type', 'mou')->count(),
            'total_moa' => Mou::where('cooperation_type', 'moa')->count(),
            'total_ia' => Mou::where('cooperation_type', 'ia')->count(),
        ];

        $reminders = [
            'h90' => Mou::where('status', 'aktif')
                ->whereBetween('end_date', [now(), now()->addDays(90)])
                ->count(),
            'h30' => Mou::where('status', 'aktif')
                ->whereBetween('end_date', [now(), now()->addDays(30)])
                ->count(),
            'h7' => Mou::where('status', 'aktif')
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count(),
        ];

        $recentActivities = ActivityLog::with('admin')
            ->latest()
            ->take(10)
            ->get();

        $recentRenewals = MouRenewal::with(['mou.institution', 'renewedByAdmin'])
            ->latest()
            ->take(5)
            ->get();

        $notifications = Notification::unread()
            ->latest()
            ->take(10)
            ->get();

        $mousByCategory = Mou::selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $mousByYear = Mou::selectRaw('YEAR(start_date) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'reminders',
            'recentActivities',
            'recentRenewals',
            'notifications',
            'mousByCategory',
            'mousByYear'
        ));
    }
}
