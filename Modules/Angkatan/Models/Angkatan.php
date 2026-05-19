<?php

namespace Modules\Angkatan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Angkatan\Database\Factories\AngkatanFactory;

class Angkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'angkatan';

    protected $fillable = [
        'nama_angkatan',
        'angkatan_ke',
        'tahun_mulai',
        'status',
    ];

    protected static function newFactory()
    {
        return AngkatanFactory::new();
    }
}