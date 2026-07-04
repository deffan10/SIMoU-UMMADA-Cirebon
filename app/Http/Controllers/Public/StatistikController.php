<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Mou;

class StatistikController extends Controller
{
    public function index()
    {
        $totalMous = Mou::public()->count();
        $totalActive = Mou::public()->active()->count();
        $totalInstitutions = Institution::whereHas('mous', fn($q) => $q->public())->count();

        $totalMoU = Mou::public()->where('cooperation_type', 'mou')->count();
        $totalMoA = Mou::public()->where('cooperation_type', 'moa')->count();
        $totalIA = Mou::public()->where('cooperation_type', 'ia')->count();

        $byCategory = Category::withCount(['mous' => fn($q) => $q->public()])
            ->having('mous_count', '>', 0)
            ->orderByDesc('mous_count')
            ->get();

        $byLevel = Mou::public()
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->get();

        $byType = Mou::public()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $byYear = Mou::public()
            ->selectRaw('YEAR(start_date) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $topInstitutions = Institution::withCount(['mous' => fn($q) => $q->public()])
            ->having('mous_count', '>', 0)
            ->orderByDesc('mous_count')
            ->take(10)
            ->get();

        return view('public.statistik', compact(
            'totalMous',
            'totalActive',
            'totalInstitutions',
            'totalMoU',
            'totalMoA',
            'totalIA',
            'byCategory',
            'byLevel',
            'byType',
            'byYear',
            'topInstitutions'
        ));
    }
}
