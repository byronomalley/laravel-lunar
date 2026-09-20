<?php

use App\Services\UserService;
use Lunar\Admin\Models\Staff;

describe('UserService', function () {
    beforeEach(function () {
        $this->service = new UserService;
    });

    it('registers a staff member', function () {
        $firstName = 'john';
        $lastName = 'doe';
        $email = 'john.doe@gmail.com';
        $password = 'password';
        $user1 = $this->service->registerStaff([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'admin' => false,
        ]);

        $firstName2 = 'john';
        $lastName2 = 'doe2';
        $email2 = 'john.doe2@gmail.com';
        $password2 = 'password';
        $user2 = $this->service->registerStaff([
            'first_name' => $firstName2,
            'last_name' => $lastName2,
            'email' => $email2,
            'password' => $password2,
            'password_confirmation' => $password2,
            'admin' => true,
        ]);
        expect($user1)->toBeInstanceOf(Staff::class)
            ->and($user1->first_name)->toBe($firstName)
            ->and($user1->last_name)->toBe($lastName)
            ->and($user1->email)->toBe($email)
            ->and($user1->admin)->toBeFalse()

            ->and($user2->first_name)->toBe($firstName2)
            ->and($user2->last_name)->toBe($lastName2)
            ->and($user2->email)->toBe($email2)
            ->and($user2->admin)->toBeTrue();
    });
});
