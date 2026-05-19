<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            // Ensure index on submitted_at exists (for sorting)
            if (!$this->indexExists('dynamic_form_submissions', 'dynamic_form_submissions_submitted_at_index')) {
                $table->index('submitted_at', 'dynamic_form_submissions_submitted_at_index');
            }
        });

        // Add composite index for efficient sorting (submitted_at desc, id asc)
        // Using raw SQL to avoid duplicate index error
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        if (!$this->indexExists('dynamic_form_submissions', 'dynamic_form_submissions_submitted_at_id_index')) {
            DB::statement("CREATE INDEX dynamic_form_submissions_submitted_at_id_index ON {$databaseName}.dynamic_form_submissions (submitted_at DESC, id ASC)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            $table->dropIndex('dynamic_form_submissions_submitted_at_id_index');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        try {
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$databaseName, $table, $index]
            );
            
            return isset($result[0]) && $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
