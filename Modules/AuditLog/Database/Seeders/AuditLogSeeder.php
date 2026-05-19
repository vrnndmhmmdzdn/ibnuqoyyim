<?php

namespace Modules\AuditLog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AuditLog\Models\AuditLog;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        AuditLog::query()->create([
            'event' => 'created',
            'auditable_type' => 'App\\Models\\User',
            'auditable_id' => '1',
            'actor_type' => 'App\\Models\\User',
            'actor_id' => 1,
            'ip_address' => '127.0.0.1',
            'method' => 'POST',
            'url' => url('/example'),
            'user_agent' => 'Seeder',
            'old_values' => null,
            'new_values' => [
                'name' => 'Sample User',
                'email' => 'sample@example.com',
            ],
            'created_at' => now(),
        ]);
    }
}
