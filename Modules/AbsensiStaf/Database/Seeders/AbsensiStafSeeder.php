<?php

namespace Modules\AbsensiStaf\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\AbsensiStaf\Models\HariLibur;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;

class AbsensiStafSeeder extends Seeder
{
    public function run(): void
    {
        AbsensiStaf::truncate();
        HariLibur::truncate();

        // Hari libur nasional 2025/2026
        $liburs = [
            ['tanggal' => '2025-08-17', 'keterangan' => 'HUT Kemerdekaan RI'],
            ['tanggal' => '2025-09-05', 'keterangan' => 'Maulid Nabi Muhammad SAW'],
            ['tanggal' => '2025-12-25', 'keterangan' => 'Hari Natal'],
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru Masehi'],
            ['tanggal' => '2026-03-28', 'keterangan' => 'Idul Fitri 1447 H'],
            ['tanggal' => '2026-03-29', 'keterangan' => 'Idul Fitri 1447 H'],
        ];

        foreach ($liburs as $libur) {
            HariLibur::create(array_merge($libur, ['is_aktif' => true]));
        }

        // Dummy absensi 30 hari terakhir
        $gurus = Guru::all();

        for ($hari = 29; $hari >= 0; $hari--) {
            $tanggal   = Carbon::now()->subDays($hari);
            $dayOfWeek = $tanggal->dayOfWeek;

            // Skip Minggu
            if ($dayOfWeek === 0) continue;

            // Skip hari libur
            if (HariLibur::isLibur($tanggal)) continue;

            foreach ($gurus as $guru) {
                $isLibur = $dayOfWeek === 0;
                if ($isLibur) continue;

                // 80% hadir, 10% terlambat, 5% izin, 5% alpha
                $rand = rand(1, 100);

                if ($rand <= 80) {
                    // Hadir tepat waktu
                    $clockIn  = $tanggal->copy()->setHour(7)->setMinute(rand(15, 34));
                    $clockOut = $dayOfWeek === 6
                        ? $tanggal->copy()->setHour(11)->setMinute(rand(0, 15))
                        : $tanggal->copy()->setHour(16)->setMinute(rand(0, 30));

                    AbsensiStaf::create([
                        'guru_id'      => $guru->id,
                        'tanggal'      => $tanggal->format('Y-m-d'),
                        'clock_in_at'  => $clockIn,
                        'clock_in_lat' => -7.5566013 + (rand(-100, 100) / 100000),
                        'clock_in_lng' => 110.8226971 + (rand(-100, 100) / 100000),
                        'clock_out_at' => $clockOut,
                        'clock_out_lat'=> -7.5566013 + (rand(-100, 100) / 100000),
                        'clock_out_lng'=> 110.8226971 + (rand(-100, 100) / 100000),
                        'status'       => 'hadir',
                    ]);

                } elseif ($rand <= 90) {
                    // Terlambat
                    $clockIn  = $tanggal->copy()->setHour(7)->setMinute(rand(36, 59));
                    $clockOut = $dayOfWeek === 6
                        ? $tanggal->copy()->setHour(11)->setMinute(rand(0, 15))
                        : $tanggal->copy()->setHour(16)->setMinute(rand(0, 30));

                    AbsensiStaf::create([
                        'guru_id'      => $guru->id,
                        'tanggal'      => $tanggal->format('Y-m-d'),
                        'clock_in_at'  => $clockIn,
                        'clock_in_lat' => -7.5566013,
                        'clock_in_lng' => 110.8226971,
                        'clock_out_at' => $clockOut,
                        'clock_out_lat'=> -7.5566013,
                        'clock_out_lng'=> 110.8226971,
                        'status'       => 'terlambat',
                    ]);

                } elseif ($rand <= 95) {
                    // Izin
                    AbsensiStaf::create([
                        'guru_id'    => $guru->id,
                        'tanggal'    => $tanggal->format('Y-m-d'),
                        'status'     => 'izin',
                        'keterangan' => 'Keperluan keluarga',
                    ]);

                } else {
                    // Alpha — tidak ada record atau record tanpa clock in
                    AbsensiStaf::create([
                        'guru_id' => $guru->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'status'  => 'alpha',
                    ]);
                }
            }
        }
    }
}