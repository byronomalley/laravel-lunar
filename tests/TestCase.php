<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Lunar\Database\Seeders\TestingSeeder;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    protected bool $seed = true;
    protected string $seeder = TestingSeeder::class;
}
