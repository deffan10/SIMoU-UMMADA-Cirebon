<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Mou;

class DashboardApiController extends Controller
{
    public function summary()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_aktif' => Mou::where('status', 'aktif')->count(),
                'total_expire' => Mou::where('status', 'expire')->count(),
                'total_akan_expire' => Mou::where('status', 'akan_expire')->count(),
                'total_institutions' => Institution::count(),
                'total_mous' => Mou::count(),
            ],
        ]);
    }

    public function chartData()
    {
        $yearly = Mou::selectRaw('YEAR(start_date) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $byCategory = Mou::selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,name,color')
            ->groupBy('category_id')
            ->get();

        $byStatus = Mou::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'yearly' => $yearly,
                'by_category' => $byCategory,
                'by_status' => $byStatus,
            ],
        ]);
    }
}
