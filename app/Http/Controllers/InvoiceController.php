<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::orderBy('issue_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'tax_rate'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'status'      => ['nullable', Rule::in(['beklemede', 'odendi', 'iptal'])],
            'issue_date'  => ['required', 'date'],
            'due_date'    => ['required', 'date', 'after_or_equal:issue_date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($invoice);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => ['sometimes', 'string', 'max:255'],
            'amount'      => ['sometimes', 'numeric', 'min:0.01'],
            'tax_rate'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'status'      => ['sometimes', Rule::in(['beklemede', 'odendi', 'iptal'])],
            'issue_date'  => ['sometimes', 'date'],
            'due_date'    => ['sometimes', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $invoice->update($validated);

        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json(null, 204);
    }
}
