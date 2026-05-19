<?php

use PhpParser\Node\Expr\AssignOp\Mod;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\Guru\GuruServiceProvider::class,
    Modules\TahunAjaran\TahunAjaranServiceProvider::class,
    Modules\MataPelajaran\MataPelajaranServiceProvider::class,
    Modules\Angkatan\AngkatanServiceProvider::class,
    Modules\Kelas\KelasServiceProvider::class,
    Modules\MediaAsset\MediaAssetServiceProvider::class,
    Modules\KalenderDidik\KalenderDidikServiceProvider::class,
    Modules\DynamicForm\DynamicFormServiceProvider::class,
    Modules\Donation\DonationServiceProvider::class,
    Modules\Forum\ForumServiceProvider::class,
    Modules\Siswa\SiswaServiceProvider::class,
    Modules\AuditLog\AuditLogServiceProvider::class,
    Modules\JadwalPelajaran\JadwalPelajaranServiceProvider::class,
];
