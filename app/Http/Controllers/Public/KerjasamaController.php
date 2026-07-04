<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Mou;
use Illuminate\Http\Request;

class KerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mou::public()->with(['institution', 'category']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        if ($request->filled('institution')) {
            $query->where('institution_id', $request->institution);
        }
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cooperation_type')) {
            $query->where('cooperation_type', $request->cooperation_type);
        }
        if ($request->filled('has_implementation')) {
            if ($request->has_implementation == 'yes') {
                $query->has('publicImplementations');
            } elseif ($request->has_implementation == 'no') {
                $query->doesntHave('publicImplementations');
            }
        }

        $mous = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $institutions = Institution::whereHas('mous', fn($q) => $q->public())->orderBy('name')->get();
        $years = Mou::public()->selectRaw('YEAR(start_date) as year')
            ->distinct()->orderByDesc('year')->pluck('year');

        return view('public.kerjasama.index', compact('mous', 'categories', 'institutions', 'years'));
    }

    public function show($slug)
    {
        $mou = Mou::public()
            ->where('slug', $slug)
            ->with(['institution', 'category', 'faculty', 'renewals', 'implementations'])
            ->firstOrFail();

        $relatedMous = Mou::public()
            ->where('id', '!=', $mou->id)
            ->where(function ($q) use ($mou) {
                $q->where('institution_id', $mou->institution_id)
                    ->orWhere('category_id', $mou->category_id);
            })
            ->with(['institution'])
            ->take(4)
            ->get();

        return view('public.kerjasama.show', compact('mou', 'relatedMous'));
    }

    public function viewPdf($slug)
    {
        $mou = Mou::public()
            ->where('slug', $slug)
            ->where('show_pdf_public', true)
            ->firstOrFail();

        if (!$mou->main_document) {
            abort(404);
        }

        return view('public.kerjasama.pdf-viewer', compact('mou'));
    }
}
