<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Mou;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_aktif' => Mou::where('status', 'aktif')->public()->count(),
            'total_institutions' => Institution::whereHas('mous', function ($q) {
                $q->where('visibility', 'public');
            })->count(),
            'total_nasional' => Mou::public()->where('level', 'nasional')->count(),
            'total_internasional' => Mou::public()->where('level', 'internasional')->count(),
        ];

        $categoryStats = Category::withCount(['mous' => function ($q) {
            $q->where('visibility', 'public');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        $recentMous = Mou::public()
            ->with(['institution', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $activeMous = Mou::public()
            ->active()
            ->with(['institution', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $renewedMous = Mou::public()
            ->where('renewal_count', '>', 0)
            ->with(['institution'])
            ->latest('updated_at')
            ->take(4)
            ->get();

        $partners = Institution::whereHas('mous', function ($q) {
            $q->where('visibility', 'public');
        })->take(12)->get();

        $yearlyData = Mou::public()
            ->selectRaw('YEAR(start_date) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->take(6)
            ->get();

        return view('public.home', compact(
            'stats',
            'categoryStats',
            'recentMous',
            'activeMous',
            'renewedMous',
            'partners',
            'yearlyData'
        ));
    }

    public function tentang()
    {
        $aboutContent = \App\Models\SiteSetting::get('about_content');
        return view('public.tentang', compact('aboutContent'));
    }
}
