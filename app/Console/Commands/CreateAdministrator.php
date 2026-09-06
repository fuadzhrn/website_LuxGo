<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdministrator extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create a LUX&GO administrator account';

    /**
     * Credentials are typed in at run time and hashed before storage, so no
     * password ever lives in the repository or in a migration.
     */
    public function handle(): int
    {
        $name = text(label: 'Name', required: true);
        $email = text(label: 'Email', required: true);
        $plainPassword = password(label: 'Password', required: true);
        $confirmation = password(label: 'Confirm password', required: true);

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
            'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($plainPassword);
        $user->role = User::ROLE_ADMINISTRATOR;
        $user->save();

        $this->info("Administrator created: {$user->email}");

        return self::SUCCESS;
    }
}
