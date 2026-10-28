<?php

namespace App\Services;

use App\Exceptions\CustomerRegistrationException;
use App\Exceptions\StaffRegistrationException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Customer;
use Nette\Schema\ValidationException;
use Throwable;

class UserService
{
    /**
     * Register a customer
     *
     * @throws ValidationException
     * @throws CustomerRegistrationException
     */
    public function registerCustomer(array $args): User
    {
        $v = Validator::make($args, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($v->fails()) {
            $errMsg = implode(', ', $v->errors()->all());
            throw new ValidationException('Validation error: '.$errMsg);
        }

        $vData = $v->validate();

        try {
            return DB::transaction(function () use ($vData) {
                $user = User::create([
                    'name' => $vData['first_name'].' '.$vData['last_name'],
                    'email' => $vData['email'],
                    'password' => Hash::make($vData['password']),
                ]);

                $customer = Customer::create([
                    'first_name' => $vData['first_name'],
                    'last_name' => $vData['last_name'],
                ]);

                $customer->users()->attach($user);

                return $user;
            });

        } catch (Throwable $e) {
            throw new CustomerRegistrationException($e);
        }
    }

    /**
     * Register a staff member
     *
     * @throws ValidationException
     * @throws StaffRegistrationException
     */
    public function registerStaff(array $args): Staff
    {
        $v = Validator::make($args, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin' => 'required|boolean',
        ]);

        if ($v->fails()) {
            $errMsg = implode(', ', $v->errors()->all());
            throw new ValidationException('Validation error: '.$errMsg);
        }

        $vData = $v->validate();

        try {
            return Staff::create([
                'first_name' => $vData['first_name'],
                'last_name' => $vData['last_name'],
                'email' => $vData['email'],
                'password' => Hash::make($vData['password']),
                'admin' => $vData['admin'], // Gives full global administrative overrides
            ]);
        } catch (Throwable $e) {
            throw new StaffRegistrationException($e);
        }
    }
}
