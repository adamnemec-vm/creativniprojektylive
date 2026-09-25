<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Vytvoří účet administrátora';

    public function handle(): int
    {
        $data = [
            'name' => text('Jméno', required: true),
            'username' => text('Uživatelské jméno', required: true),
            'email' => text('E-mail', required: true),
            'password' => password('Heslo', required: true),
        ];

        $validator = Validator::make($data, [
            'username' => ['unique:users,username'],
            'email' => ['email', 'unique:users,email'],
            'password' => [Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create($data + ['role' => User::ROLE_ADMIN]);

        $this->info("Administrátor {$data['username']} byl vytvořen.");

        return self::SUCCESS;
    }
}
