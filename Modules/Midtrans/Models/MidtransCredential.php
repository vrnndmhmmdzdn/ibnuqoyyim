<?php

namespace Modules\Midtrans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MidtransCredential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'environment',
        'server_key',
        'client_key',
        'merchant_id',
        'is_sanitized',
        'is_3ds',
        'notification_url',
        'finish_url',
        'unfinish_url',
        'error_url',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_sanitized' => 'boolean',
        'is_3ds' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'server_key',
        'client_key',
    ];

    /**
     * Boot method untuk handle enkripsi dan aktif credential
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika credential diaktifkan, nonaktifkan yang lain di environment yang sama
        static::saving(function ($credential) {
            if ($credential->is_active) {
                static::where('environment', $credential->environment)
                    ->where('id', '!=', $credential->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Accessor untuk dekripsi server key
     */
    public function getServerKeyAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::warning('Failed to decrypt server_key for MidtransCredential ID: ' . $this->id . '. The APP_KEY may have changed.');
            return null;
        }
    }

    /**
     * Mutator untuk enkripsi server key
     */
    public function setServerKeyAttribute($value)
    {
        $this->attributes['server_key'] = $value ? encrypt($value) : null;
    }

    /**
     * Accessor untuk dekripsi client key
     */
    public function getClientKeyAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::warning('Failed to decrypt client_key for MidtransCredential ID: ' . $this->id . '. The APP_KEY may have changed.');
            return null;
        }
    }

    /**
     * Mutator untuk enkripsi client key
     */
    public function setClientKeyAttribute($value)
    {
        $this->attributes['client_key'] = $value ? encrypt($value) : null;
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(MidtransTransaction::class);
    }

    /**
     * Scope untuk credential aktif
     */
    public function scopeActive($query, $environment = null)
    {
        $query->where('is_active', true);
        
        if ($environment) {
            $query->where('environment', $environment);
        }
        
        return $query;
    }

    /**
     * Get credential aktif berdasarkan environment
     */
    public static function getActiveCredential($environment = 'sandbox')
    {
        return static::active($environment)->first();
    }
}
