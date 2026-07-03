<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::orderBy('date', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'        => ['required', Rule::in(['gelir', 'gider'])],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'date'        => ['required', 'date'],
        ]);

        $transaction = Transaction::create($validated);

        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json($transaction);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'type'        => ['sometimes', Rule::in(['gelir', 'gider'])],
            'amount'      => ['sometimes', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'category'    => ['sometimes', 'string', 'max:100'],
            'date'        => ['sometimes', 'date'],
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json(null, 204);
    }
}
