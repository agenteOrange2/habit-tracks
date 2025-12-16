<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MakeFirstUserAdmin extends Seeder
{
    public function run(): void
    {
        // Buscar el usuario por email (el que está en DatabaseSeeder)
        $user = User::where('email', 'maubr170295@gmail.com')->first();
        
        if ($user) {
            $user->update(['is_admin' => true]);
            $this->command->info("✅ Usuario '{$user->name}' ({$user->email}) es ahora administrador.");
        } else {
            // Si no existe ese usuario, hacer admin al primero que encuentre
            $firstUser = User::orderBy('id')->first();
            
            if ($firstUser) {
                $firstUser->update(['is_admin' => true]);
                $this->command->info("✅ Usuario '{$firstUser->name}' ({$firstUser->email}) es ahora administrador.");
            } else {
                $this->command->warn('⚠️  No hay usuarios en la base de datos. Ejecuta primero: php artisan db:seed');
            }
        }
    }
}
