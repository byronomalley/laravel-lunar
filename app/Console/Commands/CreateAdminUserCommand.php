<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserService;
use Lunar\Admin\Models\Staff;
use Exception;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a admin user';

    /**
     * Execute the console command.
     */
    public function handle(UserService $userService): void
    {
        $name                 = trim($this->ask('What is the admin\'s name?'));
        $lastName             = trim($this->ask('What is the admin\'s last name?'));
        $email                = trim($this->ask('What is the admin\'s email?'));
        $password             = trim($this->secret('What is the admin\'s password?'));
        $passwordConfirmation = trim($this->secret('Please confirm the password'));

        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match.');
            return;
        }

        try {
            $user = $userService->registerStaff([
                'first_name'            => $name,
                'last_name'             => $lastName,
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
                'admin'                 => true,
            ]);
            $this->info("Admin user created: {$user->email}");
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
