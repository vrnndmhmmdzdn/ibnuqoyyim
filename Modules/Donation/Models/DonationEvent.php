<?php

namespace Modules\Donation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'donation_id',
        'event_type',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(DonationDonation::class, 'donation_id');
    }
}
