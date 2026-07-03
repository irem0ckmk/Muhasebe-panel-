<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $monthly = Transaction::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN type = 'gelir' THEN amount ELSE 0 END) as gelir"),
                DB::raw("SUM(CASE WHEN type = 'gider' THEN amount ELSE 0 END) as gider")
            )
            ->where('date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $byCategory = Transaction::select('category', 'type', DB::raw('SUM(amount) as total'))
            ->where('date', '>=', now()->subMonths(1)->startOfMonth())
            ->groupBy('category', 'type')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $totals = Transaction::select(
                DB::raw("SUM(CASE WHEN type = 'gelir' THEN amount ELSE 0 END) as gelir"),
                DB::raw("SUM(CASE WHEN type = 'gider' THEN amount ELSE 0 END) as gider")
            )->first();

        return response()->json([
            'monthly'     => $monthly,
            'by_category' => $byCategory,
            'totals'      => $totals,
        ]);
    }
}
