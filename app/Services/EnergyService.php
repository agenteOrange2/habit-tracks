<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class EnergyService
{
    private const MAX_ENERGY = 100;
    private const ENERGY_PER_POMODORO = -10;
    private const ENERGY_PER_HOUR_REST = 5;
    private const ENERGY_PER_REWARD_CLAIMED = -15;
    
    public function updateEnergy(User $user): void
    {
        if (!$user->last_energy_update) {
            $user->last_energy_update = now();
            $user->save();
            return;
        }
        
        $hoursSinceUpdate = Carbon::parse($user->last_energy_update)
            ->diffInHours(now());
        
        if ($hoursSinceUpdate > 0) {
            $energyGain = min(
                $hoursSinceUpdate * self::ENERGY_PER_HOUR_REST,
                self::MAX_ENERGY - $user->energy_level
            );
            
            $user->energy_level = min($user->energy_level + $energyGain, self::MAX_ENERGY);
            $user->last_energy_update = now();
            $user->save();
        }
    }
    
    public function consumeEnergy(User $user, int $amount): bool
    {
        $this->updateEnergy($user);
        
        if ($user->energy_level < $amount) {
            return false;
        }
        
        $user->energy_level -= $amount;
        $user->save();
        
        return true;
    }
    
    public function consumeForPomodoro(User $user): bool
    {
        return $this->consumeEnergy($user, abs(self::ENERGY_PER_POMODORO));
    }
    
    public function consumeForReward(User $user): bool
    {
        return $this->consumeEnergy($user, abs(self::ENERGY_PER_REWARD_CLAIMED));
    }
    
    public function getEnergyStatus(User $user): array
    {
        $this->updateEnergy($user);
        
        return [
            'current' => $user->energy_level,
            'max' => self::MAX_ENERGY,
            'percentage' => ($user->energy_level / self::MAX_ENERGY) * 100,
            'status' => match(true) {
                $user->energy_level >= 80 => 'high',
                $user->energy_level >= 50 => 'medium',
                $user->energy_level >= 20 => 'low',
                default => 'critical',
            },
        ];
    }
}