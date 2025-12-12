<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HabitSettingsMigrationService;

class MigrateHabitSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habits:migrate-settings {--verify : Only verify integrity without migrating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate habit categories and difficulties from enums to database tables';

    /**
     * Execute the console command.
     */
    public function handle(HabitSettingsMigrationService $migrationService)
    {
        if ($this->option('verify')) {
            $this->info('Verifying data integrity...');
            $results = $migrationService->verifyIntegrity();
            
            $this->newLine();
            $this->info('=== Verification Results ===');
            $this->table(
                ['Metric', 'Count'],
                collect($results['stats'])->map(fn($value, $key) => [$key, $value])->toArray()
            );
            
            if ($results['valid']) {
                $this->info('✓ ' . $results['message']);
            } else {
                $this->error('✗ ' . $results['message']);
                if (!empty($results['issues'])) {
                    $this->newLine();
                    $this->error('Issues found:');
                    foreach ($results['issues'] as $issue) {
                        $this->line('  - ' . $issue);
                    }
                }
            }
            
            return $results['valid'] ? 0 : 1;
        }

        $this->info('Starting migration of habit settings...');
        $this->newLine();

        // Migrate categories
        $this->info('Migrating categories...');
        $categoryResults = $migrationService->migrateCategories();
        
        if ($categoryResults['success']) {
            $this->info("✓ Categories migrated: {$categoryResults['migrated']} / {$categoryResults['total']}");
            if ($categoryResults['skipped'] > 0) {
                $this->warn("  Skipped: {$categoryResults['skipped']}");
            }
        } else {
            $this->error('✗ Category migration failed');
        }

        if (!empty($categoryResults['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($categoryResults['errors'] as $error) {
                $this->line('  - ' . $error);
            }
        }

        $this->newLine();

        // Migrate difficulties
        $this->info('Migrating difficulties...');
        $difficultyResults = $migrationService->migrateDifficulties();
        
        if ($difficultyResults['success']) {
            $this->info("✓ Difficulties migrated: {$difficultyResults['migrated']} / {$difficultyResults['total']}");
            if ($difficultyResults['skipped'] > 0) {
                $this->warn("  Skipped: {$difficultyResults['skipped']}");
            }
        } else {
            $this->error('✗ Difficulty migration failed');
        }

        if (!empty($difficultyResults['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($difficultyResults['errors'] as $error) {
                $this->line('  - ' . $error);
            }
        }

        $this->newLine();

        // Verify integrity
        $this->info('Verifying data integrity...');
        $verifyResults = $migrationService->verifyIntegrity();
        
        if ($verifyResults['valid']) {
            $this->info('✓ ' . $verifyResults['message']);
        } else {
            $this->error('✗ ' . $verifyResults['message']);
            if (!empty($verifyResults['issues'])) {
                foreach ($verifyResults['issues'] as $issue) {
                    $this->line('  - ' . $issue);
                }
            }
        }

        $this->newLine();
        $this->info('Migration complete!');

        return ($categoryResults['success'] && $difficultyResults['success'] && $verifyResults['valid']) ? 0 : 1;
    }
}
