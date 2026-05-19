<?php

namespace Modules\Kelas\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Kelas\Database\Factories\KelasFactory;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
    ];

    protected static function newFactory()
    {
        return KelasFactory::new();
    }
}