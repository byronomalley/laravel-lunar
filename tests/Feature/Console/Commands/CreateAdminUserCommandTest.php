<?php

use Lunar\Admin\Models\Staff;

test('it creates a super admin user', function () {
    $firstName = 'admin';
    $lastName = 'user';
    $email = 'admin@example.com';
    $password = 'password';
    $this->artisan('app:create-admin-user')
        ->expectsQuestion('What is the admin\'s name?', $firstName)
        ->expectsQuestion('What is the admin\'s last name?', $lastName)
        ->expectsQuestion('What is the admin\'s email?', $email)
        ->expectsQuestion('What is the admin\'s password?', $password)
        ->expectsQuestion('Please confirm the password', $password)
        ->expectsOutput("Admin user created: $email")
        ->assertExitCode(0);

    $user = Staff::where('email', $email)->first();

    expect($user)->not->toBeNull()
        ->and($user->first_name)->toBe($firstName)
        ->and($user->last_name)->toBe($lastName)
        ->and($user->email)->toBe($email)
        ->and($user->admin)->toBeTrue();
});
