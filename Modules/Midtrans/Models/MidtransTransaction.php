<?php

namespace Modules\Midtrans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MidtransTransaction extends Model
{
    protected $fillable = [
        'midtrans_credential_id',
        'order_id',
        'transaction_id',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'transaction_time',
        'snap_token',
        'snap_url',
        'customer_details',
        'item_details',
        'metadata',
        'raw_response',
        'updated_via',
        'status_updated_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'status_updated_at' => 'datetime',
        'customer_details' => 'array',
        'item_details' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Relasi ke credential
     */
    public function credential(): BelongsTo
    {
        return $this->belongsTo(MidtransCredential::class, 'midtrans_credential_id');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('transaction_status', $status);
    }

    /**
     * Scope untuk transaksi sukses
     */
    public function scopeSuccess($query)
    {
        return $query->whereIn('transaction_status', ['capture', 'settlement']);
    }

    /**
     * Scope untuk transaksi pending
     */
    public function scopePending($query)
    {
        return $query->where('transaction_status', 'pending');
    }

    /**
     * Scope untuk transaksi gagal
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('transaction_status', ['deny', 'cancel', 'expire']);
    }

    /**
     * Check apakah transaksi sukses
     */
    public function isSuccess(): bool
    {
        return in_array($this->transaction_status, ['capture', 'settlement']);
    }

    /**
     * Check apakah transaksi pending
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Check apakah transaksi gagal
     */
    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire']);
    }
}
