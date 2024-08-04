<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\Transaction;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_transaction()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/transactions', [
            'employee_id' => $employee->id,
            'hours' => 8,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'employee_id', 'hours']);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->id,
            'hours' => 8,
            'paid' => false,
        ]);
    }

    public function test_create_transaction_with_nonexistent_employee()
    {
        $response = $this->postJson('/api/transactions', [
            'employee_id' => 999, // Неверный ID
            'hours' => 8,
        ]);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonValidationErrors('employee_id');
    }

    public function test_get_salaries()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        Transaction::create([
            'employee_id' => $employee->id,
            'hours' => 8,
        ]);

        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJson([
                [
                    'employee_id' => $employee->id,
                    'total' => 80.00, // 10 за час * 8 часов
                ],
            ]);
    }

    public function test_pay_salaries()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        Transaction::create([
            'employee_id' => $employee->id,
            'hours' => 8,
        ]);

        $response = $this->postJson('/api/pay-salaries');

        $response->assertStatus(200)
            ->assertJson(['message' => 'All unpaid transactions have been marked as paid.']);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->id,
            'paid' => true,
        ]);
    }

    public function test_create_transaction_without_hours()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/transactions', [
            'employee_id' => $employee->id,
            // 'hours' => 8, // это поле пропущено
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('hours');
    }

    public function test_create_transaction_with_negative_hours()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/transactions', [
            'employee_id' => $employee->id,
            'hours' => -5, // Неверное значение
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('hours');
    }

    public function test_create_multiple_transactions()
    {
        $employee = Employee::create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $responses = [];
        for ($i = 1; $i <= 3; $i++) {
            $responses[] = $this->postJson('/api/transactions', [
                'employee_id' => $employee->id,
                'hours' => $i * 5,
            ]);
        }

        foreach ($responses as $response) {
            $response->assertStatus(201);
        }

        $this->assertCount(3, Transaction::all());
    }
}
