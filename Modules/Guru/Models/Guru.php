<?php

namespace Modules\Guru\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guru\Database\Factories\GuruFactory;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'name',
        'telephone',
        'email',
        'tanggal_masuk'
    ];

    // Accessor untuk WhatsApp link
    public function getWhatsappLinkAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        return 'https://wa.me/' . $phone;
    }

    // Accessor untuk format nama dengan mata pelajaran
    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->subject;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return GuruFactory::new();
    }
}