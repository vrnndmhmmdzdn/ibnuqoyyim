<?php

namespace Modules\Donation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationDonation extends Model
{
    protected $fillable = [
        'campaign_id',
        'donor_name',
        'donor_email',
        'amount',
        'message',
        'is_anonymous',
        'status',
        'payment_provider',
        'provider_order_id',
        'provider_transaction_id',
        'provider_payment_type',
        'provider_raw_response',
        'snap_token',
        'snap_redirect_url',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_anonymous' => 'boolean',
        'provider_raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(DonationCampaign::class, 'campaign_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(DonationEvent::class, 'donation_id');
    }
}
