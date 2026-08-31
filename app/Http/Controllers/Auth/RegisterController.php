<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class RegisterController extends Controller
{
    public function __construct(public UserService $userService) {}

    public function store(Request $request)
    {
        try {
            $customer = $this->userService->registerCustomer($request->toArray());
            Auth::login($customer);
            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
}
