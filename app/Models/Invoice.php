<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no', 'client_name', 'amount', 'tax_rate',
        'status', 'issue_date', 'due_date', 'description',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
        'amount'     => 'decimal:2',
        'tax_rate'   => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Invoice $invoice) {
            if (!$invoice->invoice_no) {
                $invoice->invoice_no = 'FTR-' . date('Y') . '-' . str_pad(
                    (static::whereYear('created_at', date('Y'))->count() + 1),
                    4, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
