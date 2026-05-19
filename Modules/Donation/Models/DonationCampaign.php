<?php

namespace Modules\Donation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class DonationCampaign extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'cover_image_path',
        'contact_name',
        'contact_phone',
        'target_amount',
        'collected_amount',
        'deadline_at',
        'status',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'target_amount' => 'integer',
        'collected_amount' => 'integer',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(DonationDonation::class, 'campaign_id');
    }

    public function paidDonations(): HasMany
    {
        return $this->donations()->where('status', 'paid');
    }

    public function collectedAmountComputed(): Attribute
    {
        return Attribute::get(function () {
            return (int) $this->paidDonations()->sum('amount');
        });
    }

    public function progressPercent(): Attribute
    {
        return Attribute::get(function () {
            if (($this->target_amount ?? 0) <= 0) {
                return 0;
            }
            $value = ($this->collected_amount_computed / $this->target_amount) * 100;
            if ($this->collected_amount_computed > 0 && $value < 1) {
                return 1;
            }
            return (int) min(100, round($value));
        });
    }

    protected static function booted(): void
    {
        static::creating(function (self $campaign) {
            if (!$campaign->slug && $campaign->title) {
                $campaign->slug = Str::slug($campaign->title);
            }
        });
    }
}
