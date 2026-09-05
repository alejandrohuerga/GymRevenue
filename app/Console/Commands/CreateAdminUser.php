<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:user {email} {password} {--name=Administrador}';

    protected $description = 'Crea o actualiza un usuario administrador para el panel privado.';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email')));
        $password = $this->argument('password');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Introduce un email válido.');

            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        $name = trim($this->option('name'));

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name !== '' ? $name : 'Administrador',
                'password' => $password,
                'is_admin' => true,
            ],
        );

        $this->info("Usuario administrador listo: {$user->email}");

        return self::SUCCESS;
    }
}
