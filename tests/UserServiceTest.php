<?php

use Tests\TestCase;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserServiceTest extends TestCase
{
    
use DatabaseTransactions;

protected $userService;

public function setUp(): void
{
    parent::setUp();
    // You might need to adjust the namespace and path based on your project structure
    $this->userService = new UserRepository(new User);
}

public function it_creates_or_updates_user()
{
    $userData = [
        'name'=> 'John',
        'role' => env('CUSTOMER_ROLE_ID'),
        'email' => 'test@gmail.com',
        'mobile' => '1234567898',
        'phone' => '3456789876',
        'dob_or_orgid' => '31/04/1995',
        'password' => Hash::make('admin123')
    ];

    // Add assertions based on your expectations
    $this->userService->createOrUpdate(null, $userData);

    $this->assertDatabaseHas('users', [
        'role' => env('CUSTOMER_ROLE_ID'),
    ]);
}

}