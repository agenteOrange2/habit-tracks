<?php

namespace App\Console\Commands;

use App\Models\MilestoneBadge;
use App\Models\User;
use Illuminate\Console\Command;

class CreateDefaultMilestoneBadgesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'badges:create-defaults {--user= : Create badges for a specific user ID}';

    /**
     * The console command description.
     */
    protected $description = 'Create default milestone badges for users who don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return self::FAILURE;
            }
            
            $this->createBadgesForUser($user);
            return self::SUCCESS;
        }
        
        // Get all users without milestone badges
        $usersWithoutBadges = User::whereDoesntHave('milestoneBadges')->get();
        
        if ($usersWithoutBadges->isEmpty()) {
            $this->info('All users already have milestone badges.');
            return self::SUCCESS;
        }
        
        $this->info("Found {$usersWithoutBadges->count()} users without milestone badges.");
        
        $bar = $this->output->createProgressBar($usersWithoutBadges->count());
        $bar->start();
        
        foreach ($usersWithoutBadges as $user) {
            $this->createBadgesForUser($user, false);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Default milestone badges created successfully!');
        
        return self::SUCCESS;
    }
    
    private function createBadgesForUser(User $user, bool $verbose = true): void
    {
        if ($user->milestoneBadges()->count() > 0) {
            if ($verbose) {
                $this->warn("User {$user->name} already has milestone badges.");
            }
            return;
        }
        
        MilestoneBadge::createDefaultsForUser($user);
        
        if ($verbose) {
            $this->info("Created default badges for user: {$user->name}");
        }
    }
}
