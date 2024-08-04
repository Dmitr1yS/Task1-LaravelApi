<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Employee;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_employee()
    {
        $response = $this->postJson('/api/employees', [
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'email']);

        $this->assertDatabaseHas('employees', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_create_employee_with_existing_email()
    {
        Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/employees', [
            'email' => 'test@example.com',
            'password' => 'anothersecret',
        ]);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonValidationErrors('email');
    }

    public function test_create_employee_without_email()
    {
        $response = $this->postJson('/api/employees', [
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_create_employee_without_password()
    {
        $response = $this->postJson('/api/employees', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_create_employee_with_invalid_email()
    {
        $response = $this->postJson('/api/employees', [
            'email' => 'invalid-email',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_create_employee_with_short_password()
    {
        $response = $this->postJson('/api/employees', [
            'email' => 'test@example.com',
            'password' => '123',  
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

}
