<?php

namespace Modules\Donation\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Donation\Models\DonationCampaign;
use Modules\Donation\Models\DonationDonation;
use Modules\Donation\Models\DonationEvent;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'title' => 'Pembangunan Musola Kampung',
                'slug' => 'pembangunan-musola-kampung',
                'cover_image_asset' => '01KH0JYZB5KT0XKEC9F780ZK3D.jpeg',
                'description' => 'Bantu warga membangun musola yang layak untuk ibadah dan kegiatan sosial. Dana digunakan untuk material, tukang, dan perlengkapan ibadah.',
                'target_amount' => 150000000,
                'deadline_at' => now()->addMonths(2),
                'status' => 'active',
                'donations' => [
                    ['name' => 'Ahmad', 'email' => 'ahmad@example.com', 'amount' => 50000, 'message' => 'Semoga lancar ya', 'status' => 'paid', 'days_ago' => 2],
                    ['name' => 'Siti', 'email' => 'siti@example.com', 'amount' => 100000, 'message' => 'Bismillah', 'status' => 'paid', 'days_ago' => 1],
                    ['name' => null, 'email' => null, 'amount' => 25000, 'message' => null, 'status' => 'pending', 'days_ago' => 0, 'anonymous' => true],
                    ['name' => 'Hendra', 'email' => null, 'amount' => 75000, 'message' => 'Semoga berkah', 'status' => 'paid', 'days_ago' => 3],
                ],
            ],
            [
                'title' => 'Pembangunan Jembatan Desa',
                'slug' => 'pembangunan-jembatan-desa',
                'cover_image_asset' => '01KHSXE8TVBY9F8TBVX7VQYVFF.jpeg',
                'description' => 'Jembatan penghubung antar dusun rusak dan rawan. Donasi ini untuk pembangunan jembatan yang aman dan tahan lama.',
                'target_amount' => 250000000,
                'deadline_at' => now()->addMonths(3),
                'status' => 'active',
                'donations' => [
                    ['name' => 'Rizki', 'email' => 'rizki@example.com', 'amount' => 150000, 'message' => 'Semangat warga desa', 'status' => 'paid', 'days_ago' => 5],
                    ['name' => 'Dewi', 'email' => 'dewi@example.com', 'amount' => 200000, 'message' => 'Semoga cepat selesai', 'status' => 'paid', 'days_ago' => 4],
                    ['name' => 'Anonim', 'email' => null, 'amount' => 50000, 'message' => null, 'status' => 'failed', 'days_ago' => 1, 'anonymous' => true],
                    ['name' => 'Yusuf', 'email' => 'yusuf@example.com', 'amount' => 300000, 'message' => 'Bermanfaat untuk semua', 'status' => 'paid', 'days_ago' => 2],
                ],
            ],
        ];

        foreach ($campaigns as $data) {
            $donations = $data['donations'];
            $coverImageAsset = $data['cover_image_asset'] ?? null;
            unset($data['donations']);
            unset($data['cover_image_asset']);

            if ($coverImageAsset) {
                $sourcePath = base_path('Modules/Donation/Assets/Images/' . $coverImageAsset);
                if (is_file($sourcePath)) {
                    $extension = pathinfo($coverImageAsset, PATHINFO_EXTENSION) ?: 'jpg';
                    $targetPath = 'donation/campaigns/' . ($data['slug'] ?? Str::slug($data['title'])) . '.' . $extension;

                    if (!Storage::disk('public')->exists($targetPath)) {
                        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));
                    }

                    $data['cover_image_path'] = $targetPath;
                }
            }

            $campaign = DonationCampaign::create($data);

            foreach ($donations as $donationData) {
                $status = $donationData['status'];
                $createdAt = now()->subDays($donationData['days_ago'] ?? 0);
                $paidAt = $status === 'paid' ? $createdAt->copy()->addHours(2) : null;

                $donation = DonationDonation::create([
                    'campaign_id' => $campaign->id,
                    'donor_name' => $donationData['name'],
                    'donor_email' => $donationData['email'],
                    'amount' => $donationData['amount'],
                    'message' => $donationData['message'],
                    'is_anonymous' => (bool) ($donationData['anonymous'] ?? false),
                    'status' => $status,
                    'provider_order_id' => 'DON-' . $campaign->id . '-' . $createdAt->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                    'payment_provider' => 'midtrans',
                    'paid_at' => $paidAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                DonationEvent::create([
                    'donation_id' => $donation->id,
                    'event_type' => 'seeded',
                    'payload' => [
                        'status' => $status,
                    ],
                    'created_at' => $createdAt,
                ]);
            }
        }
    }
}
