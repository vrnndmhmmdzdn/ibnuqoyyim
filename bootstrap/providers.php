<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\Angkatan\AngkatanServiceProvider::class,
    Modules\AuditLog\AuditLogServiceProvider::class,
    Modules\Donation\DonationServiceProvider::class,
    Modules\DynamicForm\DynamicFormServiceProvider::class,
    Modules\Forum\ForumServiceProvider::class,
    Modules\Guru\GuruServiceProvider::class,
    Modules\JadwalPelajaran\JadwalPelajaranServiceProvider::class,
    Modules\KalenderDidik\KalenderDidikServiceProvider::class,
    Modules\Kelas\KelasServiceProvider::class,
    Modules\MataPelajaran\MataPelajaranServiceProvider::class,
    Modules\MediaAsset\MediaAssetServiceProvider::class,
    Modules\Siswa\SiswaServiceProvider::class,
    Modules\TahunAjaran\TahunAjaranServiceProvider::class,
    Modules\JurnalGuru\JurnalGuruServiceProvider::class,
    Modules\AbsensiStaf\AbsensiStafServiceProvider::class,
];
