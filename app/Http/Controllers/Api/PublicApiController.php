<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Mou;
use App\Models\MouRenewal;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Mou::public()->with(['institution:id,name,logo,city', 'category:id,name,color']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $mous = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $mous,
        ]);
    }

    public function show($id)
    {
        $mou = Mou::public()
            ->with(['institution', 'category', 'faculty'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $mou,
        ]);
    }

    public function institutions()
    {
        $institutions = Institution::whereHas('mous', fn($q) => $q->public())
            ->withCount(['mous' => fn($q) => $q->public()])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $institutions,
        ]);
    }

    public function categories()
    {
        $categories = Category::withCount(['mous' => fn($q) => $q->public()])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function statistics()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_mous' => Mou::public()->count(),
                'total_active' => Mou::public()->active()->count(),
                'total_institutions' => Institution::whereHas('mous', fn($q) => $q->public())->count(),
                'by_level' => Mou::public()->selectRaw('level, COUNT(*) as total')->groupBy('level')->get(),
                'by_type' => Mou::public()->selectRaw('type, COUNT(*) as total')->groupBy('type')->get(),
            ],
        ]);
    }

    public function yearlyStatistics()
    {
        $data = Mou::public()
            ->selectRaw('YEAR(start_date) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function renewals($id)
    {
        $mou = Mou::public()->findOrFail($id);
        $renewals = $mou->renewals()->get();

        return response()->json([
            'status' => 'success',
            'data' => $renewals,
        ]);
    }
}
